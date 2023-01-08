<?php

declare(strict_types=1);

namespace SlickSky\SpamBlacklistQuery;

use function array_filter;
use function array_map;

class Result extends Collection
{
    public function isListed(): bool
    {
        foreach ($this->items as $mxRecord) {
            foreach ($mxRecord->ips() as $mxIp) {
                if (empty($mxIp->isListed())) {
                    continue;
                }

                return true;
            }
        }

        return false;
    }

    public function listed(): Result
    {
        return new Result(array_filter(
            $this->items,
            static function (MxRecord $record) {
                foreach ($record->ips() as $mxIp) {
                    if (! $mxIp->isListed()) {
                        continue;
                    }

                    return $record;
                }
            },
        ));
    }

    /**
     * Convert objects into an array
     *
     * @return string[][][] Array of the objects' values
     */
    public function toArray(): array
    {
        return array_map(static fn($record): array => [
            'host' => $record->host,
            'class' => $record->class,
            'ttl' => $record->ttl,
            'type' => $record->type,
            'pri' => $record->pri,
            'target' => $record->target,
            'ips' => array_map(static fn($ip): array => [
                'blacklists' => array_map(static fn($blacklist): array => [
                    'listed' => $blacklist->isListed(),
                    'host' => $blacklist->host,
                    'service' => $blacklist->service,
                    'ipReverse' => $blacklist->ipReverse,
                    'hostname' => $blacklist->hostname(),
                ], $ip->blacklists->toArray()),
                'invalid' => $ip->isInvalid(),
                'listed' => $ip->isListed(),
                'ip' => $ip->get(),
            ], $record->ips()->toArray()),
        ], $this->items);
    }
}
