<?php

namespace Omikron\Factfinder\Api\Config;

/**
 * @api
 */
interface CommunicationConfigInterface
{
    public function isChannelEnabled(int $scopeId = null): bool;

    public function getAddress(): string;

    public function getChannel(int $scopeId = null): string;

    public function isPushImportEnabled(int $scopeId = null): bool;
}
