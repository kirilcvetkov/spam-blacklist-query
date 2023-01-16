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

    /** @throws Exception */
    public function query(): Result
    {
        $records = $this->getMxRecords();

        if (empty($records)) {
            throw new Exception('No MX records found for domain.');
        }

        return new Result(array_map(
            function ($record) {
                // DNSBL URI
                foreach ($this->config->blacklistsUri as $host => $service) {
                    $record->query(
                        new Blacklist($host, $service, $this->name)
                    );
                }

                // DNSBL IP
                foreach ($record->ips() as $ip) {
                    foreach ($this->config->blacklistsIp as $host => $service) {
                        $ip->query(
                            new Blacklist($host, $service, $ip->reverse())
                        );
                    }
                }

                return $record;
            },
            $records,
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
