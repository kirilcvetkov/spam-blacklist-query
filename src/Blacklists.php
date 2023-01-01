<?php

namespace SlickSky\DomainBlacklistSpamCheck;

use Exception;

class Blacklists
{
    public const BLACKLISTS = [
        'dnsbl-1.uceprotect.net' => 'UCEPROTECT',
        'dnsbl-2.uceprotect.net' => 'UCEPROTECT',
        'dnsbl-3.uceprotect.net' => 'UCEPROTECT',
        'dnsbl.dronebl.org' => 'DroneBL',
        'dnsbl.sorbs.net' => 'SORBS',
        'zen.spamhaus.org' => 'SpamHaus',
        'bl.spamcop.net' => 'SpamCop.net',
        'list.dsbl.org' => 'DSBL',
    ];
    protected array $blacklists;
    protected array $results;

    public function __construct(public string $domain, ?array $blacklistsReplace = null)
    {
        $this->blacklists = $blacklistsReplace ?? self::BLACKLISTS;
    }

    public function check(): Blacklists
    {
        $this->results = array_map(
            function ($record) {
                foreach ($record->ips() as $ip) {
                    foreach ($this->blacklists as $host => $name) {
                        $blacklist = preg_match('/SpamHaus/i', $name)
                            ? new Spamhaus($host, $name, $ip)
                            : new Blacklist($host, $name, $ip);

                        $ip->isListed($blacklist);
                    }
                }

                return $record;
            },
            (new DomainMxRecords($this->domain))->get()
        );

        return $this;
    }

    public function all()
    {
        if (! isset($this->results)) {
            $this->check();
        }

        return $this->results;
    }

    public function listed(): array
    {
        if (! isset($this->results)) {
            $this->check();
        }

        return array_filter($this->results, function (MxRecord $record) {
            foreach ($record->ips as $mxIp) {
                if ($mxIp->listed) {
                    return $record;
                }
            }
        });
    }
}
