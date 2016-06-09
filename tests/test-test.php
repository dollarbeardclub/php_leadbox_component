<?php

class TestTest extends PHPUnit_Framework_TestCase
{
    function test_tester()
    {
        $env = getenv();
        print_r($env);
    }
}