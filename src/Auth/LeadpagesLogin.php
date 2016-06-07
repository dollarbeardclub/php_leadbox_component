<?php

namespace Leadpages\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Leadpages\Auth\Contracts\LeadpagesToken;

abstract class LeadpagesLogin extends LeadpagesToken
{

    protected $client;
    protected $response;

    public function __construct(Client $client)
    {

        $this->client        = $client;
        $this->loginurl      = 'https://api.leadpages.io/auth/v1/sessions/';
        $this->loginCheckUrl = 'https://api.leadpages.io/auth/v1/sessions/current';
    }

    protected function hashUserNameAndPassword($username, $password)
    {
        return base64_encode($username . ':' . $password);
    }

    /**
     * get user information
     *
     * @param $username
     * @param $password
     *
     * @return array|\GuzzleHttp\Message\FutureResponse|\GuzzleHttp\Message\ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|null
     */

    public function getUser($username, $password)
    {
        $authHash = $this->hashUserNameAndPassword($username, $password);

        try {
            $response       = $this->client->post(
              $this->loginurl, //url
              [
                'headers' => ['Authorization' => 'Basic ' . $authHash],
                'body'    => ['clientType' => 'wp-plugin'] //wp-plugin value makes session not expire
              ]);
            $this->response = $response->getBody();
            return $this;

        } catch (ClientException $e) {
            $response       = [
              'code'     => $e->getCode(),
              'response' => $e->getMessage(),
              'error'    => 'true'
            ];
            $this->response = json_encode($response);
            return $this;
        }
    }

    /**
     * Check the users security token to ensure it is still valid, if it is we return the response object
     * else we return an array with HttpResponseCode and error message
     * @return $this
     */
    public function checkIfUserIsLoggedIn()
    {
        print_r($this->response);
        //call getCurrentUserToken to get response

        //check if $this->response is an array
        if(is_array(json_decode($this->response, true))){
            return false;
        }else{
            return true;
        }

    }

    public function getCurrentUserToken()
    {
        $this->token = $this->getToken();

        try {
            $response       = $this->client->get(
              $this->loginCheckUrl,
              [
                'headers' => ['LP-Security-Token' => $this->token]
              ]);
            $this->response = $response->getBody();
            return $this;
        } catch (ClientException $e) {
            $response       = [
              'code'     => $e->getCode(),
              'response' => $e->getMessage(),
              'error'    => 'true'
            ];
            $this->response = json_encode($response);
            return $this;
        }
    }

    /**
     * parse response for call to Leadpages Login if response does
     * not contain an error it is good else we return an
     * response with HttpResonseCode and Message
     *
     * @param bool $deleteTokenOnFail
     *
     * @return mixed
     */
    public function parseResponse($deleteTokenOnFail = false)
    {
        $responseArray = json_decode($this->response, true);
        if (isset($responseArray['error']) && $responseArray['error'] == true) {
            //token should be unset assumed to be no longer valid
            unset($this->token);
            //delete token from data store if param is passed
            if($deleteTokenOnFail == true){
                $this->deleteToken();
            }
            return $this->response; //return json encoded response for client to handle
        }
        $this->token = $responseArray['securityToken'];
    }

    public function getLeadpagesResponse()
    {
        return $this->response;
    }

    /**
     * set response property. really did not want to make this method
     * but it is needed for testing
     *
     * @param $response
     */
    public function setLeadpagesResponse($response)
    {
        $this->response = $response;
    }


}