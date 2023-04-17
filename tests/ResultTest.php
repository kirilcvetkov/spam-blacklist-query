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
    protected function providerResultObject()
    {
        $tests = [
            [true, false, 'assertNotEmpty', true],
            [false, true, 'assertEmpty', false],
            [false, false, 'assertNotEmpty', true]
        ];

        foreach ($tests as [$isIpListed, $isUriListed, $assertListed, $expectedIsListed]) {
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

            yield [
                new Result(array_map(
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
                )),
                $assertListed,
                $expectedIsListed,
            ];
        }
    }

    /**
     * @dataProvider providerResultObject
     */
    public function testListedIp($result, $assertListed, $expectedIsListed): void
    {
        $this->assertInstanceOf(Result::class, $result);
        $this->$assertListed($result->listed());
        $this->assertContainsOnlyInstancesOf(MxRecord::class, $result);
        $this->assertEquals($expectedIsListed, $result->isListed());
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
dd($array);
        $array = reset($array);

        $this->assertArrayHasKey('host', $array);
        $this->assertArrayHasKey('target', $array);
        $this->assertArrayHasKey('ips', $array);
        $this->assertArrayHasKey('listed', $array);
        $this->assertArrayHasKey('blacklists', $array);
        $this->assertIsArray($array['ips']);

        $ip = reset($array['ips']);

        $this->assertArrayHasKey('blacklists', $ip);
        $this->assertArrayHasKey('invalid', $ip);
        $this->assertArrayHasKey('listed', $ip);
        $this->assertArrayHasKey('ip', $ip);
        $this->assertIsArray($ip['blacklists']);

        $blacklist = reset($ip['blacklists']);

        $this->assertArrayHasKey('host', $blacklist);
        $this->assertArrayHasKey('service', $blacklist);
        $this->assertArrayHasKey('listed', $blacklist);
        $this->assertArrayHasKey('ipReverse', $blacklist);
        $this->assertArrayHasKey('responseTime', $blacklist);
    }
}
