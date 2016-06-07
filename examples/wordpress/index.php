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
     * method to implement on extending class to store token in database
     *
     * @return mixed
     */
    public function storeToken()
    {
        echo $this->token;
        //update_option($this->tokenLabel, $this->token);
    }

    /**
     * method to implement on extending class to get token from datastore
     * should return token not set property of $this->token
     * @return mixed
     */
    public function getToken()
    {
        $this->token = get_option($this->tokenLabel);
    }

    /**
     * method to implement on extending class to remove token from database
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
//isLoggedIn should be true if they current token resulted in a proper response from auth api
$leadpagesLogin->getCurrentUserToken();
$isLoggedIn = $leadpagesLogin->checkIfUserIsLoggedIn();

