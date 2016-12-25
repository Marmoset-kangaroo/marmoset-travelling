<?php
/**
 * @author Stanislav Kudrytskyi <stanislav.kudrytskyi@dukascopy.com>
 * Date: 8/31/16
 * Time: 12:15 PM
 */

namespace App\Core;

use GuzzleHttp;

class RequestSender
{
	protected $proxy = [];
	protected $headers = [];
	protected $body;

	/**
	 * @param array $proxy
	 */
	public function __construct(array $proxy = [])
	{
		$this->proxy = $proxy;
	}

	public function setHeaders(array $headers) {
		$this->headers = $headers;
	}

	public function setBody($body)
	{
		$this->body = $body;
	}

	/**
	 * Send HTTP request via curl ()
	 *
	 * @param $url - target URL
	 * @param string $method - HTTP method
	 * @param array $data - postData
	 * @param bool $verify - SSL verify flag
	 * @return array like ['status' => 'responseStatus', 'body'  => 'Body Response]
	 */
	public function send($url, $method = 'GET', array $data = [], $verify = false)
	{
		$client = new GuzzleHttp\Client();

		$request = ['verify' => $verify];

		if (!empty($this->body)) {
			$request['body'] = $this->body;
		}

		if (empty($this->body) && !empty($data)) {
			$request['form_params'] = $data;
		}

		if (!empty($this->proxy)) {
			$request['proxy'] = $this->proxy;
		}

		$request['headers'] = $this->headers;
		$response = $client->request($method, $url, $request);

		return [
			'status' => $response->getStatusCode(),
			'body' => $response->getBody()->getContents(),
		];
	}
}
