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
}
