<?php

declare(strict_types=1);

namespace Tests;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SlickSky\SpamBlacklistQuery\Blacklist;
use SlickSky\SpamBlacklistQuery\Collection;
use SlickSky\SpamBlacklistQuery\Config;
use SlickSky\SpamBlacklistQuery\Domain;
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

        $config = new Config(
            [key(Config::BLACKLISTS_IP) => current(Config::BLACKLISTS_IP)],
            [key(Config::BLACKLISTS_URI) => current(Config::BLACKLISTS_URI)]
        );

        $result = (new Domain($testDomain, $config))->query();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertContainsOnlyInstancesOf(MxRecord::class, $result);

        foreach ($result as $mxRecord) {
            $this->assertEquals($testDomain, $mxRecord->host);
            $this->assertEquals('MX', $mxRecord->type);
            $this->assertInstanceOf(Collection::class, $mxRecord->ips());
            $this->assertContainsOnlyInstancesOf(MxIp::class, $mxRecord->ips());
            $this->assertFalse($mxRecord->isListed());

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
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid domain.');

        $invalidDomain = 'google com';
        new Domain($invalidDomain);
    }

    public function testNoMxRecords(): void
    {
        $nonExistingDomain = 'google.commmm';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No MX records found for domain.');

        (new Domain($nonExistingDomain))
            ->query();
    }
}
