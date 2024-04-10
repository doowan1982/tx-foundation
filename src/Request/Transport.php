<?php


namespace Tesoon\Foundation\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Tesoon\Foundation\Constant;
use Tesoon\Foundation\Context;
use Tesoon\Foundation\EmptyApplication;
use Tesoon\Foundation\Encoder;
use Tesoon\Foundation\Exceptions\DataException;
use Tesoon\Foundation\Exceptions\RequestException;
use Tesoon\Foundation\GeneralObject;
use Tesoon\Foundation\Header;
use Tesoon\Foundation\Parameter;
use Tesoon\Foundation\Response\ResponseBody;
use Tesoon\Foundation\SignatureSetting;

class Transport extends GeneralObject
{
    const AUTHENTICATION = 'authentication';

    /**
     * @var string
     */
    private $protocol = 'https';

    /**
     * @var string
     */
    private $host = '';

    /**
     * @var string
     */
    private $method = Constant::POST_REQUEST;

    /**
     * @var string
     */
    private $uri = '';

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var Encoder
     */
    private $encoder;

    private $fulfilled;

    private $rejected;

    private $setting;

    private $body;

    public function __construct(Encoder $encoder = null)
    {
        $this->encoder = $encoder;
    }

    public function setProtocol(string $protocol): Transport{
        $this->protocol = $protocol;
        return $this;
    }

    public function setHost(string $host): Transport{
        $this->host = $host;
        return $this;
    }

    public function setUri(string $uri): Transport{
        $this->uri = $uri;
        return $this;
    }

    public function setMethod(string $method): Transport{
        $this->method = $method;
        return $this;
    }

    public function setParameter(Parameter $parameter): Transport{
        $this->parameters[] = $parameter;
        return $this;
    }

    public function setParameters(array $parameters): Transport{
        foreach($parameters as $parameter){
            $this->setParameter($parameter);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getParameters(): array{
        return $this->parameters;
    }

    public function setSignatureSetting(SignatureSetting $setting): Transport{
        $this->setting = $setting;
        return $this;
    }

    /**
     * 设置异步promise回调
     * @param callable $fulfilled
     * @param callable|null $rejected
     * @return $this
     */
    public function setAsyncRequest(callable $fulfilled, callable $rejected = null): Transport{
        $this->fulfilled = $fulfilled;
        $this->rejected = $rejected;
        return $this;
    }

    /**
     * @param ResponseBody $body
     * @return Transport
     */
    public function setResponseBody(ResponseBody $body): Transport{
        $this->body = $body;
        return $this;
    }

    /**
     * @param array $config
     * @return ResponseBody
     * @throws RequestException
     */
    public function send(array $config = []): ResponseBody{
        return $this->lastSend($config, $this->encoder);
    }

    /**
     * @param array $config
     * @param Encoder $encoder
     * @return ResponseBody
     * @throws RequestException
     */
    public function lastSend(array $config = [], Encoder $encoder = null){
        $config = $this->assembleConfig($config, $encoder);
        $client = new Client([
            'base_uri' => "{$this->protocol}://{$this->host}/",
        ] + $config);

        if($this->fulfilled || $this->rejected){
            $client->requestAsync($this->method, $this->uri, $config)
                ->then(function(Response $response){
                    return $this->response($response);
                }, function($reason){
                    Transport::logger()->warning("异步请求失败：{$reason}");
                    return $reason;
                })
                ->then($this->fulfilled, $this->rejected)
                ->wait();
            return new ResponseBody(); //异步任务将始终返回true
        }

        try{
            return $this->response($client->request($this->method, $this->uri, $config));
        }catch(GuzzleException|DataException $e){
            throw new RequestException($this, $e->getCode(), $e);
        }
    }

    /**
     * @param array $config
     * @param Encoder $encoder;
     * @return array
     * @throws RequestException
     */
    protected function assembleConfig($config = [], Encoder $encoder = null){
        $json = $headers = $query = [];
        foreach($this->parameters as $parameter){
            if($parameter instanceof Header) {
                $headers[$parameter->getName()] = $parameter->getValue();
            }else if($parameter instanceof QueryParameter){
                $query[$parameter->getName()] = $parameter->getValue();
            }else if($parameter instanceof Parameter){
                $json[$parameter->getName()] = $parameter->getValue();
            }
        }

        if($encoder && !($encoder->getApplication() instanceof EmptyApplication)){
            if($this->setting === null){
                $this->setting = new SignatureSetting();
            }
            $this->setting->setClaim(Constant::APPLICATION_NAME, $encoder->getApplication()->id);
            $headers[static::AUTHENTICATION] = $encoder->encrypt($json + $query, $this->setting);
        }
        static::logger()->notice('创建请求体', [
            'application_id' => $encoder ? $encoder->getApplication()->id : 0,
            'url' => "{$this->protocol}://{$this->host}/{$this->uri}",
            'header' => $headers,
            'json' => $json,
            'query' => $query,
        ]);

        return [
            'verify' => $this->protocol === 'https' && Context::instance()->isProd(),
            'headers' => $headers,
            'json' => $json,
            'query' => $query,
        ] + $config;
    }

    protected function response(Response $response): ResponseBody{
        $responseBody = $this->getResponseBody();
        if($response->getStatusCode() === 200){
            $data = $response->getBody()->getContents();
            static::logger()->notice('响应信息', [
                'header' => $response->getHeaders(),
                'data' => $data
            ]);
            $content = json_decode($data, true);
            if(is_array($content) && $content['status'] === Constant::RESPONSE_CODE_OK){
                $responseBody->setCode($content['status'], $content['message']);
                $responseBody->setResponseParameters($content['data'] ?? []);
            }else{
                $responseBody->setCode($content['status'] ?? 0, $content['message'] ?? $data);
            }
        }else{ //http错误码处理
            $responseBody->setCode($response->getStatusCode(), $response->getReasonPhrase());
        }
        return $responseBody;
    }

    protected function getResponseBody(): ResponseBody{
        if($this->body != null){
            return $this->body;
        }
        return new ResponseBody();
    }

}