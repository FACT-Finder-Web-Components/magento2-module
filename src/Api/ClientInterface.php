<?php

declare(strict_types=1);

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
     * Set request headers
     *
     * @param array $headers
     *
     * @return ClientInterface
     */
    public function setHeaders(array $headers): ClientInterface;

    /**
     * Sends HTTP GET request to FACT-Finder. Returns the parsed server response.
     *
     * @param string $endpoint
     * @param array  $params
     *
     * @return array
     * @throws ResponseException
     */
    public function get(string $endpoint, array $params): array;

    /**
     * Sends HTTP POST request to FACT-Finder. Returns the parsed server response.
     *
     * @param string $endpoint
     * @param array  $params
     *
     * @return array
     * @throws ResponseException
     */
    public function post(string $endpoint, array $params): array;
}
