# Spam Blacklist Query

This small package helps you find out if a domain or IP is blacklisted on the most popular spam listing services.

Here's how it works:
1. Retrieve MX records for an inputted domain
2. Get the list of IPs for each MX record
3. Test each IP against the Spam Blacklist services

### Installation

Run this command in your project's root folder

```bash
composer require slicksky/blacklist-spam-query
```

## Usage

```php
require 'vendor/autoload.php';

use SlickSky\SpamBlacklistQuery\Blacklist;
use SlickSky\SpamBlacklistQuery\Config;
use SlickSky\SpamBlacklistQuery\Domain;
use SlickSky\SpamBlacklistQuery\MxIp;

// Test a Domain
$sampleDomain = 'google.com';
$domainResults = (new Domain($sampleDomain))
   ->query(); // Collection

// Get the listed records only
$listedIps = $domainResults->listed(); // Collection

// Ask if any IP records of the domain are listed
$isListed = $domainResults->isListed(); // bool

// Override blacklisting services
// array of ['service address' => 'name']
$blacklists = new Config([
   'dnsbl-1.uceprotect.net' => 'UCEPROTECT',
]);

$domainResults = (new Domain($sampleDomain, $blacklists))
   ->query(); // returns Collection


// Test IP
$ip = new MxIp('8.8.8.8');

// Is this IP valid?
$isInvalid = $ip->isInvalid(); // bool

// Query the IP
foreach (Config::BLACKLISTS as $serviceHost => $serviceName) {
   $isListed = $ip->query(
      Blacklist::load($serviceHost, $serviceName, $ip),
   ); // bool
}

// Get the listed state
$isListed = $ip->isListed(); // bool

// Get the blacklists objects and their results
$blacklistsResults = $ip->blacklists; // Collection


```

## Results

```php

SlickSky\SpamBlacklistQuery\Result::__set_state([
   'items' => [
    SlickSky\SpamBlacklistQuery\MxRecord::__set_state([
       'host' => 'google.com',
       'class' => 'IN',
       'ttl' => 377,
       'type' => 'MX',
       'pri' => 10,
       'target' => 'smtp.google.com',
       'ips' =>
      SlickSky\SpamBlacklistQuery\Collection::__set_state([
         'items' => [
          SlickSky\SpamBlacklistQuery\MxIp::__set_state([
             'blacklists' =>
            SlickSky\SpamBlacklistQuery\Collection::__set_state([
               'items' => [
                SlickSky\SpamBlacklistQuery\Blacklist::__set_state([
                   'listed' => false,
                   'host' => 'dnsbl-1.uceprotect.net',
                   'name' => 'UCEPROTECT',
                   'ipReverse' => '27.2.251.142',
                ]),
              ],
            ]),
             'invalid' => false,
             'listed' => false,
             'ip' => '142.251.2.27',
          ]),
        ],
      ]),
    ]),
  ],
])

```

## License

The Spam Blacklist Query is open-sourced software licensed under the MIT license.
