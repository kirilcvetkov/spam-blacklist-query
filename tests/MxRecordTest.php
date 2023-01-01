<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use SlickSky\DomainBlacklistSpamCheck\MxRecord;

use function dns_get_record;

use const DNS_MX;

final class MxRecordTest extends TestCase
{
    /**
     * @covers MxRecord
     */
    public function testInitialize(): void
    {
        $testDoamin = 'google.com';
        $records = dns_get_record($testDoamin, DNS_MX);
        $record = reset($records);

        $mx = new MxRecord($record);

        $this->assertEquals($testDoamin, $mx->host);
        $this->assertEquals('IN', $mx->class);
        $this->assertIsInt($mx->ttl);
        $this->assertEquals('MX', $mx->type);
        $this->assertIsInt($mx->pri);
        $this->assertEquals('smtp.google.com', $mx->target);
    }
}
