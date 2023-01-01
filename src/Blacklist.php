<?php

declare(strict_types=1);

namespace SlickSky\DomainBlacklistSpamCheck;

class Blacklist
{
    public bool $listed = false;
    public SpamhausParser $spamhouse;

    public function __construct(protected string $host, protected string $name, protected MxIp $ip)
    {
        $this->listed = $this->parse();
    }

    public function parse(): bool
    {
        return (bool) checkdnsrr($this->dnsHost(), 'A');
    }

    public function isListed(): bool
    {
        return $this->listed;
    }

    public function dnsHost(): string
    {
        return $this->ip->reverse() . '.' . $this->host . '.';
    }
}
