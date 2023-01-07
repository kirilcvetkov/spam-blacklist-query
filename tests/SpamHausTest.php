<?php

declare(strict_types=1);

namespace Tests\SystemChecks;

use Mockery;
use PHPUnit\Framework\TestCase;
use SlickSky\SpamBlacklistQuery\MxIp;
use SlickSky\SpamBlacklistQuery\SpamHaus;

final class SpamHausTest extends TestCase
{
    protected string $host    = 'zen.spamhaus.org';
    protected string $name    = 'SpamHaus';
    protected string $ipValid = '8.8.8.8';

    public function testIsNotListed(): void
    {
        $blacklist = new SpamHaus($this->host, $this->name, (new MxIp($this->ipValid))->reverse());

        $this->assertFalse($blacklist->isListed());
    }

    public function testIsListed(): void
    {
        $blacklist = Mockery::mock(
            SpamHaus::class,
            [$this->host, $this->name, (new MxIp($this->ipValid))->reverse()],
        )->makePartial();

        $blacklist->shouldReceive('query')
            ->andReturn([['ip' => '127.0.0.1']]);

        $this->assertTrue($blacklist->isListed());
    }
}
