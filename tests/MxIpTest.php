<?php

declare(strict_types=1);

namespace Tests\SystemChecks;

use Mockery;
use PHPUnit\Framework\TestCase;
use SlickSky\SpamBlacklistQuery\Blacklist;
use SlickSky\SpamBlacklistQuery\MxIp;

final class MxIpTest extends TestCase
{
    protected string $validIp = '8.8.8.8'; // Google DNS

    public function testIpInvalid(): void
    {
        $invalidIp = '8.8.8';

        $ip = new MxIp($invalidIp);

        $this->assertTrue($ip->isInvalid());
    }

    public function testIpValid(): void
    {
        $ip = new MxIp($this->validIp);

        $this->assertEquals($this->validIp, $ip->get());
        $this->assertFalse($ip->isInvalid());
    }

    public function testIsListed(): void
    {
        $blacklist = Mockery::mock(Blacklist::class);
        $blacklist->shouldReceive('isListed')
            ->once()
            ->andReturn(true);

        $ip = new MxIp($this->validIp);

        $result = $ip->query($blacklist);

        $this->assertFalse($ip->isInvalid());
        $this->assertTrue($result);
        $this->assertTrue($ip->isListed());
    }

    public function testIsNotListed(): void
    {
        $blacklist = Mockery::mock(Blacklist::class);
        $blacklist->shouldReceive('isListed')
            ->once()
            ->andReturn(false);

        $ip = new MxIp($this->validIp);

        $result = $ip->query($blacklist);

        $this->assertFalse($ip->isInvalid());
        $this->assertFalse($result);
        $this->assertFalse($ip->isListed());
    }
}
