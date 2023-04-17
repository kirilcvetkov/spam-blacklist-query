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
            if ($mxRecord->isListed()) {
                return true;
            }

            foreach ($mxRecord->ips() as $mxIp) {
                if (empty($mxIp->isListed())) {
                    continue;
                }

                return true;
            }
        }

        return false;
    }

    public function listedOnly(): Result
    {
        return new Result(array_filter(
            $this->items,
            static function (MxRecord $record) {
                if ($record->isListed()) {
                    return $record;
                }

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
        return array_map(fn($record): array => [
            'host' => $record->host,
            'target' => $record->target,
            'class' => $record->class,
            'ttl' => $record->ttl,
            'type' => $record->type,
            'pri' => $record->pri,
            'isListed' => $record->isListed(),
            'blacklists' => array_map(fn($blacklist): array => [
                'host' => $blacklist->host,
                'hostname' => $blacklist->hostname(),
                'service' => $blacklist->service,
                'ipReverse' => $blacklist->ipReverse,
                'isListed' => $blacklist->isListed(),
                'responseTime' => $blacklist->responseTime,
            ], $record->blacklists->toArray()),
            'ips' => array_map(fn($ip): array => [
                'ip' => $ip->get(),
                'invalid' => $ip->isInvalid(),
                'blacklists' => array_map(fn($blacklist): array => [
                    'host' => $blacklist->host,
                    'hostname' => $blacklist->hostname(),
                    'service' => $blacklist->service,
                    'ipReverse' => $blacklist->ipReverse,
                    'isListed' => $blacklist->isListed(),
                    'responseTime' => $blacklist->responseTime,
                ], $ip->blacklists->toArray()),
                'isListed' => $ip->isListed(),
            ], $record->ips()->toArray()),
        ], $this->items);
    }
}
