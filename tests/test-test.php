<?php

class TestTest extends PHPUnit_Framework_TestCase
{
    function test_tester()
    {
        $env = getenv('lpusername');
        print_r($env);
    }
}