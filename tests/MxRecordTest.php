<?php

declare(strict_types=1);

namespace Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use SlickSky\SpamBlacklistQuery\Blacklist;
use SlickSky\SpamBlacklistQuery\MxRecord;

use function dns_get_record;
use function reset;

use const DNS_MX;

final class MxRecordTest extends TestCase
{
    protected string $testDomain = 'google.com';
    protected MxRecord $mx;

    public function setUp(): void
    {
        $records = dns_get_record($this->testDomain, DNS_MX);
        $this->mx = new MxRecord(reset($records) ?? []);
    }

    public function testInitialize(): void
    {
        $this->assertEquals($this->testDomain, $this->mx->host);
        $this->assertEquals('IN', $this->mx->class);
        $this->assertIsInt($this->mx->ttl);
        $this->assertEquals('MX', $this->mx->type);
        $this->assertIsInt($this->mx->pri);
        $this->assertEquals('smtp.google.com', $this->mx->target);
    }

    public function testIsListed(): void
    {
        $blacklist = Mockery::mock(Blacklist::class);
        $blacklist->shouldReceive('isListed')
            ->once()
            ->andReturn(true);

        $this->mx->query($blacklist);

        $this->assertTrue($this->mx->isListed());
    }

    public function testIsNotListed(): void
    {
        $blacklist = Mockery::mock(Blacklist::class);
        $blacklist->shouldReceive('isListed')
            ->once()
            ->andReturn(false);

        $this->mx->query($blacklist);

        $this->assertFalse($this->mx->isListed());
    }
}
