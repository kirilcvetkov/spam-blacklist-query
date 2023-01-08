<?php

declare(strict_types=1);

namespace SlickSky\SpamBlacklistQuery;

use function array_map;
use function dns_get_record;
use function filter_var;

use const DNS_MX;
use const FILTER_FLAG_HOSTNAME;
use const FILTER_VALIDATE_DOMAIN;

class Domain
{
    /** @throws Exception */
    public function __construct(protected string $name, public Config|null $config = null)
    {
        $this->config ??= new Config();

        if (filter_var($this->name, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== $this->name) {
            throw new Exception('Invalid domain.');
        }
    }

    public function query(): Result
    {
        return new Result(array_map(
            function ($record) {
                foreach ($record->ips() as $ip) {
                    foreach ($this->config->blacklists as $host => $name) {
                        $ip->query(
                            Blacklist::load($host, $name, $ip),
                        );
                    }
                }

                return $record;
            },
            $this->getMxRecords(),
        ));
    }

    /**
     * Retrieves IPs associated with the MX server of a domain
     *
     * @return string[] List of MX servers.
     */
    protected function getMxRecords(): array
    {
        return array_map(
            static fn ($mx) => new MxRecord($mx),
            dns_get_record($this->name, DNS_MX) ?: [],
        );
    }
}
