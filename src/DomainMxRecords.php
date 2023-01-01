<?php

declare(strict_types=1);

namespace SlickSky\DomainBlacklistSpamCheck;

use Exception;

use function array_map;
use function dns_get_record;
use function filter_var;

use const DNS_MX;
use const FILTER_VALIDATE_DOMAIN;
use const FILTER_FLAG_HOSTNAME;

class DomainMxRecords
{
    public function __construct(protected string $domain)
    {
    }

    /**
     * Retrieves IPs associated with the MX server of a domain
     *
     * @return string[] List of MX servers.
     *
     * @throws Exception
     */
    public function get(): array
    {
        if (! filter_var($this->domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            throw new Exception('Invalid domain.');
        }

        $records = array_map(function ($mx) {
            return new MxRecord($mx);
        }, dns_get_record($this->domain, DNS_MX));

        if (empty($records)) {
            throw new Exception('Unable to find any MX records.');
        }

        return $records;
    }
}
