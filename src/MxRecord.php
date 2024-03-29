<?php

declare(strict_types=1);

namespace SlickSky\SpamBlacklistQuery;

use function array_map;
use function dns_get_record;

use const DNS_A;

class MxRecord
{
    public string|null $host;
    public string|null $class;
    public int|null $ttl;
    public string|null $type;
    public int|null $pri;
    public string|null $target;
    public Collection $blacklists;
    protected bool $listed;
    protected Collection $ips;

    /**
     * __construct
     *
     * @param string[] $record Individual item from dns_get_record()
     */
    public function __construct(array $record)
    {
        $this->host   = $record['host'] ?? null;
        $this->class  = $record['class'] ?? null;
        $this->ttl    = $record['ttl'] ?? null;
        $this->type   = $record['type'] ?? null;
        $this->pri    = $record['pri'] ?? null;
        $this->target = $record['target'] ?? null;

        $this->blacklists = new Collection([]);
    }

    /**
     * Lookup MX records of a domain
     *
     * @return string[] List of MX servers.
     */
    public static function lookup(string $domain): array
    {
        return array_map(
            static fn ($mx) => new static($mx),
            dns_get_record($domain, DNS_MX) ?: [],
        );
    }

    /**
     * Lazy loading MX record IP(s)
     *
     * @return string[] List of MX servers.
     */
    public function ips(): Collection
    {
        if (isset($this->ips)) {
            return $this->ips;
        }

        return $this->ips = new Collection(array_map(
            static fn ($record) => new MxIp($record['ip'] ?? null),
            dns_get_record($this->target, DNS_A) ?? [],
        ));
    }

    /**
     * Performs query for the A records of a domain
     *
     * @return string[][] Response from dns_get_record()
     */
    public function query(Blacklist $blacklist): bool|null
    {
        $listed = $blacklist->isListed();

        $this->listed ??= false ?: $listed;

        $this->blacklists[] = $blacklist;

        return $listed;
    }

    public function isListed(): bool|null
    {
        return $this->listed ?? null;
    }
}
