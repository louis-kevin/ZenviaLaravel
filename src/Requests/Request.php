<?php
/**
 * Created by PhpStorm.
 * User: DevMaker BackEnd
 * Date: 16/04/2018
 * Time: 12:32
 */

namespace Zenvia\Requests;


use Zenvia\Exceptions\AuthenticationNotFoundedException;
use Zenvia\Exceptions\FieldMissingException;
use Zenvia\Exceptions\RequestException;
use Zenvia\Responses\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Response as HttpResponse;

class Request
{
    const ENDPOINT = 'https://api-rest.zenvia360.com.br/services';
    private $key;

    /**
     * Request constructor.
     * @param $key
     * @throws AuthenticationNotFoundedException
     */
    public function __construct($key)
    {
        if(blank($key)){
            throw new AuthenticationNotFoundedException();
        }
        $this->key = $key;
    }

    /**
     * @param $url
     * @return array
     * @throws RequestException
     * @throws AuthenticationNotFoundedException
     */
    public function get($url)
    {
        try {
            $curl = new Client();
            $res = $curl->request('GET', self::ENDPOINT . '/' . $this->clearUrl($url), $this->getHeaders());

            if ($res->getStatusCode() > '499') {
                throw new RequestException('Erro na API Zenvia', HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
            }

            return json_decode($res->getBody(), true) ?: [];
        } catch (GuzzleException $e) {
            throw new RequestException($e->getMessage(), $e->getCode());
        }
    }

    public function clearUrl($url): string
    {
        if (strpos($url, '/') === 0) {
            $url = substr($url, 1);
        }
        return $url;
    }

    /**
     * @return array
     * @throws AuthenticationNotFoundedException
     */
    private function getHeaders()
    {
        return [
            'Authorization' => 'Basic ' . $this->key,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
    }

    /**
     * @param $to
     * @param $message
     * @param null $from
     * @return Response
     * @throws AuthenticationNotFoundedException
     * @throws RequestException
     * @throws FieldMissingException
     * @throws RequestException
     */
    public function post($body)
    {
        try {
            $curl = new Client();
            $res = $curl->request('POST', self::ENDPOINT, $this->getOptions($body));

            if ($res->getStatusCode() >= '400') {
                throw new RequestException('Erro na Zenvia', HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
            }

            return new Response(json_decode($res->getBody(), true));
        } catch (GuzzleException $e) {
            throw new RequestException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param $body
     * @return array
     * @throws AuthenticationNotFoundedException
     * @throws FieldMissingException
     */
    private function getOptions($body)
    {
        return [
            'headers' => $this->getHeaders(),
            'body' => $body
        ];
    }
}
