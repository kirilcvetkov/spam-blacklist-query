<?php

declare(strict_types=1);

namespace SlickSky\DomainBlacklistSpamCheck;

class MxRecord
{
    public ?string $host;
    public ?string $class;
    public ?int $ttl;
    public ?string $type;
    public ?int $pri;
    public ?string $target;
    public array $ips;

    public function __construct(array $record)
    {
        $this->host = $record['host'] ?? null;
        $this->class = $record['class'] ?? null;
        $this->ttl = $record['ttl'] ?? null;
        $this->type = $record['type'] ?? null;
        $this->pri = $record['pri'] ?? null;
        $this->target = $record['target'] ?? null;
    }

    public function ips(): array
    {
        if (isset($this->ips)) {
            return $this->ips;
        }

        return $this->ips = array_map(function($row) {
            return new MxIp($row['ip'] ?? null);
        }, dns_get_record($this->target, DNS_A) ?? []);
    }
}
