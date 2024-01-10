<?php
namespace Tesoon\Foundation\Models;

use Tesoon\Foundation\Exceptions\DataException;
use Tesoon\Foundation\Helper;

class EnterpriseOrganization extends Data{

    public $id;

    public $name;

    public $form;

    /**
     * 上级部门
     * @var Organization
     */
    public $parentId = 0;

    public $parent;

    /**
     * 子部门数组
     * @var Lists
     */
    public $organizations;

    /**
     * 显示顺序
     */
    public $sequence = 0;

    public function __construct()
    {
        $this->organizations = new Lists();
    }

    public function setParent(EnterpriseOrganization $organization){
        $this->parent = $organization;
        $organization->addEnterpriseOrganization($this);
    }

    public function addEnterpriseOrganization(EnterpriseOrganization $organization){
        $this->organizations->add($organization);
    }

    public function getAllEnterpriseOrganization(): Lists{
        $list = new Lists();
        foreach($this->organizations as $organization){
            $list->add($organization);
            foreach($organization->getAllEnterpriseOrganization() as $sub){
                $list->add($sub);
            }
        }
        return $list;
    }

    public function maps(): array{
        return [
            "organization_id" => "id",
            "full_name" => "name",
            "organization_form" => "form",
            "parent_organization_id" => "parentId",
        ];
    }

    /**
     * @inheritDoc
     */
    public static function create($data): Data{
        $list = new Lists();
        foreach($data as $value){
            $obj = new static();
            Helper::setObjectValues($obj, $value);
            $list->add($obj);
        }
        static::organize($list);
        if($list->size() > 1){
            throw  new DataException(static::class, $data, "组织架构仅能包含一个顶级部门");
        }
        return $list->get(0);
    }
    
    private static function organize(Lists $organizations){
        $pendingDelete = [];
        foreach($organizations as $parent){
            $size = $organizations->size();
            for($i=$size; $i >= 0; $i--){
                $organization = $organizations->get($i);
                if($parent->id == $organization->parentId){
                    $organization->setParent($parent);
                    $pendingDelete[] = $i;
                }
            }
        }
        rsort($pendingDelete);
        foreach($pendingDelete as $k){
            $organizations->remove($k);
        }
    }

}