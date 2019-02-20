<?php

namespace Omikron\Factfinder\Api\Config;

interface CommunicationConfigInterface
{
    public function getChannel(int $scopeId = null): string;

    public function getAddress(): string;

    public function getDefaultQuery(): string;

    public function isEnabled(int $scopeId = null): bool;

    public function isPushImportEnabled($scopeCode = null): bool;
}
