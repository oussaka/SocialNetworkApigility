<?php

namespace Api\Client;

use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Decoder as JsonDecoder;
use Zend\Json\Json;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Session\Container;
use Zend\Cache\StorageFactory;

/**
 * This client manages all the operations needed to interface with the
 * social network API
 *
 * @package default
 */
class ApiClient {

    /**
     * Holds the client we will reuse in this class
     *
     * @var Client
     */
    protected static $client = null;

    /**
     * Holds the session container
     *
     * @var Zend\Session\Container
     */
    protected static $session = null;

    /**
     * Holds the endpoint urls
     *
     * @var string
     */
    protected static $endpointHost = 'http://localhost:8080';
    protected static $endpointWall = '/api/wall/%s';
    protected static $endpointFeeds = '/api/feeds/%s';
    protected static $endpointSpecificFeed = '/api/feeds/%s/%d';
    protected static $endpointUsers = '/api/users';
    protected static $endpointGetUser = '/api/users/%s';
    protected static $endpointUserLogin = '/api/users/login';
    protected static $endpointOAuth = '/oauth/login';

    /**
     * Perform an API reqquest to retrieve the data of the wall
     * of an specific user on the social network
     *
     * @param string $username
     * @return Zend\Http\Response
     */
    public static function getWall($username)
    {
        $url = self::$endpointHost . sprintf(self::$endpointWall, $username);
        return self::doRequest($url);
    }

    /**
     * Perform an API request to post content on the wall of an specific user
     *
     * @param string $username
     * @param array $data
     * @return Zend\Http\Response
     */
    public static function postWallContent($username, $data)
    {
        $url = self::$endpointHost . sprintf(self::$endpointWall, $username);
        return self::doRequest($url, $data, Request::METHOD_POST);
    }

    /**
     * Perform an API request to get the list subscriptions of a username
     *
     * @param string $username
     * @return Zend\Http\Response
     */
    public static function getFeeds($username)
    {
        $url = self::$endpointHost . sprintf(self::$endpointFeeds, $username);
        return self::doRequest($url);
    }

    /**
     * Perform an API request to add a new subscription
     *
     * @param string $username
     * @param array $postData
     * @return Zend\Http\Response
     */
    public static function addFeedSubscription($username, $postData)
    {
        $url = self::$endpointHost . sprintf(self::$endpointFeeds, $username);
        return self::doRequest($url, $postData, Request::METHOD_POST);
    }

    /**
     * Perform an API request to remove a subscription
     *
     * @param string $username
     * @param array $postData
     * @return Zend\Http\Response
     */
    public static function removeFeedSubscription($username, $feedId)
    {
        $url = self::$endpointHost . sprintf(self::$endpointSpecificFeed, $username, $feedId);
        return self::doRequest($url, null, Request::METHOD_DELETE);
    }

    /**
     * Perform an API request to add a new user
     *
     * @param array $postData
     * @return Zend\Http\Response
     */
    public static function registerUser($postData)
    {
        $url = self::$endpointHost . self::$endpointUsers;
        return self::doRequest($url, $postData, Request::METHOD_POST);
    }

    /**
     * Perform an API request to get the basic data of a user
     *
     * @param string $username
     * @return Zend\Http\Response
     */
    public static function getUser($username)
    {
        $url = self::$endpointHost . sprintf(self::$endpointGetUser, $username);
        return self::doRequest($url);
    }

    /**
     * get oauth token
     */
    public static function getToken()
    {
        $postData['grant_type'] = 'client_credentials';
        $postData['redirect_uri'] = 'http://example.com';
        $postData['client_id'] = 'social_netwotk_client';
        $postData['client_secret'] = 'testpass';
        /**
         * if grant_type = password use this:
         * user password, stored in oauth_users table
         */
        /*$postData['grant_type'] = 'password';
         $postData['username'] = '';
         $postData['password'] = '';
        */

        $url = self::$endpointHost . self::$endpointOAuth;
        return self::doRequest($url, $postData, Request::METHOD_POST);
    }

    /**
     * Perform an API request to authenticate a user
     *
     * @param array $postData Array containing username and password on their respective keys
     * @return Zend\Http\Response
     */
    public static function authenticate($postData)
    {
        $url = self::$endpointHost . self::$endpointUserLogin;
        return self::doRequest($url, $postData, Request::METHOD_POST);
    }

    /**
     * Get the oauth session container
     *
     * @return Zend\Session\Container
     */
    public static function getSession()
    {
        if (self::$session === null) {
            self::$session = new Container('oauth_session');
        }

        return self::$session;
    }

    /**
     * Create a new instance of the Client if we don't have it or
     * return the one we already have to reuse
     *
     * @return Client
     */
    protected static function getClientInstance()
    {
        if (self::$client === null) {
            self::$client = new Client();
            self::$client->setEncType(Client::ENC_URLENCODED);
            self::$client->setOptions(array(
                'maxredirects' => 0,
                'timeout'      => 40
            ));
            $headers = new \Zend\Http\Headers();
            $headers->addHeaders(array(
                // An object implementing Zend\Http\Header\HeaderInterface
                \Zend\Http\Header\ContentType::fromString('Content-Type: application/json'),
                \Zend\http\header\Accept::fromString('Accept: application/json')  // 'Content-Type: text/html',
            ));
            //$headers->addHeaderLine('Accept: application/json');
            self::$client->setHeaders($headers);
        }

        return self::$client;
    }

    /**
     * Perform a request to the API
     *
     * @param string $url
     * @param array $postData
     * @param Client $client
     * @return Zend\Http\Response
     * @author Christopher
     */
    protected static function doRequest($url, array $postData = null, $method = Request::METHOD_GET)
    {
        $client = self::getClientInstance();
        // $client->resetParameters(); // must be called before addHeader methode
        $client->setUri($url);
        $client->setMethod($method);

        if ($postData === null) {
            $postData = array();
        }

        $postData['access_token'] = self::getSession()->accessToken;
        if(!empty($postData['access_token'])) {
            // Fetch the container
            $headers = $client->getRequest()->getHeaders();
            $headers->addHeader(\Zend\Http\Header\Authorization::fromString('Authorization: Bearer ' . $postData['access_token']));
            $client->setHeaders($headers);
        }

        if ($method == Request::METHOD_POST && $postData !== null) {
            // $client->setParameterPost($postData);
            if ($postData !== null) {
                $client->setRawBody(
                    json_encode($postData)
                );
            }
        }

        /* if (($method == Request::METHOD_GET || $method == Request::METHOD_DELETE) && $postData !== null) {
            $client->setParameterGet($postData);
        } */

        $response = $client->send();

        if ($response->isSuccess()) {
            return JsonDecoder::decode($response->getBody(), Json::TYPE_ARRAY);
        } else {
            $logger = new Logger;
            $logger->addWriter(new Stream('data/logs/apiclient.log'));
            $logger->debug($response->getBody());
            return FALSE;
        }
    }
}