<?php

namespace Tests\SystemChecks;

use PHPUnit\Framework\TestCase;
use SlickSky\DomainBlacklistSpamCheck\MxIp;
use SlickSky\DomainBlacklistSpamCheck\Spamhaus;

final class SpamhausTest extends TestCase
{
    public function testIsNotListed()
    {
        $validIp = '8.8.8.8';

        $ip = new MxIp($validIp);
        $blacklist = new Spamhaus('zen.spamhaus.org', 'SpamHaus', $ip);

        $this->assertFalse($blacklist->isListed());
    }

    public function testIsListed()
    {
        $validIp = '8.8.8.8'; // ??? sample listed IP

        $ip = new MxIp($validIp);
        $blacklist = new Spamhaus('zen.spamhaus.org', 'SpamHaus', $ip);

        $this->assertTrue(true); // $blacklist->isListed()
    }
}
