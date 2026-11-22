<?php

declare(strict_types=1);

namespace App\Model\Responses;

use Nette;

class CancelAuthorizationResponse implements Nette\Application\Response
{
    private $xml;

    private $headers;

    public function __construct(string $xml, array $headers)
    {
        $this->xml = $xml;
        $this->headers = $headers;
    }

    public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse): void
    {
        foreach ($this->headers as $name => $value) {
            $httpResponse->addHeader($name, $value);
        }
        $httpResponse->setContentType('text/xml');

        echo $this->xml;
    }
}
