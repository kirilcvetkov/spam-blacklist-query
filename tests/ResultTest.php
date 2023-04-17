<?php

declare(strict_types=1);

namespace Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use SlickSky\SpamBlacklistQuery\Blacklist;
use SlickSky\SpamBlacklistQuery\Config;
use SlickSky\SpamBlacklistQuery\Domain;
use SlickSky\SpamBlacklistQuery\MxRecord;
use SlickSky\SpamBlacklistQuery\Result;

use function array_map;
use function current;
use function key;
use function reset;

final class ResultTest extends TestCase
{
    protected function getResultObject(bool $isIpListed = false, bool $isUriListed = false): Result
    {
        $blacklistIp = Mockery::mock(Blacklist::class);
        $blacklistIp->shouldReceive('isListed')
            ->once()
            ->andReturn($isIpListed);

        $blacklistUri = Mockery::mock(Blacklist::class);
        $blacklistUri->shouldReceive('isListed')
            ->once()
            ->andReturn($isUriListed);

        $mxRecord = new MxRecord([
            'host' => 'google.com',
            'class' => 'IN',
            'ttl' => 377,
            'type' => 'MX',
            'pri' => 10,
            'target' => 'smtp.google.com',
        ]);

        return new Result(array_map(
            static function ($record) use ($blacklistIp, $blacklistUri) {
                // DNSBL URI
                $record->query($blacklistUri);

                // DNSBL IP
                foreach ($record->ips() as $ip) {
                    $ip->query($blacklistIp);
                }

                return $record;
            },
            [$mxRecord],
        ));
    }

    public function testListedIp(): void
    {
        $result = $this->getResultObject(isIpListed: true);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertNotEmpty($result->listedOnly());
        $this->assertContainsOnlyInstancesOf(MxRecord::class, $result);
        $this->assertTrue($result->isListed());
    }

    public function testListedUri(): void
    {
        $result = $this->getResultObject(isUriListed: true);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertNotEmpty($result->listedOnly());
        $this->assertContainsOnlyInstancesOf(MxRecord::class, $result);
        $this->assertTrue($result->isListed());
    }

    public function testNotListed(): void
    {
        $result = $this->getResultObject();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEmpty($result->listedOnly());
        $this->assertContainsOnlyInstancesOf(MxRecord::class, $result);
        $this->assertFalse($result->isListed());
    }

    public function testToArray(): void
    {
        $testDomain = 'google.com';

        $config = new Config(
            [key(Config::BLACKLISTS_IP) => current(Config::BLACKLISTS_IP)],
            [key(Config::BLACKLISTS_URI) => current(Config::BLACKLISTS_URI)]
        );

        $result = (new Domain($testDomain, $config))->query();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertContainsOnlyInstancesOf(MxRecord::class, $result);

        $array = $result->toArray();

        $array = reset($array);

        $this->assertArrayHasKey('host', $array);
        $this->assertArrayHasKey('target', $array);
        $this->assertArrayHasKey('ips', $array);
        $this->assertArrayHasKey('isListed', $array);
        $this->assertArrayHasKey('blacklists', $array);
        $this->assertIsArray($array['ips']);

        $ip = reset($array['ips']);

        $this->assertArrayHasKey('blacklists', $ip);
        $this->assertArrayHasKey('invalid', $ip);
        $this->assertArrayHasKey('isListed', $ip);
        $this->assertArrayHasKey('ip', $ip);
        $this->assertIsArray($ip['blacklists']);

        $blacklist = reset($ip['blacklists']);

        $this->assertArrayHasKey('host', $blacklist);
        $this->assertArrayHasKey('service', $blacklist);
        $this->assertArrayHasKey('isListed', $blacklist);
        $this->assertArrayHasKey('ipReverse', $blacklist);
        $this->assertArrayHasKey('responseTime', $blacklist);
    }
}
