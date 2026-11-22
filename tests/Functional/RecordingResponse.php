<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Nette\Http\IResponse;

/**
 * An HTTP response that records the code and headers instead of sending them, so the app can run inside PHPUnit.
 */
final class RecordingResponse implements IResponse
{
    public int $code = self::S200_OK;

    /** @var array<string, list<string>> */
    public array $headers = [];

    #[\Override]
    public function setCode(int $code, ?string $reason = null): static
    {
        $this->code = $code;
        return $this;
    }

    #[\Override]
    public function getCode(): int
    {
        return $this->code;
    }

    #[\Override]
    public function setHeader(string $name, string $value): static
    {
        $this->headers[strtolower($name)] = [$value];
        return $this;
    }

    #[\Override]
    public function addHeader(string $name, string $value): static
    {
        $this->headers[strtolower($name)][] = $value;
        return $this;
    }

    #[\Override]
    public function setContentType(string $type, ?string $charset = null): static
    {
        return $this->setHeader('Content-Type', $type . ($charset !== null ? '; charset=' . $charset : ''));
    }

    #[\Override]
    public function redirect(string $url, int $code = self::S302_Found): void
    {
        $this->setCode($code);
        $this->setHeader('Location', $url);
    }

    #[\Override]
    public function setExpiration(?string $expire): static
    {
        return $this;
    }

    #[\Override]
    public function isSent(): bool
    {
        return false;
    }

    #[\Override]
    public function getHeader(string $header): ?string
    {
        return $this->headers[strtolower($header)][0] ?? null;
    }

    /**
     * @return array<string, string> the first value of each header
     */
    #[\Override]
    public function getHeaders(): array
    {
        return array_map(static fn(array $values): string => $values[0], $this->headers);
    }

    #[\Override]
    public function setCookie(
        string $name,
        string $value,
        string|int|\DateTimeInterface|null $expire,
        ?string $path = null,
        ?string $domain = null,
        ?bool $secure = null,
        ?bool $httpOnly = null,
    ): static {
        return $this;
    }

    #[\Override]
    public function deleteCookie(string $name, ?string $path = null, ?string $domain = null, ?bool $secure = null): void {}
}
