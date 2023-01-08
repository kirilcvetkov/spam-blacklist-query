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
    public function testBlacklist(): void
    {
        $testIp = '8.8.8.8';
        $ip     = new MxIp($testIp);

        $blacklist = new Blacklist(
            key(Config::BLACKLISTS),
            current(Config::BLACKLISTS),
            $ip->reverse(),
        );

        $this->assertFalse($blacklist->isListed());
    }
}
