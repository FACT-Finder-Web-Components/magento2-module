<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

class ComponentBuilder
{
    /**
     * @param string $componentName - name of rootElement
     * @param array  $attributes  - as key/value pairs
     *
     * @return string - xml element as a string
     */
    public function buildComponent(string $componentName, array $attributes): string
    {
        $writer = new \XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startElement($componentName);
        foreach ($attributes as $key => $value) {
            $writer->startAttribute($key);
            $writer->text((string) $value);
            $writer->endAttribute();
        }
        $writer->fullEndElement();

        return $writer->outputMemory();
    }
}
