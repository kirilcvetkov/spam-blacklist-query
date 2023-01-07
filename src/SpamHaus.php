<?php

declare(strict_types=1);

namespace SlickSky\SpamBlacklistQuery;

use function array_merge;
use function dns_get_record;
use function ip2long;

use const DNS_A;

class SpamHaus extends Blacklist
{
    public const RETURN_CODES = [
        'IP Blocklists' => ['127.0.0.0', '127.0.0.255'],        // Spamhaus IP Blocklists
        'Domain Blocklists' => ['127.0.1.0', '127.0.1.255'],    // Spamhaus Domain Blocklists
        'Zero Reputation' => ['127.0.2.0', '127.0.2.255'],      // Spamhaus Zero Reputation Domains list
        // 'Error' => ['127.255.255.0', '127.255.255.255'],        // ERRORS (not implying a "listed" response)
    ];
    public Collection $response;

    public function isListed(): bool
    {
        if (isset($this->listed)) {
            return $this->listed;
        }

        foreach ($this->query() as $record) {
            foreach (self::RETURN_CODES as $name => $range) {
                if ($this->ipInRange($record['ip'], $range[0], $range[1])) {
                    $this->response = new Collection(array_merge($record, ['listed' => $name]));

                    return $this->listed = true;
                }
            }
        }

        return $this->listed = false;
    }

    /**
     * Query A records from the SpamHaus
     *
     * @return string[][] Response from dns_get_record()
     */
    public function query(): array
    {
        return dns_get_record($this->hostname(), DNS_A) ?: [];
    }

    public function ipInRange(string $ip, string $rangeStart, string $rangeEnd): bool
    {
        $ip   = ip2long($ip);
        $low  = ip2long($rangeStart);
        $high = ip2long($rangeEnd);

        return $low <= $ip && $ip <= $high;
    }
}
