<?php

declare(strict_types=1);

namespace SlickSky\SpamBlacklistQuery;

class Config
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

    public function __construct(public array|null $blacklists = null)
    {
        $this->blacklists ??= self::BLACKLISTS;
    }
}
