<?php

namespace App\Services;

use App\Enums\ApiTypeEnum;
use App\Repositories\AuthRepository\AuthRepository;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class ApiService
{

    /**
     * variable that contain custom url
     * @var string
     */
    private string $url;

    /**
     * client to run api.
     *
     * @var \IIlluminate\Support\Facades\Http
     */
    private $client;

    public function __construct(
    )
    {
        $baseUrl = $this->getBaseUrl();
        $token = $this->getToken();
        $this->url = $baseUrl;

        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ];

        $this->client = Http::withHeaders($headers)->timeout(60);
    }

    /**
     * Get from api with parameters
     */
    public function get(string $uri, array $params = []):array
    {
        return $this->client->get($this->url . $uri, $params)->json();
    }

    public function getFile(string $uri, array $params = []): ClientResponse{

        return $this->client->get($this->url . $uri, $params);
    }


    /**
     * $file -> request('example_file')
     * $name -> parameter yang akan dikirim, cth: import_excel
     */
    public function postFile(string $uri, UploadedFile $file, $parameterName, array $body = []){
        
        return $this->client->attach(
                $parameterName, file_get_contents($file), $file->getClientOriginalName()
            )->post($this->url.$uri, $body);
    }

    /**
     * Post api
     */
    public function post(string $uri, array $body = []): array
    {
        return $this->client->post($this->url . $uri, $body)->json();
    }

    /**
     * Put api
     */
    public function put(string $uri, array $body = []): array
    {
        $body['_method'] = 'put';
        return $this->client->put($this->url . $uri, $body)->json();
    }

    /**
     * Update api
     */
    public function update(string $uri, array $body = []): array
    {
        return $this->client->update($this->url . $uri, $body)->json();
    }

    /**php 
     * Delete api
     */
    public function delete(string $uri, array $body = []) : array
    {
        return $this->client->delete($this->url . $uri, $body)->json();
    }

    /**
     * base api url used in application
     */
    public function getBaseUrl(): string{

        return config('api.url.main');
    }

    /**
     * Get token dari api
     */
    private function getToken(): ?string{

        $authRepository = app(AuthRepository::class);
        $token = $authRepository->getToken();

        return $token;
    }
}
