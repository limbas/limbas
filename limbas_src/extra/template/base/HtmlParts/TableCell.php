<?php

/**
 * Class TableCell
 * Corresponds to a <td>
 */
class TableCell extends AbstractHtmlPart {

    /**
     * @var AbstractHtmlPart[]
     */
    protected $parts;

    /**
     * @var array the attributes of this td (attrName -> attrVal).
     */
    protected $attributes;

    public function __construct($parts, $attributes) {
        $this->parts = $parts;
        $this->attributes = $attributes;
    }

    public function getAsHtmlArr() {
        $attributesStrArr = array();
        foreach ($this->attributes as $key => $val) {
            $attributesStrArr[] = "{$key}=\"{$val}\"";
        }

        // get html of parts
        $dataCellsHtmlArr = array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getAsHtmlArr();
        }, $this->parts));

        // add opening <td>
        array_unshift($dataCellsHtmlArr, '<td ', join(' ', $attributesStrArr), '>');

        // add closing </td>
        array_push($dataCellsHtmlArr, '</td>');
        return $dataCellsHtmlArr;
    }

    public function getUnresolvedDataPlaceholders() {
        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedDataPlaceholders();
        }, $this->parts));
    }

    public function getUnresolvedSubTemplateElementPlaceholders() {
        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedSubTemplateElementPlaceholders();
        }, $this->parts));
    }

    public function getUnresolvedTemplateGroupPlaceholders() {
        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedTemplateGroupPlaceholders();
        }, $this->parts));
    }

    public function getUnresolvedDynamicDataPlaceholders() {
        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedDynamicDataPlaceholders();
        }, $this->parts));
    }

    public function getAllDynamicDataPlaceholders() {
        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getAllDynamicDataPlaceholders();
        }, $this->parts));
    }

    public function getTableRows() {
        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getTableRows();
        }, $this->parts));
    }

    public function getParts() {
        return $this->parts;
    }

}
