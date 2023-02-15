<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace gtab\forms\elements;


use gtab\fields\LMBField;

class DataElement extends LMBFormElement
{
    
    protected LMBField $field;
    
    
    public function __construct(LMBField $lmbField) {
        
        $this->field = $lmbField;
        
        parent::__construct();
    }


    public function render(): string
    {
        return $this->field->renderInput();
    }
    
    public function getId(): int {
        return $this->field->getId();
    }

    public function getType(): string {
        return $this->field->getType();
    }
    
    public function getLabel(): string {
        return $this->field->getLabel();
    }
    
    
    private function checkParams() {
        
        
        
        if($gfield[$gtabid]["grouping"][$key] OR $gfield[$gtabid]["field_name"][$key] == 'LMB_VALIDTO'){
            return;
        }

        # ----------- Viewrule -----------
        # prüfe ob Feld überhaupt angezeigt wird
        if($gfield[$gtabid]["viewrule"][$key]){
            $returnval = eval(trim($gfield[$gtabid]["viewrule"][$key]).";");
            if($returnval){
                return;
            }
        }

        // falls das Feld eine Sparte ist, eröffne neue Unterebene
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

        //gebe alle Felder die kein Tabulator sind normal aus
        if($gfield[$gtabid]["funcid"][$key]){
            if($gfield[$gtabid]["INDIZE"][$key]){$indexed1 = "Indexed: ".$gfield[$gtabid]["INDIZE_TIME"][$key];$indexed2 = " (<B STYLE=\"color:green\">i</B>)";}else{$indexed1 = null;$indexed2 = null;}
            echo "<TR class=\"gtabBodyDetailTR\"><TD class=\"gtabBodyDetailTD\" VALIGN=\"TOP\" TITLE=\"".$gfield[$gtabid]["beschreibung"][$key]."\"";
            if($edittyp){echo " OnClick=\"fieldtype('".$gfield[$gtabid]["data_type"][$key]."','".$gfield[$gtabid]["form_name"][$key]."','$indexed1');\"";}
            echo ">";
            echo "<span style=\"cursor:help;\">".$gfield[$gtabid]["spelling"][$key].$indexed2."<span class=\"gtabBodyDetailNeedTitle\">".lmb_substr($gfield[$gtabid]["need"][$key],0,1)."</span></span>";
            echo "</TD><TD class=\"gtabBodyDetailValTD\">";

            // ------------------ Change-Typfunction ---------------------
            

            echo "</TD></TR>\n";

            $bzm++;
        }
        
        
    }


    
}
