<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


/**
 * Class TemplateElement
 * Corresponds to a single dataset of a table of type 'report-template'
 */
class TemplateElement {

    /**
     * Limbas field ids of table type 'report-template'
     */
    const FIELD_NAME = 1;
    const FIELD_HTML = 2;

    /**
     * @var int the gtabid of the table of type 'report-template'
     */
    protected $templateElementGtabid;


    /**
     * @var integer the id of the template element
     */
    protected $id;

    /**
     * @var string the name of the template element
     */
    protected $name;

    /**
     * @var array of AbstractHtmlParts which are contained in this element's html
     */
    protected $parts;

    /**
     * @var integer used for infinite loop detection
     */
    protected $trackRecursion;

    /**
     * Returns the TemplateElement which corresponds to the given dataset id
     * @param $templateElementGtabid
     * @param $templateElementID
     * @return null|TemplateElement
     */
    public static function fromID($templateElementGtabid, $templateElementID) {
        $gresult = get_gresult($templateElementGtabid, 1, null, null, null, null, $templateElementID);
        $gresult = &$gresult[$templateElementGtabid];
        if ($gresult and $gresult['res_count'] > 0) {
            return TemplateConfig::$instance->getTemplateElementInstance($templateElementGtabid, $gresult[self::FIELD_NAME][0], $gresult[self::FIELD_HTML][0], $gresult['id'][0]);
        }
        return null;
    }

    public function __construct($templateElementGtabid, $name, &$html, $id=0, $gtabid=null, $datid=null, $recursion = 0) {
        $this->templateElementGtabid = $templateElementGtabid;
        $this->id = $id;
        $this->name = $name;
        $this->trackRecursion = $recursion;
        // specific dataset?
        if ($gtabid && $datid) {
            $this->parts = TemplateConfig::$instance->forTemporaryBaseTable($gtabid, function() use (&$html) {
                return self::parseHtml($html);
            });
            $this->resolve($datid);
        } else {
            $this->parts = self::parseHtml($html);
        }
    }

    /**
     * Splits the given html into the html parts
     * @param $html
     * @return array of AbstractHtmlPart
     */
    protected static function parseHtml(&$html) {
        require_once(COREPATH . 'extra/template/parser/searchParserGenerated.php');
        try {
            $parser = new Parser;
            $html = preg_replace('/<!--(?!<!)[^\[>].*?-->/', '', $html);
            $result = $parser->parse($html);
        } catch (Exception $ex) {
            if ($ex instanceof SyntaxError) {
                $from = max(0, $ex->grammarOffset - 15);
                $len = min(30, lmb_strlen($html));
                $region = html_entity_decode(lmb_substr($html, $from, $len));
                lmb_log::error($ex->getMessage() . " Near:\n$region", $ex->getMessage() . " Near:\n$region");
            } else {
                lmb_log::error($ex->getMessage(), $ex->getMessage());
            }
            $result = array();
        }
        return $result;
    }

