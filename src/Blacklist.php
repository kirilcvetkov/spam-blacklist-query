<?php

declare(strict_types=1);

namespace SlickSky\SpamBlacklistQuery;

use function checkdnsrr;
use function preg_match;

class Blacklist
{
    protected bool $listed;
    public float $responseTime;

    public function __construct(public string $host, public string $service, public string $ipReverse)
    {
    }

    public function query(): array|bool
    {
        $start = microtime(true);

        $response = checkdnsrr($this->hostname(), 'A');

        $this->responseTime = round(microtime(true) - $start, 4);

        return $response;
    }

    public function isListed(): bool
    {
        if (isset($this->listed)) {
            return $this->listed;
        }

        return $this->listed = $this->isActuallyListed($this->query());
    }

    public function hostname(): string
    {
        return $this->ipReverse . '.' . $this->host . '.';
    }

    /**
     * Some DNSBL (e.g. SpamHaus) respond еven if the IP isn't listed.
     * For example, the response can be an error code for hitting a throttle limit.
     *
     * @return bool Is the response actually listed
     */
    public function isActuallyListed(bool $isListed): bool
    {
        if ($isListed === false) {
            return $isListed;
        }

        $record = dns_get_record($this->hostname(), DNS_A) ?: [];

        if (empty($record) || empty($record['ip']) || ! filter_var($record['ip'], FILTER_VALIDATE_IP)) {
            return false;
        }

        $ip   = ip2long($record['ip']);
        $low  = ip2long('127.255.255.0');
        $high = ip2long('127.255.255.255');

        return $low <= $ip && $ip <= $high;
    }
}
