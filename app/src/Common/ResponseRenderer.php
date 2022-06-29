<?php


namespace App\Common;


use Symfony\Component\HttpFoundation\Response;

class ResponseRenderer
{
    public function response(array $content): Response
    {
        $response = new Response();
        $response->setContent(json_encode(
            $content
        ));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}