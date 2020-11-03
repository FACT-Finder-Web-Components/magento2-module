<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Api\Config;

/**
 * @api
 */
interface CommunicationConfigInterface
{
    const NG_VERSION = 'ng';

    public function isChannelEnabled(int $scopeId = null): bool;

    public function getAddress(): string;

    public function getChannel(int $scopeId = null): string;

    public function isPushImportEnabled(int $scopeId = null): bool;

    public function getVersion(): string;

    public function isLoggingEnabled(): boolean;
}
