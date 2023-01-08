<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use SlickSky\SpamBlacklistQuery\MxRecord;

use function dns_get_record;
use function reset;

use const DNS_MX;

final class MxRecordTest extends TestCase
{
    public function testInitialize(): void
    {
        $testDomain = 'google.com';
        $records    = dns_get_record($testDomain, DNS_MX);
        $record     = reset($records);

        $mx = new MxRecord($record);

        $this->assertEquals($testDomain, $mx->host);
        $this->assertEquals('IN', $mx->class);
        $this->assertIsInt($mx->ttl);
        $this->assertEquals('MX', $mx->type);
        $this->assertIsInt($mx->pri);
        $this->assertEquals('smtp.google.com', $mx->target);
    }
}
