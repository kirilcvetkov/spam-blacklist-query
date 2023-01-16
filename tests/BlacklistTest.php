<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use SlickSky\SpamBlacklistQuery\Blacklist;
use SlickSky\SpamBlacklistQuery\Config;
use SlickSky\SpamBlacklistQuery\MxIp;

use function current;
use function key;

final class BlacklistTest extends TestCase
{
    public function testBlacklistIp(): void
    {
        $ip = new MxIp('8.8.8.8');

        $blacklist = new Blacklist(
            key(Config::BLACKLISTS_IP),
            current(Config::BLACKLISTS_IP),
            $ip->reverse(),
        );

        $this->assertFalse($blacklist->isListed());
    }

    public function testBlacklistUri(): void
    {
        $blacklist = new Blacklist(
            key(Config::BLACKLISTS_URI),
            current(Config::BLACKLISTS_URI),
            'google.com',
        );

        $this->assertFalse($blacklist->isListed());
    }

    // public function testBlacklistExtended(): void
    // {
    //     $ip = new MxIp('8.8.8.8');

    //     foreach (Config::BLACKLISTS_FULL as $bl => $service) {
    //         d($service, $bl, (new Blacklist($bl, $service, $ip->reverse()))->isListed(), '------------');
    //     }
    // }
}
