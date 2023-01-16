# Spam Blacklist Query

This small package helps you find out if a domain or IP is blacklisted on the most popular spam listing services.

Here's how it works:
1. Test the domain against these Spam Blacklist services (DNSBL URI):
   - APEWS Level 1 (http://www.apews.org/)
   - Scientific Spam URI (https://www.scientificspam.net/)
   - SEM URI (https://spameatingmonkey.com/)
   - SEM URIed (https://spameatingmonkey.com/)
   - SORBS URI (http://www.sorbs.net/)
   - SpamHaus Zen (https://www.spamhaus.org/zen/)
   - SURBL multi (https://surbl.org/)
   - URIBL multi (https://uribl.com/)
2. Retrieve mail servers for the given domain (MX records).
3. Get the list of IPs for each mail servers (A records).
4. Test each IP against these Spam Blacklist services (DNSBL IP):
   - UCEPROTECT (https://www.uceprotect.net/en/)
   - DroneBL (https://dronebl.org/)
   - SORBS (http://www.sorbs.net/)
   - SpamHaus Zen (https://www.spamhaus.org/zen/)
   - SpamCop.net (https://www.spamcop.net/)
   - DSBL (https://www.dsbl.org/)


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
   ->query(); // returns Collection

// Get the listed records only
$listedIps = $domainResults->listed(); // returns Collection

// Ask if the domain or any IP records are listed
$isListed = $domainResults->isListed(); // returns bool


/**
 * Customize blacklist services (DNSBL)
 *
 * There are 4 sets of Blacklists in the Config class:
 *   1. Config::BLACKLISTS_IP - used to test IPs
 *   2. Config::BLACKLISTS_URI - used to test domains/subdomains
 *   3. Config::BLACKLISTS_EXTENDED - mixed list of most popular blacklists
 *   4. Config::BLACKLISTS_FULL - mixed list of all blacklists I've found so far
 *
 * In the Config class, you can customize blacklistsIp and/or blacklistsUri.
 * If you omit any, the internal list will be used.
 * If you want to turn off IP or URI queries, pass an empty array to blacklistsIp or blacklistsUri.
 *
 * Blacklist array template: ['service address' => 'name']
 */

$blacklists = new Config(
   blacklistsIp: ['dnsbl-1.uceprotect.net' => 'UCEPROTECT'],
   blacklistsUri: ['zen.spamhaus.org' => 'SpamHaus Zen'],
);

$domainResults = (new Domain($sampleDomain, $blacklists))
   ->query(); // returns Collection


// Test a single IP
$ip = new MxIp('8.8.8.8');

// Is this IP valid?
$isInvalid = $ip->isInvalid(); // returns bool

// Query the IP
foreach (Config::BLACKLISTS_IP as $serviceHost => $serviceName) {
   $isListed = $ip->query(
      Blacklist::load($serviceHost, $serviceName, $ip),
   ); // returns bool
}

// Get the listed state
$isListed = $ip->isListed(); // returns bool

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
       'listed' => false,
       'blacklists' =>
      SlickSky\SpamBlacklistQuery\Collection::__set_state([
         'items' => [
          SlickSky\SpamBlacklistQuery\Blacklist::__set_state([
             'listed' => false,
             'host' => 'dnsbl-1.uceprotect.net',
             'name' => 'UCEPROTECT',
             'ipReverse' => 'google.com',
             'responseTime' => 0.012,
          ]),
        ],
      ]),
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
                   'responseTime' => 0.012,
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
