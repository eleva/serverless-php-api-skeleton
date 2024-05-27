<?php

namespace App;

use Bref\Event\Handler;
use Bref\Context\Context;

require 'vendor/autoload.php';

class HelloHandler implements Handler
{
    /**
     * @param $event
     * @param Context|null $context
     * @return array
     */
    public function handle($event, ?Context $context = null): array
    {
        return [
            "statusCode"=>200,
            "headers"=>[
                'Access-Control-Allow-Origin'=> '*',
                'Access-Control-Allow-Credentials'=> true,
                'Access-Control-Allow-Headers'=> '*',
            ],
            "body"=>json_encode([
                "message" =>'Bref! Your function executed successfully!',
                "context" => $context,
                "input" => $event
            ])
        ];

    }
}

return new HelloHandler();
