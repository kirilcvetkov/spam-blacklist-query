# Domain Blacklist Spam Check

This small package helps to check a domain for blacklisted MX server against most Spam listing services.

## Usage

```
require __DIR__ . '/../vendor/autoload.php';

use SlickSky\DomainBlacklistSpamCheck\Blacklists;

// Retrieve a full report
$results = (new Blacklists('google.com'))->all();

// Retrieve only the listed MX servers
$results = (new Blacklists('google.com'))->listed();
```

## Results

```
array:1 [
  0 => SlickSky\DomainBlacklistSpamCheck\MxRecord^ {#441
    +host: "google.com"
    +class: "IN"
    +ttl: 377
    +type: "MX"
    +pri: 10
    +target: "smtp.google.com"
    +ips: array:5 [
      0 => SlickSky\DomainBlacklistSpamCheck\MxIp^ {#442
        +listed: false
        +blacklists: array:1 [
          0 => SlickSky\DomainBlacklistSpamCheck\Blacklist^ {#443
            +listed: false
            #host: "dnsbl-1.uceprotect.net"
            #name: "UCEPROTECT"
            #ip: SlickSky\DomainBlacklistSpamCheck\MxIp^ {#442}
          }
        ]
        #ip: "142.251.16.27"
      }
      1 => SlickSky\DomainBlacklistSpamCheck\MxIp^ {#440
        +listed: false
        +blacklists: array:1 [
          0 => SlickSky\DomainBlacklistSpamCheck\Blacklist^ {#436
            +listed: false
            #host: "dnsbl-1.uceprotect.net"
            #name: "UCEPROTECT"
            #ip: SlickSky\DomainBlacklistSpamCheck\MxIp^ {#440}
          }
        ]
        #ip: "142.251.163.26"
      }
      2 => SlickSky\DomainBlacklistSpamCheck\MxIp^ {#439
        +listed: false
        +blacklists: array:1 [
          0 => SlickSky\DomainBlacklistSpamCheck\Blacklist^ {#435
            +listed: false
            #host: "dnsbl-1.uceprotect.net"
            #name: "UCEPROTECT"
            #ip: SlickSky\DomainBlacklistSpamCheck\MxIp^ {#439}
          }
        ]
        #ip: "142.251.163.27"
      }
      3 => SlickSky\DomainBlacklistSpamCheck\MxIp^ {#438
        +listed: false
        +blacklists: array:1 [
          0 => SlickSky\DomainBlacklistSpamCheck\Blacklist^ {#434
            +listed: false
            #host: "dnsbl-1.uceprotect.net"
            #name: "UCEPROTECT"
            #ip: SlickSky\DomainBlacklistSpamCheck\MxIp^ {#438}
          }
        ]
        #ip: "172.253.62.26"
      }
      4 => SlickSky\DomainBlacklistSpamCheck\MxIp^ {#437
        +listed: false
        +blacklists: array:1 [
          0 => SlickSky\DomainBlacklistSpamCheck\Blacklist^ {#433
            +listed: false
            #host: "dnsbl-1.uceprotect.net"
            #name: "UCEPROTECT"
            #ip: SlickSky\DomainBlacklistSpamCheck\MxIp^ {#437}
          }
        ]
        #ip: "172.253.115.26"
      }
    ]
  }
]
```

## License

The Domain Blacklist Spam Check is open-sourced software licensed under the MIT license.

