<?php

namespace Tests\SystemChecks;

use PHPUnit\Framework\TestCase;
use SlickSky\DomainBlacklistSpamCheck\Blacklist;
use SlickSky\DomainBlacklistSpamCheck\Blacklists;
use SlickSky\DomainBlacklistSpamCheck\MxIp;
use SlickSky\DomainBlacklistSpamCheck\MxRecord;

final class BlacklistTest extends TestCase
{
    public function testBlacklist()
    {
        $testIp = '8.8.8.8';

        $ip = new MxIp($testIp);
        $blacklist = new Blacklist(key(Blacklists::BLACKLISTS), current(Blacklists::BLACKLISTS), $ip);

        $this->assertFalse($blacklist->listed);
    }
}
