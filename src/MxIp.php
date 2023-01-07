<?php

declare(strict_types=1);

namespace SlickSky\SpamBlacklistQuery;

use function array_reverse;
use function explode;
use function filter_var;
use function implode;

use const FILTER_VALIDATE_IP;

class MxIp
{
    public Collection $blacklists;
    protected bool $invalid;
    protected bool $listed;

    public function __construct(protected string|null $ip)
    {
        $this->invalid    = filter_var($this->ip, FILTER_VALIDATE_IP) !== $this->ip;
        $this->blacklists = new Collection([]);
    }

    public function get(): string
    {
        return $this->ip;
    }

    public function query(Blacklist $blacklist): bool|null
    {
        if ($this->invalid) {
            return null; // TODO Exception?
        }

        $listed = $blacklist->isListed();

        $this->listed ??= false ?: $listed;

        $this->blacklists[] = $blacklist;

        return $listed;
    }

    public function isListed(): bool|null
    {
        return $this->listed ?? null;
    }

    public function isInvalid(): bool
    {
        return $this->invalid;
    }

    public function reverse(): string
    {
        if ($this->invalid) {
            return '';
        }

        return implode('.', array_reverse(explode('.', $this->ip)));
    }
}
