<?php

//require composer autoload this path will probably change for your implementation
//wordpress would suggest using plugin_dir_path
//example require plugin_dir_path(dirname(dirname(dirname(__FILE__))))."/vendor/autoload.php";

require dirname(dirname(dirname(__FILE__))) . "/vendor/autoload.php";

use Leadpages\Auth\LeadpagesLogin;
use GuzzleHttp\Client;

class WordPressLeadpagesLogin extends LeadpagesLogin
{

    /**
     * store token in Wordpress Database
     *
     * @return mixed
     */
    public function storeToken()
    {
        update_option($this->tokenLabel, $this->token);
    }


    /**
     * get token form WordPress Database and set the $this->token
     * $this->token needs to be set on this method
     */
    public function getToken()
    {
        $this->token = get_option($this->tokenLabel);
    }

    /**
     * Delete token from WordPress Database
     * @return mixed
     */
     
    public function deleteToken()
    {
        delete_options($this->tokenLabel);
    }
}

//instantiate Class
$leadpagesLogin = new WordPressLeadpagesLogin(new Client());
//call get user pipe into parseResponse
$leadpagesLogin->getUser('example@example.com', 'password')->parseResponse();
if (isset($leadpages->token)) {
    //STORE TOKEN
    $leadpagesLogin->storeToken();
}else{
    //RETURN RESPONSE FOR DISPLAY
    return $leadpagesLogin->getLeadpagesResponse();
}


//this will set the response for checkIfUserIsloggedIn to verify against.
//could also chain them as they are fluent $leadpagesLogin->getCurrentUserToken()->checkIfUserIsLoggedIn()
//isLoggedIn should be true if the current token call resulted in a proper response from auth api
$leadpagesLogin->getCurrentUserToken();
$isLoggedIn = $leadpagesLogin->checkIfUserIsLoggedIn();

