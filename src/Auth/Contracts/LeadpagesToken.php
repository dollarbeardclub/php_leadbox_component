<?php

namespace Leadpages\Auth\Contracts;


/**
 * Abstract class to contract the names of functions to store and retrieve the Leadpages Token form the data store
 * Class LeadpagesToken
 * @package Leadpages\Auth\Interfaces
 */

abstract class LeadpagesToken
{

    /**
     * Token label that should be used to reference the token in the database for consistency across platforms
     * and upgrades easier
     * @var string
     */
    protected $tokenLabel = 'leadpages_security_token';

    public $token;


    /**
     * method to implement on extending class to store token in database
     *
     * @return mixed
     */
    public abstract function storeToken();

    /**
     * method to implement on extending class to get token from datastore
     * should return token not set property of $this->token
     * @return mixed
     */
    public abstract function getToken();

    /**
     * method to implement on extending class to remove token from database
     * @return mixed
     */
    public abstract function deleteToken();
}
