<?php

namespace App;

use Bref\Event\Handler;
use Bref\Context\Context;
use Nyholm\Psr7\Response;

require 'vendor/autoload.php';

class HelloHandler implements Handler
{
    /**
     * @param $event
     * @param Context|null $context
     * @return Response
     */
    public function handle($event, ?Context $context): Response
    {
        $attributes = $event->getAttributes();
        $queryParams = $event->getQueryParams();
        $parsedBody = $event->getParsedBody();

        $message = [
            "message" =>'Bref! Your function executed successfully!',
            "context" => $context,
            "input" => [
                "attributes"=>$attributes,
                "queryParams"=>$queryParams,
                "parsedBody"=>$parsedBody
            ]
        ];

        $status = 200;

        return new Response($status, ["Content-Type"=>"application/json"], json_encode($message));
    }
}

return new HelloHandler();
