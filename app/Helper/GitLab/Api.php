<?php


namespace App\Helper\GitLab;


use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class Api
{

	protected Client $client;
	protected $defaultParams = [];

	/**
	 * ImportProjects constructor.
	 */
	public function __construct(string $baseUrl, string $accessToken)
	{
		$this->client = new Client([
			'base_uri' => $baseUrl,
		]);
		$this->defaultParams[RequestOptions::HEADERS]['Authorization'] = 'Bearer '.$accessToken;
	}

	protected function sendRequest(string $method, string $url, array $additionalParams): ResponseInterface
	{
		return $this->client->request($method, $url, array_merge($this->defaultParams, $additionalParams));
	}


}