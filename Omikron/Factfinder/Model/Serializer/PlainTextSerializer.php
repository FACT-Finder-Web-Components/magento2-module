<?php

namespace Omikron\Factfinder\Model\Serializer;

use Magento\Framework\Serialize\SerializerInterface;

class PlainTextSerializer implements SerializerInterface
{
    public function serialize($data)
    {
        throw new \BadMethodCallException('Not implemented');
    }

    public function unserialize($string)
    {
        return ['success' => stripos($string, 'success') !== false];
    }
}
