## Synopsis

Leadpages Auth is meant to make it simple to get your integration into Leadpages off the ground quickly.
* Abstracts away the required methods to call Leadpages to retrieve your security token.
* Built in minimal storage abstraction to allow Leadpages extensions to follow known sets of standards.
* Uses Guzzle5 to allow a consistant Http abstraction layer across all platforms. Guzzle5 chosen for PHP 5.4 support

## Code Example - WordPress

```
<?php

require "vendor/autoload.php";

use Leadpages\Auth\LeadpagesLogin;

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
$leadpagesLogin = new WordPressLeadpagesLogin();
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
```


## Installation

Package can be installed via [Composer](https://getcomposer.org/)

```
#install composer
curl -sS https://getcomposer.org/installer | php
```

Run composer to require the package

```
php composer.phar require leadpages\leadpages-auth
```

Next insure that you are included the composer autoloader into your project. Package uses PSR-4 Autoloading
```
require 'vendor/autoload.php';
```

## API Reference

Docs to come

## Tests

Tests are run via PHPUnit

## Contributors

## License

