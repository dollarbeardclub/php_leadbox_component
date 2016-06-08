<?php

use Leadpages\Auth\LeadpagesLogin;
use phpunit\framework\TestCase;

require dirname(__FILE__) . '/data/testData.php';

class LeadpagesLoginTestSuccess extends TestCase
{
    public $stub;
    public $username;
    public $password;

    public function setUp()
    {
        global $testData;

        $this->username = $testData['username'];
        $this->password = $testData['password'];

        $this->stub = $this->getMockForAbstractClass(LeadpagesLogin::class, [new GuzzleHttp\Client()]);

        //set to true to simulate getting back a true response from api call

        $this->stub->expects($this->any())
                   ->method('storeToken')
                   ->will($this->returnValue(true));

        $this->stub->expects($this->any())
                   ->method('getToken')
                   ->will($this->returnValue(true));

        $this->stub->expects($this->any())
                   ->method('deleteToken')
                   ->will($this->returnValue(true));

        $this->stub->expects($this->any())
                   ->method('deleteToken')
                   ->will($this->returnValue(true));

        $this->stub->expects($this->any())
                   ->method('checkIfUserIsLoggedIn')
                   ->will($this->returnValue(true));

    }

    /**
     * test getting actual user and parsing the response. This should set $this->token to
     * Leadpages security token
     *
     * @group login-success
     */
    public function test_get_user()
    {
        //call to actual service, choose not to mock this out as I want to
        //make sure the service itself is returning the correct data

        //get a response from Leadpages
        $this->stub->getUser($this->username, $this->password)->parseResponse();

        //if all succeeded the token should not be empty and should be a string
        $this->assertNotEmpty($this->stub->token);
        $this->assertInternalType('string', $this->stub->token);
    }

    /**
     * @group login-success
     */

    public function test_current_user_is_logged_in()
    {
        //set response
        $this->stub->setLeadpagesResponse('leadpagesToken');
        //check if response satisfies being logged in
        $isLoggedIn = $this->stub->checkIfUserIsLoggedIn();
        $this->assertTrue($isLoggedIn);
    }

}

class LeadpagesLoginTestFail extends TestCase
{
    public $stub;
    public $username;
    public $password;

    public function setUp()
    {
        global $testData;

        $this->username = $testData['badusername'];
        $this->password = $testData['badusername'];

        $this->stub = $this->getMockForAbstractClass(LeadpagesLogin::class, [new GuzzleHttp\Client()]);

        //set to true to simulate getting back a true response from api call
        $this->stub->expects($this->any())
                   ->method('getCurrentUserToken')
                   ->will($this->returnValue(true));

        $this->stub->expects($this->any())
                   ->method('storeToken')
                   ->will($this->returnValue(true));

        $this->stub->expects($this->any())
                   ->method('getToken')
                   ->will($this->returnValue('123abc'));

        $this->stub->expects($this->any())
                   ->method('deleteToken')
                   ->will($this->returnValue(true));
    }

    /**
     * test getting actual user and parsing the repsonse. This should set $this->token to
     * Leadpages security token
     *
     * @group login-fail
     */
    public function test_get_user()
    {
        //call to actual service, chose not to mock this out as I want to
        //make sure the service itself is returning the correct data

        //get a response from Leadpages
        $this->stub->getUser($this->username, $this->password)->parseResponse();


        $responseArray = json_decode($this->stub->getLeadpagesResponse(), true);
        $this->assertArrayHasKey('error', $responseArray);
        $this->assertEquals('401', $responseArray['code']);

    }

    /**
     * @group login-fail
     */

    public function test_current_user_is_logged_in()
    {
        //set response
        $this->stub->setLeadpagesResponse(json_encode([
            "code"     => "401",
            "response" => "Client error response [url] https://api.leadpages.io/auth/v1/sessions/ [status code] 401 [reason phrase] Unauthorized",
            "error"    => true

          ])
        );
        //check if response satisfies being logged in
        $isLoggedIn = $this->stub->checkIfUserIsLoggedIn();
        $this->assertFalse($isLoggedIn);
    }


}