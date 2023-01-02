# Domain Blacklist Spam Check

This small package helps to check a domain for blacklisted MX server against most Spam listing services.

### Installation
```bash
composer require slicksky/domain-blacklist-spam-check
```

## Usage

```php
require 'vendor/autoload.php';

use SlickSky\DomainBlacklistSpamCheck\Blacklists;

$sampleDomain = 'google.com';

// Retrieve a full report
$results = (new Blacklists($sampleDomain))->all();

// Retrieve only the listed MX servers
$results = (new Blacklists($sampleDomain))->listed();
```

## Results

```php
[
  0 => SlickSky\DomainBlacklistSpamCheck\MxRecord::__set_state([
     'host' => 'google.com',
     'class' => 'IN',
     'ttl' => 377,
     'type' => 'MX',
     'pri' => 10,
     'target' => 'smtp.google.com',
     'ips' => [
      0 => SlickSky\DomainBlacklistSpamCheck\MxIp::__set_state([
         'listed' => false,
         'blacklists' => [
          0 => SlickSky\DomainBlacklistSpamCheck\Blacklist::__set_state([
             'listed' => false,
             'host' => 'dnsbl-1.uceprotect.net',
             'name' => 'UCEPROTECT',
             'ip' => NULL,
          ]),
        ],
         'ip' => '172.253.115.26',
      ]),
      1 => SlickSky\DomainBlacklistSpamCheck\MxIp::__set_state([
         'listed' => false,
         'blacklists' => [
          0 => SlickSky\DomainBlacklistSpamCheck\Blacklist::__set_state([
             'listed' => false,
             'host' => 'dnsbl-1.uceprotect.net',
             'name' => 'UCEPROTECT',
             'ip' => NULL,
          ]),
        ],
         'ip' => '172.253.122.26',
      ]),
      2 => SlickSky\DomainBlacklistSpamCheck\MxIp::__set_state([
         'listed' => false,
         'blacklists' => [
          0 => SlickSky\DomainBlacklistSpamCheck\Blacklist::__set_state([
             'listed' => false,
             'host' => 'dnsbl-1.uceprotect.net',
             'name' => 'UCEPROTECT',
             'ip' => NULL,
          ]),
        ],
         'ip' => '172.253.63.27',
      ]),
      3 => SlickSky\DomainBlacklistSpamCheck\MxIp::__set_state([
         'listed' => false,
         'blacklists' => [
          0 => SlickSky\DomainBlacklistSpamCheck\Blacklist::__set_state([
             'listed' => false,
             'host' => 'dnsbl-1.uceprotect.net',
             'name' => 'UCEPROTECT',
             'ip' => NULL,
          ]),
        ],
         'ip' => '172.253.63.26',
      ]),
      4 => SlickSky\DomainBlacklistSpamCheck\MxIp::__set_state([
         'listed' => false,
         'blacklists' => [
          0 => SlickSky\DomainBlacklistSpamCheck\Blacklist::__set_state([
             'listed' => false,
             'host' => 'dnsbl-1.uceprotect.net',
             'name' => 'UCEPROTECT',
             'ip' => NULL,
          ]),
        ],
         'ip' => '142.251.16.26',
      ]),
    ],
  ]),
]
```

## License

The Domain Blacklist Spam Check is open-sourced software licensed under the MIT license.

