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
     * @param string $endpoint
     * @param array $params
     * @return array
     * @throws RequestExceptionInterface
     */
    public function sendRequest(string $endpoint, array $params) : array;
}
