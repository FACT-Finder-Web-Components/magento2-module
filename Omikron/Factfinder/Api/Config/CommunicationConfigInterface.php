<?php

namespace Omikron\Factfinder\Api\Config;

interface CommunicationConfigInterface
{
    public function getChannel(int $scopeId = null): string;

    public function getAddress(): string;

}
