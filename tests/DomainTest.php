<?php

declare(strict_types=1);

namespace Tests\SystemChecks;

use PHPUnit\Framework\TestCase;
use SlickSky\SpamBlacklistQuery\Blacklist;
use SlickSky\SpamBlacklistQuery\Collection;
use SlickSky\SpamBlacklistQuery\Config;
use SlickSky\SpamBlacklistQuery\Domain;
use SlickSky\SpamBlacklistQuery\Exception;
use SlickSky\SpamBlacklistQuery\MxIp;
use SlickSky\SpamBlacklistQuery\MxRecord;
use SlickSky\SpamBlacklistQuery\Result;

use function current;
use function key;

final class DomainTest extends TestCase
{
    public function testCompleteListOfObjects(): void
    {
        $testDomain = 'google.com';

        $result = (new Domain(
            $testDomain,
            new Config([key(Config::BLACKLISTS) => current(Config::BLACKLISTS)]),
        ))->query();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertContainsOnlyInstancesOf(MxRecord::class, $result);

        foreach ($result as $mxRecord) {
            $this->assertEquals($testDomain, $mxRecord->host);
            $this->assertEquals('MX', $mxRecord->type);
            $this->assertInstanceOf(Collection::class, $mxRecord->ips());
            $this->assertContainsOnlyInstancesOf(MxIp::class, $mxRecord->ips());

            foreach ($mxRecord->ips() as $mxIp) {
                $this->assertFalse($mxIp->isListed());
                $this->assertFalse($mxIp->isInvalid());
                $this->assertContainsOnlyInstancesOf(Blacklist::class, $mxIp->blacklists);

                foreach ($mxIp->blacklists as $blacklist) {
                    $this->assertFalse($blacklist->isListed());
                }
            }
        }
    }

    public function testInvalidDomain(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid domain.');

        $invalidDoamin = 'google com';
        new Domain($invalidDoamin);
    }

    public function testNoMxRecords(): void
    {
        $nonExistingDoamin = 'google.commmm';

        $result = (new Domain($nonExistingDoamin))
            ->query();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEmpty($result);
    }
}
