<?php

namespace Tesoon\Foundation\Request\Transports;

use Tesoon\Foundation\Request\QueryParameter;
use Tesoon\Foundation\Request\Transport;
use Tesoon\Foundation\Response\ResponseBody;

class Admin extends Transport
{
    /**
     * @var array
     */
    private $id = [];
    public function setId($id): Transport{
        if(!is_array($id)){
            $id = [$id];
        }
        $this->id = array_merge($this->id, $id);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function send(array $config = []): ResponseBody
    {
        if(count($this->id) == 1){
            $id = $this->id[0];
            $this->setUri("v2/admin/{$id}/info")
                ->setParameter(QueryParameter::create("adminId={$id}"));
        }else{
            $this->setUri('v2/admin/lists')
                ->setParameter(new QueryParameter('id', $this->id));
        }
        return parent::send($config);
    }

}