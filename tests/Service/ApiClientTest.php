<?php

declare(strict_types=1);

namespace Service;

use App\Service\ApiClient;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiClientTest extends TestCase
{
    public function testFetchData(): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);

        $httpClientMock
            ->method('request')
            ->willReturn($responseMock);

        $responseMock
            ->method('getStatusCode')
            ->willReturn(200);

        $responseMock
            ->method('toArray')
            ->willReturn(['name' => 'test', 'value' => 123]);

        $apiClient = new ApiClient($httpClientMock);
        $data = $apiClient->fetchData('http://example.com/api');

        $this->assertEquals(['name' => 'test', 'value' => 123], $data);
    }
}
