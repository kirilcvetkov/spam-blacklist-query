<?php

declare(strict_types=1);

namespace SlickSky\SpamBlacklistQuery;

use function array_filter;

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

    public function toArray()
    {
        return array_map(function ($record) {
            return [
                'host' => $record->host,
                'class' => $record->class,
                'ttl' => $record->ttl,
                'type' => $record->type,
                'pri' => $record->pri,
                'target' => $record->target,
                'ips' => array_map(function ($ip) {
                    return [
                        'blacklists' => array_map(function ($blacklist) {
                            return [
                                'listed' => $blacklist->isListed(),
                                'host' => $blacklist->host,
                                'service' => $blacklist->service,
                                'ipReverse' => $blacklist->ipReverse,
                                'hostname' => $blacklist->hostname(),
                            ];
                        }, $ip->blacklists->toArray()),
                        'invalid' => $ip->isInvalid(),
                        'listed' => $ip->isListed(),
                        'ip' => $ip->get(),
                    ];
                }, $record->ips()->toArray()),
            ];
        }, $this->items);
    }
}
