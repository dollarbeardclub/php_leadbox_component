<?php


namespace Leadpages\Leadboxes;

use GuzzleHttp\Client;
use Leadpages\Auth\LeadpagesLogin;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ClientException;


class Leadboxes
{

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;
    /**
     * @var \Leadpages\Auth\LeadpagesLogin
     */
    private $login;
    /**
     * @var \Leadpages\Auth\LeadpagesLogin
     */
    public $response;

    /**
     * @property string leadboxesUrl
     */
    public $leadboxesUrl;

    public function __construct(Client $client, LeadpagesLogin $login)
    {

        $this->client = $client;
        $this->login = $login;
        $this->leadboxesUrl = "https://my.leadpages.net/leadbox/v1/leadboxes";
    }


    public function getAllLeadboxes()
    {
        try{
            $response = $this->client->get($this->leadboxesUrl,
              [
                'headers' => ['LP-Security-Token' => $this->login->token]
              ]);
            $response       = [
              'code'     => '200',
              'response' => $response->getBody()->getContents()
            ];
        }catch (ClientException $e){
            $response       = [
              'code'     => $e->getCode(),
              'response' => $e->getMessage(),
              'error'    => (bool)true
            ];
        }

        return $response;
    }


    public function getSingleLeadboxEmbedCode($id, $type)
    {
        try{
            $url = $this->buildSingleLeadboxUrl($id, $type);
            $response = $this->client->get($url,
              [
                'headers' => ['LP-Security-Token' => $this->login->token]
              ]);

            $body = $response->getBody()->getContents();
            $body = json_decode($body, true);

            $response       = [
              'code'     => '200',
              'response' => json_encode(['embed_code' => $body['_items']['publish_settings']['embed_code']])
            ];
        }catch (ClientException $e){
            $response       = [
              'code'     => $e->getCode(),
              'response' => $e->getMessage(),
              'error'    => (bool)true
            ];
        }
        //returns a terrible error if the id does not exist, throws a 500
        catch(ServerException $e){
            $response       = [
              'code'     => $e->getCode(),
              'response' => $e->getMessage(),
              'error'    => (bool)true
            ];
        }

        return $response;
    }

    public function buildSingleLeadboxUrl($id, $type)
    {
        $queryParams = http_build_query(['popup_type' => $type]);
        $url = $this->leadboxesUrl.'/'.$id.'?'.$queryParams;
        return $url;
    }

}