<?php

namespace Omikron\Factfinder\Api\Config;

interface CommunicationConfigInterface
{
    public function isEnabled(int $scopeId = null): bool;

    public function getAddress(): string;

    public function getChannel(int $scopeId = null): string;

    public function getDefaultQuery(): string;

    public function isPushImportEnabled(int $scopeId = null): bool;
}
