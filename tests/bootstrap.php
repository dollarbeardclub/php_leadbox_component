<?php

require_once dirname(dirname(__FILE__)).'/vendor/autoload.php';

$localTestDataFile = dirname(__FILE__) . '/data/testData.php';
if(file_exists($localTestDataFile)) {
    require $localTestDataFile;
}