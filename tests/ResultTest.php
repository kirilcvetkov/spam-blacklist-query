<?php

declare(strict_types=1);

namespace Tests\SystemChecks;

use Mockery;
use PHPUnit\Framework\TestCase;
use SlickSky\SpamBlacklistQuery\Blacklist;
use SlickSky\SpamBlacklistQuery\Config;
use SlickSky\SpamBlacklistQuery\Domain;
use SlickSky\SpamBlacklistQuery\MxRecord;
use SlickSky\SpamBlacklistQuery\Result;

use function array_map;

final class ResultTest extends TestCase
{
    protected function getResultObject(bool $isListed): Result
    {
        $blacklist = Mockery::mock(Blacklist::class);
        $blacklist->shouldReceive('isListed')
            ->once()
            ->andReturn($isListed);

        $mxRecord = new MxRecord([
            'host' => 'google.com',
            'class' => 'IN',
            'ttl' => 377,
            'type' => 'MX',
            'pri' => 10,
            'target' => 'smtp.google.com',
        ]);

        return new Result(array_map(
            static function ($record) use ($blacklist) {
                foreach ($record->ips() as $ip) {
                    $ip->query($blacklist);
                }

                return $record;
            },
            [$mxRecord],
        ));
    }

    public function testListed(): void
    {
        $result = $this->getResultObject(true);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertNotEmpty($result->listed());
        $this->assertContainsOnlyInstancesOf(MxRecord::class, $result);
        $this->assertTrue($result->isListed());
    }

    public function testNotListed(): void
    {
        $result = $this->getResultObject(false);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEmpty($result->listed());
        $this->assertContainsOnlyInstancesOf(MxRecord::class, $result);
        $this->assertFalse($result->isListed());
    }

    public function testToArray(): void
    {
        $testDomain = 'google.com';

        $result = (new Domain(
            $testDomain,
            new Config([key(Config::BLACKLISTS) => current(Config::BLACKLISTS)]),
        ))->query();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertContainsOnlyInstancesOf(MxRecord::class, $result);

        $array = $result->toArray();
        $array = reset($array);

        $this->assertArrayHasKey('host', $array);
        $this->assertArrayHasKey('target', $array);
        $this->assertArrayHasKey('ips', $array);
        $this->assertIsArray($array['ips']);

        $ip = reset($array['ips']);

        $this->assertArrayHasKey('blacklists', $ip);
        $this->assertArrayHasKey('invalid', $ip);
        $this->assertArrayHasKey('listed', $ip);
        $this->assertArrayHasKey('ip', $ip);
        $this->assertIsArray($ip['blacklists']);

        $blacklist = reset($ip['blacklists']);

        $this->assertArrayHasKey('host', $blacklist);
        $this->assertArrayHasKey('name', $blacklist);
        $this->assertArrayHasKey('listed', $blacklist);
        $this->assertArrayHasKey('ipReverse', $blacklist);
    }
}
