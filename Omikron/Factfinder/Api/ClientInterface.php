<?php

namespace Omikron\Factfinder\Api;

use Omikron\Factfinder\Exception\ResponseException;

/**
 * Interface for communicating with the FACT-Finder API
 *
 * @api
 */
interface ClientInterface
{
    /**
     * Sends HTTP GET request to FACT-Finder. Returns the parsed server response.
     *
     * @param string $endpoint
     * @param array  $params
     *
     * @return array
     * @throws ResponseException
     */
    public function sendRequest(string $endpoint, array $params): array;
}
