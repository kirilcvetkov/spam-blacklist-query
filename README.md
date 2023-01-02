# Domain Blacklist Spam Check

This small package helps to check a domain for blacklisted MX server against most Spam listing services.

## Usage

require __DIR__ . '/../vendor/autoload.php';

use SlickSky\DomainBlacklistSpamCheck\Blacklists;

$results = (new Blacklists('google.com'))->all();

## License

TODO
