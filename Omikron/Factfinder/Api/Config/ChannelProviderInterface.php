<?php

namespace Omikron\Factfinder\Api\Config;

interface ChannelProviderInterface
{
    public function getChannel(int $scopeId = null): string;

    public function isChannelEnabled(int $scopeId = null): bool;
}
