<?php


namespace Ehua\Rest\Fulu;


class Common
{
    public function __construct($debug=true)
    {
        if($debug){
            define('HOST','https://openapi.fulu.com/api/getway');
        }else{
            define('HOST','https://pre-openapi.fulu.com/api/getway');
        }
    }
}