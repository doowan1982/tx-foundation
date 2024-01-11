<?php
namespace Tesoon\Foundation\Request;

use Tesoon\Foundation\Encoder;
use Tesoon\Foundation\Exceptions\FoundationException;
use Tesoon\Foundation\Exceptions\RequestException;
use Tesoon\Foundation\Header;
use Tesoon\Foundation\Models\Admin;
use Tesoon\Foundation\Models\Lists;
use Tesoon\Foundation\Request\Transports\Admin as AdminTransport;
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
        return $this->initTransport(new AdminTransport($this->encoder))
            ->setId($id)
            ->send()
            ->getData();
    }


    /**
     * @param Transport $transport
     * @return Transport
     * @throws FoundationException
     */
    private function initTransport(Transport $transport): Transport{
        if($this->encoder === null){
            throw new FoundationException('请指定Encoder实例', 50000);
        }
        if($this->setting === null){
            $this->setting = new SignatureSetting();
        }
        return $transport->setProtocol($this->protocol)
                ->setHost($this->host)
                ->setSignatureSetting($this->setting)
                ->setParameter(Header::create('accept=application/json'));
    }

}