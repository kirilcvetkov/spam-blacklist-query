<?php

require __DIR__ . '/../vendor/autoload.php';

$testDomain = 'google.com';

dd((new SlickSky\DomainBlacklistSpamCheck\Blacklists($testDomain))->all());
