<?php
namespace Tesoon\Tests;

use Tesoon\Foundation\Application;
use Tesoon\Foundation\Authentication;
use Tesoon\Foundation\Signature as FoundationSignature;

class Signature implements FoundationSignature{

    public function encrypt(Application $application, int $timestamp, array $data): string{
        return "test";
    }

    public function check(Application $application, Authentication $authentication): bool{
        return true;
    }


}