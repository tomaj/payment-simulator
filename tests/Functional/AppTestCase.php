<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Bootstrap;
use App\Tests\TestCase;
use Nette\Application\Application;
use Nette\Bootstrap\Configurator;
use Nette\Http\Request;
use Nette\Http\UrlScript;

/**
 * Runs the whole Nette application in-process (routing, presenters, templates, error presenter)
 * for one HTTP request, in production mode.
 */
abstract class AppTestCase extends TestCase
{
    protected const string HOST = 'http://simulator.test';

    /**
     * @param array<mixed> $query
     * @param array<mixed> $post
     * @param array<string, string> $headers
     * @param string $host scheme, host and port the request is sent to
     */
    protected function request(string $method, string $path, array $query = [], array $post = [], array $headers = [], ?string $body = null, string $host = self::HOST): HttpResult
    {
        $configurator = new Configurator();
        $configurator->setDebugMode(false);
        Bootstrap::configure($configurator);
        $configurator->addConfig(__DIR__ . '/config.neon');
        $container = $configurator->createContainer();

        $url = new UrlScript($host . $path . ($query !== [] ? '?' . http_build_query($query) : ''), '/');
        $httpRequest = new Request($url, $post, [], [], $headers, $method, '127.0.0.1', null, $body === null ? null : static fn(): string => $body);
        $container->removeService('http.request');
        $container->addService('http.request', $httpRequest);
        $httpResponse = $container->getByType(RecordingResponse::class);

        $application = $container->getByType(Application::class);
        ob_start();
        try {
            $application->run();
        } finally {
            $output = (string) ob_get_clean();
        }

        return new HttpResult($httpResponse->code, $httpResponse->headers, $output);
    }

    /**
     * @param array<mixed> $query
     */
    protected function get(string $path, array $query = []): HttpResult
    {
        return $this->request('GET', $path, $query);
    }
}
