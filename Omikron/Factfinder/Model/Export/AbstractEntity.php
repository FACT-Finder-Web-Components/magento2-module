<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export;

use Omikron\Factfinder\Api\Export\ExportEntityInterface;

abstract class AbstractEntity implements ExportEntityInterface
{
    /** @var array */
    protected $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function toArray(array $attributes = []): array
    {
        if (!$attributes) return $this->data;
        $data = array_combine($attributes, array_fill(0, count($attributes), ''));
        return array_merge($data, array_intersect_key($this->data, $data));
    }
}
