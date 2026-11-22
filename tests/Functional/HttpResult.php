<?php

declare(strict_types=1);

namespace App\Tests\Functional;

final readonly class HttpResult
{
    /**
     * @param array<string, list<string>> $headers lowercase names
     */
    public function __construct(
        public int $code,
        public array $headers,
        public string $body,
    ) {}

    public function header(string $name): ?string
    {
        return $this->headers[strtolower($name)][0] ?? null;
    }

    /**
     * The decoded href of the link with the given text ("continue ok", ...), or null.
     */
    public function link(string $text): ?string
    {
        if (preg_match('~<a href="([^"]*)"[^>]*>' . preg_quote($text, '~') . '</a>~', $this->body, $m) !== 1) {
            return null;
        }

        return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5);
    }

    public function computedSign(): ?string
    {
        return preg_match('~Computed Sign: <code>([^<]*)</code>~', $this->body, $m) === 1 ? html_entity_decode($m[1]) : null;
    }
}