    public function getAsHtmlArr() {
        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getAsHtmlArr();
        }, $this->parts));
    }

    /**
     * Resolve all SubTemplateElementPlaceholders to their corresponding TemplateElements
     * @param int $depth
     * @return bool whether a template was resolved
     */
    public function resolveTemplates($depth = -1) {
        $subElPlaceholders = $this->getUnresolvedSubTemplateElementPlaceholders();
        if (!$subElPlaceholders) {
            return false;
        }
        return $this->resolveTemplatesRec($subElPlaceholders,$depth);
    }

    protected function resolveTemplatesRec($subElPlaceholders,$depth,$reccount=0) {

        $subElPlaceholdersByTabId = [
            $this->templateElementGtabid => []
        ];

        /** @var SubTemplateElementPlaceholder $subElPlaceholder */
        foreach ($subElPlaceholders as $subElPlaceholder) {
            $tabId = $subElPlaceholder->getTabId($this->templateElementGtabid);
            if (!array_key_exists($tabId,$subElPlaceholdersByTabId)) {
                $subElPlaceholdersByTabId[$tabId] = [];
            }
            $subElPlaceholdersByTabId[$tabId][] = $subElPlaceholder;
        }


        $recSubElPlaceholders = array();
        $success = false;

        $templateElementGtabids = array_keys($subElPlaceholdersByTabId);
        foreach($templateElementGtabids as $templateElementGtabid) {

            $subElPlaceholders = $subElPlaceholdersByTabId[$templateElementGtabid];
            
            $subElPlaceholdersByName = array();
            $gsr = array();
            $i = 0;
            foreach ($subElPlaceholders as &$subElPlaceholder) {
                $name = $subElPlaceholder->getName();

                # store placeholder by name for later lookup
                if (!array_key_exists($name, $subElPlaceholdersByName)) {
                    $subElPlaceholdersByName[$name] = array();
                }
                $subElPlaceholdersByName[$name][] = &$subElPlaceholder;

                # fill gsr
                $gsr[$templateElementGtabid][self::FIELD_NAME][$i] = $name;
                $gsr[$templateElementGtabid][self::FIELD_NAME]['txt'][$i] = 2 /* equals */;
                $gsr[$templateElementGtabid][self::FIELD_NAME]['andor'][$i] = 2 /* or */;

                $i++;
            }

            $filter = array();
            $filter['getlongval'][$templateElementGtabid] = true;

            # Filter
            $extension = $this->getExtension();
            $gresult = get_gresult($templateElementGtabid, 1, $filter, $gsr, null, null, null, $extension);
            $gresult = &$gresult[$templateElementGtabid];


            foreach ($gresult[self::FIELD_NAME] as $key => $templateElementName) {
                //TODO: $templateElement = $this->gresultToTemplateElement($gresult, $key, ( $this->trackRecursion + $reccount + 1 ) );

                # set the new template element for all placeholders that need it
                /** @var SubTemplateElementPlaceholder $subElPlaceholder */
                foreach ($subElPlaceholdersByName[$templateElementName] as &$subElPlaceholder) {
                    $success = true;

                    $templateElement = $subElPlaceholder->createTemplateElement($templateElementGtabid, $gresult[self::FIELD_NAME][$key], $gresult[self::FIELD_HTML][$key], $gresult['id'][$key], ( $this->trackRecursion + $reccount + 1 ));

                    # get all new sub element placeholders
                    $recSubElPlaceholders = array_merge($recSubElPlaceholders, $templateElement->getUnresolvedSubTemplateElementPlaceholders());
                }
            }
            
        }
        

        # recursive call
        if ($depth != -1) {
            $depth--;
        }
        if (is_array($recSubElPlaceholders) && !empty($recSubElPlaceholders) && ($depth > 0 || $depth == -1) ) {
            return $this->resolveTemplatesRec($recSubElPlaceholders,$depth,($reccount+1));
        }
        return $success;
    }

    protected function getExtension() {
        return null;
    }

    //TODO:
    /*protected function gresultToTemplateElement(&$gresult, $key) {
        return TemplateConfig::$instance->getTemplateElementInstance($this->templateElementGtabid, $gresult[self::FIELD_NAME][$key], $gresult[self::FIELD_HTML][$key], $gresult['id'][$key]);
    }*/

    public function getUnresolvedSubTemplateElementPlaceholders() {
        $placeholders = array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedSubTemplateElementPlaceholders();
        }, $this->parts));

        $recursion = $this->getRecursionTrack();
        
        return array_filter($placeholders, function(&$placeholder) use ($recursion) {
            if ($placeholder->getName() === $this->name && $recursion > 1) {
                //lmb_log::error("Found recursion: {$this->name} contains {$this->name} again", "Found recursion: {$this->name} contains {$this->name} again");
                return false;
            }
            return true;
        });
    }

    public function getUnresolvedDataPlaceholders() {
        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedDataPlaceholders();
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

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getRecursionTrack() {
        return $this->trackRecursion;
    }

    public function getParts() {
        return $this->parts;
    }

    public function setParts($parts) {
        $this->parts = $parts;
    }
}
