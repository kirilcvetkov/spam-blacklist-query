<?php

declare(strict_types=1);

namespace SlickSky\SpamBlacklistQuery;

use function checkdnsrr;
use function preg_match;

class Blacklist
{
    protected bool $listed;

    public function __construct(public string $host, public string $name, public string $ipReverse)
    {
    }

    public static function load(string $host, string $name, MxIp $ip): self|SpamHaus
    {
        return preg_match('/SpamHaus/i', $name)
            ? new SpamHaus($host, $name, $ip->reverse())
            : new static($host, $name, $ip->reverse());
    }

    public function query(): array|bool
    {
        return (bool) checkdnsrr($this->dnsHost(), 'A');
    }

    public function isListed(): bool
    {
        if (isset($this->listed)) {
            return $this->listed;
        }

        return $this->listed = $this->query();
    }

    public function dnsHost(): string
    {
        return $this->ipReverse . '.' . $this->host . '.';
    }
}
