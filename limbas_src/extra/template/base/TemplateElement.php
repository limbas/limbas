<?php


/**
 * Class TemplateElement
 * Corresponds to a single dataset of a table of type 'report-template'
 */
class TemplateElement {

    /**
     * Regex used to extract placeholders
     */
    const PLACEHOLDER_REGEX = '/\\$\\{([^\{\}]+?)\\}/';

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
     * @var string the name of the template element
     */
    protected $name;

    /**
     * @var array of AbstractHtmlParts which are contained in this element's html
     */
    protected $parts;

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
            return TemplateConfig::$instance->getTemplateElementInstance($templateElementGtabid, $gresult[self::FIELD_NAME][0], $gresult[self::FIELD_HTML][0]);
        }
        return null;
    }

    public function __construct($templateElementGtabid, $name, &$html) {
        $this->templateElementGtabid = $templateElementGtabid;
        $this->name = $name;
        $this->parts = self::parseHtml($html);
    }

    /**
     * Splits the given html into the html parts
     * @param $html
     * @return array of AbstractHtmlPart
     */
    protected static function parseHtml(&$html) {
        require_once('extra/template/parser/searchParserGenerated.php');
        try {
            $parser = new Parser;
            $result = $parser->parse($html);
        } catch (SyntaxError $ex) {
            lmb_log::error($ex->getMessage(), $ex->getMessage());
            return array();
        } catch (Exception $ex) {
            lmb_log::error($ex->getMessage(), $ex->getMessage());
            return array();
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
     * @return bool whether a template was resolved
     */
    public function resolveTemplates() {
        $subElPlaceholders = $this->getUnresolvedSubTemplateElementPlaceholders();
        if (!$subElPlaceholders) {
            return false;
        }
        return $this->resolveTemplatesRec($subElPlaceholders);
    }

    protected function resolveTemplatesRec($subElPlaceholders) {
        $subElPlaceholdersByName = array();
        $gsr = array();
        $i = 0;
        foreach ($subElPlaceholders as &$subElPlaceholder) {
            $name = &$subElPlaceholder->getName();

            # store placeholder by name for later lookup
            if (!array_key_exists($name, $subElPlaceholdersByName)) {
                $subElPlaceholdersByName[$name] = array();
            }
            $subElPlaceholdersByName[$name][] = &$subElPlaceholder;

            # fill gsr
            $gsr[$this->templateElementGtabid][self::FIELD_NAME][$i] = $name;
            $gsr[$this->templateElementGtabid][self::FIELD_NAME]['txt'][$i] = 2 /* equals */;
            $gsr[$this->templateElementGtabid][self::FIELD_NAME]['andor'][$i] = 2 /* or */;

            $i++;
        }

        $filter = array();
        $filter['getlongval'][$this->templateElementGtabid] = true;

        # Filter
        $extension = $this->getExtension();
        $gresult = get_gresult($this->templateElementGtabid, 1, $filter, $gsr, null, null, null, $extension);
        $gresult = &$gresult[$this->templateElementGtabid];

        $recSubElPlaceholders = array();
        $success = false;
        foreach ($gresult[self::FIELD_NAME] as $key => $templateElementName) {
            $templateElement = $this->gresultToTemplateElement($gresult, $key);

            # set the new template element for all placeholders that need it
            foreach ($subElPlaceholdersByName[$templateElementName] as &$subElPlaceholder) {
                $success = true;
                $subElPlaceholder->setTemplateElement($templateElement);
            }

            # get all new sub element placeholders
            $recSubElPlaceholders = array_merge($recSubElPlaceholders, $templateElement->getUnresolvedSubTemplateElementPlaceholders());
        }

        # recursive call
        if (count($recSubElPlaceholders) > 0) {
            return $this->resolveTemplatesRec($recSubElPlaceholders);
        }
        return $success;
    }

    protected function getExtension() {
        return null;
    }

    protected function gresultToTemplateElement(&$gresult, $key) {
        return TemplateConfig::$instance->getTemplateElementInstance($this->templateElementGtabid, $gresult[self::FIELD_NAME][$key], $gresult[self::FIELD_HTML][$key]);
    }

    public function getUnresolvedSubTemplateElementPlaceholders() {
        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedSubTemplateElementPlaceholders();
        }, $this->parts));
    }

    public function getUnresolvedDataPlaceholders() {
        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedDataPlaceholders();
        }, $this->parts));
    }

    public function getName() {
        return $this->name;
    }

}