<?php

declare(strict_types=1);

namespace Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use SlickSky\DomainBlacklistSpamCheck\DomainMxRecords;
use SlickSky\DomainBlacklistSpamCheck\MxRecord;

use function dns_get_record;
use function filter_var;

use const DNS_MX;
use const FILTER_VALIDATE_IP;

final class DomainMxRecordsTest extends TestCase
{
    public function testGet(): void
    {
        $testDoamin = 'google.com';
        $records    = dns_get_record($testDoamin, DNS_MX);

        $mxs = (new DomainMxRecords($testDoamin))->get();

        $this->assertIsArray($mxs);
        $this->assertEquals(count($records), count($mxs));

        foreach ($records as $k => $record) {
            $mx = $mxs[$k];

            $this->assertInstanceOf(MxRecord::class, $mx);
            $this->assertEquals($mx->host, $record['host']);
        }
    }

    public function testInvalidDomain(): void
    {
        $invalidDoamin = 'google commmm';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid domain.');

        (new DomainMxRecords($invalidDoamin))->get();
    }

    public function testNoMxRecords(): void
    {
        $invalidDoamin = 'google.commmm';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to find any MX records.');

        (new DomainMxRecords($invalidDoamin))->get();
    }
}
