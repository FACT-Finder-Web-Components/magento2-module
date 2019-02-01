<?php

declare(strict_types = 1);

namespace Omikron\Factfinder\Api;

/**
 * @api
 * Interface for communicating with the FACT-Finder API
 */
interface ClientInterface
{
    /**
     * Sends HTTP GET request to FACT-Finder. Returns the server response.
     *
     * @param string $apiName
     * @param string $paramsQuery
     * @return string
     */
    public function sendToFF(string $apiName, string $paramsQuery) : string;

    /**
     * Triggers an FACT-Finder import on the pushed data
     *
     * @param string $storeId
     * @return bool
     */
    public function pushImport(string $storeId) : bool;

    /**
     * Update trackingProductNumber field role
     *
     * @param string $storeId
     * @return array
     */
    public function updateFieldRoles(string $storeId) : array;
}
