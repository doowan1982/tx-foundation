<?php
namespace Tesoon\Foundation\Models;

use Tesoon\Foundation\Models\Categories\Period;
use Tesoon\Foundation\Models\Categories\Subject;

class Admin extends Data{

    public $id;

    public $realName = '';

    public $username = '';

    public $nickname = '';

    public $employeeNo;

    public $sex;
    
    public $phone;
    
    public $email;

    /**
     * 通行证状态
     * @var int
     */
    public $status;

    /**
     * 北森部门id
     * @var int
     */
    public $departmentId;

    public $departmentName;

    public $departmentUniqueName;

    public $subjects;

    public $periods;

    public $employeeId;

    public $employee;

    public function __construct()
    {
        $this->employee = new Employee();
        $this->subjects = new Lists(Subject::class);
        $this->periods = new Lists(Period::class);
    }

    /**
     * @inheritdoc
     */
    public function maps(): array{
        return [
            'admin_id' => 'id'
        ];
    }

}