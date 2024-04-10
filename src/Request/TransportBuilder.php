<?php
namespace Tesoon\Foundation\Request;

use Tesoon\Foundation\Constant;
use Tesoon\Foundation\Encoder;
use Tesoon\Foundation\Exceptions\FoundationException;
use Tesoon\Foundation\Exceptions\RequestException;
use Tesoon\Foundation\Header;
use Tesoon\Foundation\Models\Admin;
use Tesoon\Foundation\Models\Lists;
use Tesoon\Foundation\Request\Transports\Abilities;
use Tesoon\Foundation\Request\Transports\Admin as AdminTransport;
use Tesoon\Foundation\Response\ResponseBody;
use Tesoon\Foundation\SignatureSetting;

/**
 * 请求Builder，对服务端相关接口调用
 */
final class TransportBuilder extends \Tesoon\Foundation\GeneralObject
{
    private $encoder;

    private $protocol = 'http';

    private $host = '';

    /**
     * @var SignatureSetting
     */
    private $setting;

    private static $instance;

    public static function create(): TransportBuilder{
        if(static::$instance === null){
            static::$instance = new self();
        }
        return static::$instance;
    }

    public function setSetting(SignatureSetting $setting): TransportBuilder{
        $this->setting = $setting;
        return $this;
    }

    public function setEncoder(Encoder $encoder): TransportBuilder{
        $this->encoder = $encoder;
        return $this;
    }

    public function setRequestContext(string $host, string $protocol = 'https'): TransportBuilder{
        $this->host = $host;
        $this->protocol = $protocol;
        return $this;
    }

    /**
     * 获取管理员信息
     * @param int $id
     * @return Admin
     * @throws RequestException
     * @throws FoundationException
     */
    public function getAdmin(int $id): ?Admin{
        $lists = $this->getAdmins([$id]);
        if($lists instanceof Lists){
            return $lists->get(0) ?? null;
        }
        return null;
    }

    /**
     * 获取多个管理员的信息
     * @param array $id
     * @return Lists
     */
    public function getAdmins(array $id): Lists{
        $transport = new AdminTransport($this->encoder);
        $this->initTransport($transport);
        return $transport->setId($id)
                    ->send()
                    ->getData();
    }

    /**
     * 应用之间启动业务逻辑前的token验证
     * @param string $authentication 校验令牌，为业务发起方的header中authentication值，
     * @param array $body 业务发起方的post+get参数
     * @return bool|ResponseBody 如果验证通过则为true，否则返回ResponseBody
     */
    public function verifySign(string $authentication, array $body){
        $transport = new Abilities();
        $this->initTransport($transport);
        $responseBody = $transport->verifySign($authentication, $body);
        if($responseBody->ok()){
            return true;
        }
        return $responseBody;
    }

    /**
     * 创建一个请求并封装签名信息到名称为：
     * ```php
     *  Transport::AUTHENTICATION
     * ```
     * 的请求头中，注意该请求对与响应数据需遵循一下格式：
     * ```json
     *  {
     *      status: xx,
     *      message: '',
     *      data: []
     *  }
     * ```
     * @param string $url
     * @param array $options query、body、header以及method参数
     * @param ResponseBody $body 指定一个ResponseBody来处理返回值，如果为指定将使用默认的
     * @return ResponseBody
     * @throws RequestException
     */
    public function sendWarpper(string $url, array $options = [], ResponseBody $responseBody = null): ResponseBody{
        $query = $options['query'] ?? [];
        $body = $options['body'] ?? [];
        $headers = $options['headers'] ?? [];
        $method = $options['method'] ?? Constant::GET_REQUEST;
        $transport = new Abilities($this->encoder);
        $info = parse_url($url);
        if(!$info){
            throw new RequestException($transport, 'url不合法');
        }
        $transport->setProtocol($info['scheme'] ?? '')
                    ->setHost($info['host'] ?? '/')
                    ->setUri($info['path'] ?? '')
                    ->setMethod($method);
        if($responseBody != null){
            $transport->setResponseBody($responseBody);
        }
        if(!isset($headers['accept'])){
            $headers['accept'] = 'application/json';
        }
        return $transport->sendWarpper($query, $body, $headers);
    }


    /**
     * @param Transport $transport
     * @return Transport
     * @throws FoundationException
     */
    private function initTransport(Transport $transport): Transport{
        if($this->setting === null){
            $this->setting = new SignatureSetting();
        }
        return $transport->setProtocol($this->protocol)
                ->setHost($this->host)
                ->setSignatureSetting($this->setting)
                ->setParameter(Header::create('accept=application/json'));
    }

}