<?php

namespace App\Model\Responses;

use Nette;
use Nette\Application\IResponse;

class CancelAuthorizationResponse implements IResponse
{
    private $xml;

    private $headers;

    public function __construct(string $xml, array $headers)
    {
        $this->xml = $xml;
        $this->headers = $headers;
    }

    function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse): void
    {
        foreach ($this->headers as $name => $value) {
            $httpResponse->addHeader($name, $value);
        }
        $httpResponse->setContentType('text/xml');

        echo $this->xml;
    }
}
