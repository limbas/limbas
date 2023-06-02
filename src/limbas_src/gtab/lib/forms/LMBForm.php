<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\gtab\lib;


use Limbas\gtab\lib\forms\elements\DataElement;
use Limbas\gtab\lib\forms\elements\SectionElement;

class LMBForm
{
    private $formId;

    private $gtabid;

    private $datid;

    private $gresult;
    
    private $formElements;
    
    private $sections;
    
    private $hasData;

    private $readonly;

    public function __construct(int|string $formId)
    {
        $this->formId = $formId;
    }

    public function assignData(int $gtabid, int $datid, array $gresult)
    {
        $this->gtabid = $gtabid;
        $this->datid = $datid;
        $this->gresult = $gresult;
    }

    public function render(bool $readonly = false): string
    {
        $this->readonly = $readonly;
        
        $this->loadFormElements();
        
        if ($this->formId === 'default') {
            return $this->renderDefaultForm();
        }

        return $this->renderCustomForm();
    }

    private function loadFormElements(): void
    {
        global $gfield;
        
        if ($this->formId === 'default' && empty($this->gtabid)) {
            return;
        }

        $this->formElements = [];
        
        $this->sections = [];
        $currentSection = new SectionElement();
        
        foreach ($gfield[$this->gtabid]['sort'] as $key => $value){
            
            $fieldId = $gfield[$this->gtabid]['field_id'][$key];
            
            $field = null; // new LMBField($this->gtabid, $fieldId, $this->gresult);

            $formElement = new DataElement($field);
            
            if($formElement->getType() === 'section') {
                // if first formelement is a section => dont add default section
                if (!empty($this->formElements)) {
                    $this->sections[] = $currentSection;
                }
                $currentSection = new SectionElement($field);
            } elseif($formElement->getType() !== 'section') {
                $currentSection->addChild($formElement);
            }

            $this->formElements[] = $formElement;
            
            

            //display_dftyp($gresult,$gtabid,$key,$ID,$edit,"gtabchange",null,null,$bzm);
            /*
            if($gfield[$gtabid]["grouping"][$key] OR $gfield[$gtabid]["field_name"][$key] == 'LMB_VALIDTO'){continue;}

            # ----------- Viewrule -----------
            if($gfield[$gtabid]["viewrule"][$key]){
                $returnval = eval(trim($gfield[$gtabid]["viewrule"][$key]).";");
                if($returnval){continue;}
            }

            if($gfield[$gtabid]["field_type"][$key] == 100){
                if($filter["groupheader"][$gtabid]){
                    # grouping header with tabs
                    if(!$gf){$gf = $key;}
                    if($gf != $key){$dst = "style=\"display:none\"";}else{$dst = "";}

                    echo "<tr class=\"gtabBodyDetailTR\"><td class=\"gtabBodyDetailTD\" colspan=\"2\">&nbsp;</TD></TR>";
                    echo "</table>";
                    echo "<table id=\"LmbHeaderPart_".$gtabid."_".$key."\" class=\"gtabBodyDetailTab\" $dst width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
                }else{
                    # grouping header with rows
                    echo "<TR class=\"gtabBodyDetailTR\"><TD class=\"gtabBodyDetailTD\" style=\"width:100%\" colspan=\"2\" VALIGN=\"TOP\" TITLE=\"".$gfield[$gtabid]["beschreibung"][$key]."\"><div class=\"gtabGrouping\">".$gfield[$gtabid]["spelling"][$key]."</div></TD></TR>";
                }
            }

            if($gfield[$gtabid]["funcid"][$key]){
                if($gfield[$gtabid]["INDIZE"][$key]){$indexed1 = "Indexed: ".$gfield[$gtabid]["INDIZE_TIME"][$key];$indexed2 = " (<B STYLE=\"color:green\">i</B>)";}else{$indexed1 = null;$indexed2 = null;}
                echo "<TR class=\"gtabBodyDetailTR\"><TD class=\"gtabBodyDetailTD\" VALIGN=\"TOP\" TITLE=\"".$gfield[$gtabid]["beschreibung"][$key]."\"";
                if($edittyp){echo " OnClick=\"fieldtype('".$gfield[$gtabid]["data_type"][$key]."','".$gfield[$gtabid]["form_name"][$key]."','$indexed1');\"";}
                echo ">";
                echo "<span style=\"cursor:help;\">".$gfield[$gtabid]["spelling"][$key].$indexed2."<span class=\"gtabBodyDetailNeedTitle\">".lmb_substr($gfield[$gtabid]["need"][$key],0,1)."</span></span>";
                echo "</TD><TD class=\"gtabBodyDetailValTD\">";

                /* ------------------ Change-Typfunction --------------------- /
                

                echo "</TD></TR>\n";

                $bzm++;
            } */
        }
    }
    
    private function renderDefaultForm(): string
    {        
        $formElements = $this->formElements;
        $sectionElements = $this->formElements;


        $gtabid = $this->gtabid;
        $gresult = $this->gresult;
        $verkn_addfrom = $GLOBALS["verkn_addfrom"];
        $readonly = $this->readonly;
        $showheader = null;
        
        ob_start();
        include(__DIR__ . '/templates/defaultForm.php');
        $output=ob_get_contents();
        ob_end_clean();
        
        if ($output === false) {
            $output = '';
        }
        
        return $output;
    }


    private function renderCustomForm(): string
    {

        return '';
    }

}
