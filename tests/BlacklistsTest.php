<?php

namespace Tests\SystemChecks;

use PHPUnit\Framework\TestCase;
use SlickSky\DomainBlacklistSpamCheck\Blacklist;
use SlickSky\DomainBlacklistSpamCheck\Blacklists;
use SlickSky\DomainBlacklistSpamCheck\MxIp;
use SlickSky\DomainBlacklistSpamCheck\MxRecord;

final class BlacklistsTest extends TestCase
{
    public function testCompleteListOfObjects()
    {
        $testDomain = 'google.com';
        $testOnlyOneBlacklist = [key(Blacklists::BLACKLISTS) => current(Blacklists::BLACKLISTS)];
        // $testOnlyOneBlacklist = ['zen.spamhaus.org' => 'SpamHaus'];

        $results = (new Blacklists($testDomain, $testOnlyOneBlacklist))->all();

        $this->assertIsArray($results);
        $this->assertContainsOnlyInstancesOf(MxRecord::class, $results);

        foreach ($results as $mxRecord) {
            $this->assertEquals($testDomain, $mxRecord->host);
            $this->assertEquals('MX', $mxRecord->type);
            $this->assertContainsOnlyInstancesOf(MxIp::class, $mxRecord->ips);

            foreach ($mxRecord->ips as $mxIp) {
                $this->assertFalse($mxIp->listed);
                $this->assertContainsOnlyInstancesOf(Blacklist::class, $mxIp->blacklists);

                foreach ($mxIp->blacklists as $blacklist) {
                    $this->assertFalse($blacklist->listed);
                }
            }
        }
    }

    public function testNoListedRecords()
    {
        $testDomain = 'google.com';
        $testOnlyOneBlacklist = [key(Blacklists::BLACKLISTS) => current(Blacklists::BLACKLISTS)];

        $results = (new Blacklists($testDomain, $testOnlyOneBlacklist))->listed();

        $this->assertContainsOnlyInstancesOf(MxRecord::class, $results);

        foreach ($results as $mxRecord) {
            $this->assertEquals($testDomain, $mxRecord->host);
            $this->assertEquals('MX', $mxRecord->type);
            $this->assertContainsOnlyInstancesOf(MxIp::class, $mxRecord->ips);

            foreach ($mxRecord->ips as $mxIp) {
                $this->assertTrue($mxIp->listed);
                $this->assertContainsOnlyInstancesOf(Blacklist::class, $mxIp->blacklists);
            }
        }
    }

    public function testListedRecords()
    {
        $testDomain = 'google.com';
        $testOnlyOneBlacklist = ['zen.spamhaus.org' => 'Test']; // rename Spamhaus to trigger listed

        $results = (new Blacklists($testDomain, $testOnlyOneBlacklist))->listed();

        $this->assertContainsOnlyInstancesOf(MxRecord::class, $results);

        foreach ($results as $mxRecord) {
            $this->assertEquals($testDomain, $mxRecord->host);
            $this->assertEquals('MX', $mxRecord->type);
            $this->assertContainsOnlyInstancesOf(MxIp::class, $mxRecord->ips);

            foreach ($mxRecord->ips as $mxIp) {
                $this->assertTrue($mxIp->listed);
                $this->assertContainsOnlyInstancesOf(Blacklist::class, $mxIp->blacklists);
            }
        }
    }
}
