<?php

declare(strict_types=1);

namespace SlickSky\DomainBlacklistSpamCheck;

use Exception;

class MxIp
{
    public bool $listed = false;
    public array $blacklists = [];

    public function __construct(protected ?string $ip)
    {
        if (filter_var($this->ip, FILTER_VALIDATE_IP) !== $this->ip) {
            throw new Exception('Invalid IP.');
        }
    }

    public function reverse()
    {
        return implode('.', array_reverse(explode('.', $this->ip)));
    }

    public function isListed(Blacklist $blacklist): bool
    {
        $this->listed = $this->listed ?: $blacklist->isListed();

        $this->blacklists[] = $blacklist;

        return $blacklist->isListed();
    }
}
