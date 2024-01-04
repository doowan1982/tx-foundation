<?php
namespace Tesoon\Tests;

use PHPUnit\Framework\TestCase;
use Tesoon\Foundation\Models\Categories\Subject;
use Tesoon\Foundation\Models\Data;
use Tesoon\Foundation\Models\Categories\Job;
use Tesoon\Foundation\Models\Lists;

/**
 * @group data
 */
class DataTest extends TestCase{


    public function testSetObjectValues(){
        $subject = new Subject();
        $data = [
            "subject_name" => "数学",
            "unique_name" => "XK00002",
            "subject_id" => 88
        ];
        Data::setObjectValues($subject, $data);
        $this->assertTrue($subject->id == $data['subject_id'], '设置subject_id失败');
        $this->assertTrue($subject->name == $data['subject_name'], '设置subject_name失败');
        $this->assertTrue($subject->uniqueName == $data['unique_name'], '设置unique_name失败');
    }
    
    public function testFromNormal(){
        $class = $this->extendsDataClass();
        $class = $class::create([
            'id' => 1,
            'name' => 'test',
            'this_is_name' => [
                'a' => 'bb',
                'b' => 'cc',
            ],
            'job' => [
                'id' => 123,
                'name' => 'CTO',
                'sequence' => 1
            ]
        ]);
        $this->assertTrue($class->id == 1, 'id不正确');
        $this->assertTrue($class->name == 'test', 'name不正确');
        $this->assertTrue(is_array($class->thisIsName) && $class->thisIsName['a'] == 'bb', 'thisIsName不正确');
        $this->assertNotNull($class->job, 'Job初始化未创建成功');
        $this->assertTrue($class->job->id == 123, 'JobID不正确');
    }

    public function testList(){
        $class = $this->listDataClass();
        $data = [
            [
                "subject_name" => "语文",
                "unique_name" => "XK00001",
                "subject_id" => 87
            ],
            [
                "subject_name" => "数学",
                "unique_name" => "XK00002",
                "subject_id" => 88
            ]
        ];
        $class->setValue('list', $data);
        foreach($class->list as $k=>$value){
            $this->assertEquals($value->id == $data[$k]['subject_id'], "设置[{$k}]ID失败");
        }
    }

    /**
     * @group dataSetValue
     */
    public function testSetValue(){
        $class = $this->includeArrayExtendsDataClass();
        $class->setValue('job', [
            'id' => 123,
            'name' => 'CTO',
            'sequence' => 1
        ]);
        $this->assertNotNull($class->job, '设置Job失败');
        $this->assertEquals($class->job->id === 123, "设置Job【ID】失败【{$class->job->id}】");
        $this->assertEquals($class->job->name === 'CTO', "设置Job【ID】失败【{$class->job->name}】");
    }

    public function testFromArray(){
        $class = $this->includeArrayExtendsDataClass();
        $class = $class::create([
            'job' => [
                'id' => 123,
                'name' => 'CTO',
                'sequence' => 1
            ]
        ]);
        $this->assertNotNull($class->job, 'Job不正确');
        $this->assertNotNull($class->job->id === 123, 'JobID不正确');
    }

    private function listDataClass(): Data{
        return new class extends Data{
            public $list;
            public function __construct()
            {
                $this->list = new Lists(Subject::class);
            }
        };
    }

    private function extendsDataClass(): Data{
        return new class extends Data{
            public $id;
            public $name;
            public $thisIsName;
            public $job;
            public function __construct()
            {
                $this->job = new Job();
            }

        };
    }

    private function includeArrayExtendsDataClass(): Data{
        return new class extends Data{
            public $job;
            public function __construct()
            {
                $this->job = new Job();
            }
        };

    }

}