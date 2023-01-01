<?php

namespace Tests\SystemChecks;

use Exception;
use PHPUnit\Framework\TestCase;
use SlickSky\DomainBlacklistSpamCheck\Blacklist;
use SlickSky\DomainBlacklistSpamCheck\Blacklists;
use SlickSky\DomainBlacklistSpamCheck\MxIp;
use SlickSky\DomainBlacklistSpamCheck\MxRecord;

final class MxIpTest extends TestCase
{
    public function testValidIp()
    {
        $validIp = '8.8.8.8'; // Google DNS

        $ip = new MxIp($validIp);

        $this->assertEquals($validIp, $ip->reverse());
        $this->assertFalse($ip->listed);
    }

    public function testInvalidIp()
    {
        $invalidIp = '8.8.8';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid IP.');

        $ip = new MxIp($invalidIp);
    }

    public function testCheck()
    {
        $validIp = '8.8.8.8'; // Google DNS
        $host = key(Blacklists::BLACKLISTS);
        $name = current(Blacklists::BLACKLISTS);

        $ip = new MxIp($validIp);
        $blacklist = new Blacklist($host, $name, $ip);

        $this->assertFalse($ip->isListed($blacklist));
    }
}
