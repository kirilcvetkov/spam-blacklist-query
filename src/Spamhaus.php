<?php

namespace SlickSky\DomainBlacklistSpamCheck;

class Spamhaus extends Blacklist
{
    public const RETURN_CODES = [
        'IP Blocklists' => ['127.0.0.0', '127.0.0.255'],        // Spamhaus IP Blocklists
        'Domain Blocklists' => ['127.0.1.0', '127.0.1.255'],    // Spamhaus Domain Blocklists
        'Zero Reputation' => ['127.0.2.0', '127.0.2.255'],      // Spamhaus Zero Reputation Domains list
        // 'Error' => ['127.255.255.0', '127.255.255.255'],        // ERRORS (not implying a "listed" response)
    ];
    public array $result = [];

    public function parse(): bool
    {
        foreach (dns_get_record($this->dnsHost(), DNS_A) ?: [] as $record) {
            foreach (self::RETURN_CODES as $name => $range) {
                if ($this->ipInRange($record['ip'], $range)) {
                    $this->result = array_merge($record, ['listed' => $name]);

                    return true;
                }
            }
        }

        return false;
    }

    public function ipInRange($ip, $range)
    {
        $ip = ip2long($ip);
        $low = ip2long($range[0]);
        $high = ip2long($range[1]);

        return $low <= $ip && $ip <= $high;
    }
}
