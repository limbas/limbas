<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\dashboard;

use ReflectionClass;

abstract class Widget
{
    /** @var int unique from database */
    protected int $id;
    /** @var string lowercase relevant part of the class name */
    protected string $type;
    /** @var string contains the class name of the specific class */
    protected string $class;
    /** @var bool  specifies whether an associated Javascript class of the same name exists */
    protected bool $hasjs;

    /** @var string the name of the widget that is displayed to the user */
    protected string $name;
    /** @var string the name of the icon that is displayed inside the placeholder */
    protected string $icon;

    /** @var int x position of the widget */
    protected int $x;
    /** @var int y position of the widget */
    protected int $y;
    /** @var int current width of the widget */
    protected int $width;
    /** @var int current height of the widget */
    protected int $height;

    /** @var ?int minimum height of the widget */
    protected ?int $minHeight;
    /** @var ?int maximum height of the widget */
    protected ?int $maxHeight;
    /** @var ?int minimum width of the widget */
    protected ?int $minWidth;
    /** @var ?int maximum width of the widget */
    protected ?int $maxWidth;

    /** @var bool specifies whether the widget can be edited at all */
    protected bool $readonly;
    /** @var bool specifies whether the widget can be resized */
    protected bool $noresize;
    /** @var bool specifies whether the widget can be moved */
    protected bool $nomove;

    /** @var bool specifies whether the widget has options panel and whether the settings icon should be displayed */
    protected bool $hasOptions;
    /** @var array contains all settings of the widget */
    protected array $options;

    /**
     * Widget constructor.
     * @param int $id
     * @param ?array $options
     */
    public function __construct(int $id, ?array $options = [])
    {
        if (empty($options)) {
            $options = ['x' => 0, 'y' => 0, 'height' => 1, 'width' => 1, 'readonly' => false];
        }
        $this->id = $id;

        if (isset($this->minHeight) && $options['height'] < $this->minHeight) {
            $this->height = $this->minHeight;
        } else if (isset($this->maxHeight) && $options['height'] > $this->maxHeight) {
            $this->height = $this->maxHeight;
        } else {
            $this->height = $options['height'] ?? 1;
        }

        if (isset($this->minWidth) && $options['width'] < $this->minWidth) {
            $this->width = $this->minWidth;
        } else if (isset($this->maxWidth) && $options['width'] > $this->maxWidth) {
            $this->width = $this->maxWidth;
        } else {
            $this->width = $options['width'] ?? 1;
        }

        $this->x = intval($options['x']);
        $this->y = intval($options['y']);
        $this->readonly = boolval($options['readonly']);
        if ($this->readonly) {
            $this->noresize = true;
            $this->nomove = true;
        }

        $reflect = new ReflectionClass($this);
        $this->class = $reflect->getShortName();
        $this->type = substr(strtolower($this->class), 6);

        $this->options = $options;
    }


    /**
     * This function returns the basic html structure for a dashboard widget and thereby embeds the widget-specific html.
     * It is called when the dashboard is generated.
     *
     * @return string
     */
    public function render(): string
    {
        return '<div class="grid-stack-item dashboard-widget-' . $this->type . '"  id="gs-item-' . $this->id . '" ' . $this->getAttributeString() . '><div class="grid-stack-item-content">' . $this->getButtonString() . $this->internalRender() . '</div></div>';
    }

    /**
     * Returns all widget and option dependent attributes as html string
     *
     * @return string
     */
    protected function getAttributeString(): string
    {
        $dataAttributes = [];

        $dataAttributes[] = 'data-id="' . $this->id . '"';
        $dataAttributes[] = 'data-type="' . $this->type . '"';
        $dataAttributes[] = 'gs-x="' . $this->x . '"';
        $dataAttributes[] = 'gs-y="' . $this->y . '"';
        $dataAttributes[] = 'gs-w="' . $this->width . '"';
        $dataAttributes[] = 'gs-h="' . $this->height . '"';

        if (isset($this->noresize) && $this->noresize === true) {
            $dataAttributes[] = 'gs-no-resize="true"';
        }
        if (isset($this->nomove) && $this->nomove === true) {
            $dataAttributes[] = 'gs-no-move="true"';
        }
        if (isset($this->minHeight) && !empty($this->minHeight)) {
            $dataAttributes[] = 'gs-min-h="' . $this->minHeight . '"';
        }
        if (isset($this->maxHeight) && !empty($this->maxHeight)) {
            $dataAttributes[] = 'gs-max-h="' . $this->maxHeight . '"';
        }
        if (isset($this->minWidth) && !empty($this->minWidth)) {
            $dataAttributes[] = 'gs-min-w="' . $this->minWidth . '"';
        }
        if (isset($this->maxWidth) && !empty($this->maxWidth)) {
            $dataAttributes[] = 'gs-max-w="' . $this->maxWidth . '"';
        }
        if (isset($this->hasjs) && $this->hasjs === true) {
            $dataAttributes[] = 'data-widget-class="' . $this->class . '"';
        }
        return implode(' ', $dataAttributes);
    }


    /**
     * Returns edit buttons of a widget
     *
     * @return string
     */
    protected function getButtonString(): string
    {
        if (isset($this->readonly) && $this->readonly === true) {
            return '';
        }

        $buttons = [];

        if (isset($this->hasOptions) && $this->hasOptions === true) {
            $buttons[] = '<i class="lmb-icon lmb-cog widget-options" data-widget-options="' . $this->id . '"></i>';
        }

        $buttons[] = '<i class="lmb-icon lmb-erase text-danger widget-delete" data-delete-widget="' . $this->id . '"></i>';


        return implode(' ', $buttons);
    }


    /**
     * This function is responsible for returning the html required for displaying the specific widget.
     * The html is then embedded directly into the basic structure.
     *
     * @return string
     */
    protected abstract function internalRender(): string;


    /**
     * The placeholder represents the widget in the drag and drop area for new widgets.
     *
     * @return string
     */
    public function getPlaceholder(): string
    {
        return '<div class="newWidget card grid-stack-item d-inline-block mb-1 mr-1" data-type="' . $this->type . '" data-name="' . $this->name . '" ' . $this->getAttributeString() . '><div class="card-body grid-stack-item-content"><i class="lmb-icon ' . $this->icon . '"></i> ' . $this->name . '</div></div>';
    }


    /**
     * This function returns the input mask of the options required for the widget as html.
     *
     * @return string
     */
    public function loadOptionsEditor(): string
    {
        return '';
    }
}
