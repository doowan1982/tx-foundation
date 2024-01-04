<?php
namespace Tesoon\Foundation\Models;

class Employee extends Data{

    public $id;

    public $realName;

    public $jobNumber;

    public $avatar;

    public $job;

    public $contact;

    public $superiorId;

    public $platform = [];

    public $organization;

    public function __construct()
    {
        $this->organization = new Lists(Organization::class);
    }

}