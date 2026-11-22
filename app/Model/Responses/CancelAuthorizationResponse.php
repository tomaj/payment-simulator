<?php

declare(strict_types=1);

namespace App\Model\Responses;

use Nette\Application\Response;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

/**
 * The XML answer of the CardPay cancel (void) request.
 */
final readonly class CancelAuthorizationResponse implements Response
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        private string $xml,
        private array $headers,
    ) {}

    #[\Override]
    public function send(IRequest $httpRequest, IResponse $httpResponse): void
    {
        foreach ($this->headers as $name => $value) {
            $httpResponse->addHeader($name, $value);
        }
        $httpResponse->setContentType('text/xml');

        echo $this->xml;
    }
}
