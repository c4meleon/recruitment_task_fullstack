<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

class ApiClient implements ApiClientInterface
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @throws \Exception
     */
    public function fetchData(string $url): array
    {
        try {
            $response = $this->httpClient->request('GET', $url);
            $statusCode = $response->getStatusCode();

            if ($statusCode === 404) {
                throw new \Exception("Data not available for the selected date.");
            }

            if ($statusCode !== 200) {
                throw new \Exception("API request failed with status code: $statusCode");
            }

            return $response->toArray();
        } catch (TransportExceptionInterface | ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface $e) {
            throw new \Exception("API request failed: " . $e->getMessage());
        } catch (DecodingExceptionInterface $e) {
            throw new \Exception("Failed to decode API response: " . $e->getMessage());
        }
    }
}
