<?php

declare(strict_types=1);

namespace SlickSky\SpamBlacklistQuery;

use function checkdnsrr;
use function preg_match;

class Blacklist
{
    protected bool $listed;

    public function __construct(public string $host, public string $service, public string $ipReverse)
    {
    }

    public static function load(string $host, string $service, MxIp $ip): self|SpamHaus
    {
        return preg_match('/SpamHaus/i', $service)
            ? new SpamHaus($host, $service, $ip->reverse())
            : new static($host, $service, $ip->reverse());
    }

    public function query(): array|bool
    {
        return (bool) checkdnsrr($this->hostname(), 'A');
    }

    public function isListed(): bool
    {
        if (isset($this->listed)) {
            return $this->listed;
        }

        return $this->listed = $this->query();
    }

    public function hostname(): string
    {
        return $this->ipReverse . '.' . $this->host . '.';
    }
}
