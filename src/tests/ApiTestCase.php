<?php

namespace App\tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ApiTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
    }

    public function requestAndGetResponseWithAssert(string $method, string $uri, string|false|null $content = null, bool $expectedSuccess = true): Response
    {
        if ($content === false) {
            throw new \Exception('Failed to encode JSON');
        }
        $this->client->request(method: $method, uri: $uri, content: $content);

        if ($expectedSuccess === true) {
            $this->assertResponseIsSuccessful();
        }

        $response = $this->client->getResponse();
        $content = $response->getContent();

        $this->assertIsNotBool($content);
        $this->assertJson($content);

        return $response;
    }

    /**
     * @return array<string, mixed>
     */
    public static function getContentFromResponse(Response $response): array
    {
        /** @var string $content */
        $content = $response->getContent();

        return json_decode($content, true);
    }
}
