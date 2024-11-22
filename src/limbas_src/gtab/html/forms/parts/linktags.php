<?php

global $farbschema;
global $gtab;
global $gfield;
global $verknpool;
global $gverkn;
global $action;
global $greminder;


$groupable = $gtab["groupable"][$gtabid];

if(!$groupable) {
    return;
}

?>



<nav class="nav nav-pills nav-fill">

    

    <?php

    //print_linktags

    

    # aus Verknpf Pool
    $verknpool[$gtabid] = null;
    

    # ------- (-) Verknüpfung -----------------------------------------
    if($gfield[$gtabid]["r_verkntabid"] AND $groupable){
        foreach($gfield[$gtabid]["r_verkntabid"] as $key => $value){
            if($value != $gtabid AND $gfield[$value]["verkntabletype"][$gfield[$gtabid]["r_verknfieldid"][$key]] != 2){
                if($gfield[$value]["unique"][$gfield[$gtabid]["r_verknfieldid"][$key]] OR $gfield[$value]["data_type"][$gfield[$gtabid]["r_verknfieldid"][$key]] == 27)
                {$act = "gtab_change";}
                else{$act = "gtab_erg";}


                ?>

                <a class="nav-link" id="tabs_<?=$value?>" OnClick="limbasLinkTags(event,'<?=$value?>','0','<?=$value?>','<?=$gfield[$gtabid]["r_verknfieldid"][$key]?>','<?=$ID?>',2,'<?=$act?>','1','<?=$gtabid?>',0)"><?=$gtab["desc"][$value]?></a>

                <?php


                # --- Verknüpfungs Pool setzen -------------------------
                $verknpool[$gtabid][] = array($value,0,$value,$gfield[$gtabid]["r_verknfieldid"][$key],$ID,2,$act,1,0,'',$gtab["markcolor"][$value]);
            }
        }
    }

    # ------- Aktuelle Tabelle -----------------------------------------
    ?>




    <a class="nav-link active" aria-current="page" href="#" id="tabs_<?=$gtabid?>" onclick="document.form1.action.value='gtab_erg';document.form1.form_id.value=document.form1.formlist_id.value;send_form('1');"><?=$gtab["desc"][$gtabid]?></a>
    
    <?php

    $verknpool[$gtabid][] = array($gtabid,$ID,0,0,0,0,"gtab_change",$GLOBALS["verkn_showonly"],$GLOBALS["form_id"],"");

    # ------- (+) Verknüpfung -----------------------------------------
    if($gverkn[$gtabid]["id"] AND $groupable){
        foreach($gverkn[$gtabid]["id"] as $key => $value){
            if($value != $gtabid AND $gfield[$gtabid]["verkntabletype"][$key] != 2){
                if($gfield[$gtabid]["unique"][$key]){$act = "gtab_change";}else{$act = "gtab_erg";} ?>


                <a class="nav-link" id="tabs_<?=$value?>" OnClick="limbasLinkTags(event,'<?=$value?>','0','<?=$gtabid?>','<?=$gfield[$gtabid]["field_id"][$key]?>','<?=$ID?>',1,'<?=$act?>','1','<?=$gtabid?>',0)"><?=$gtab["desc"][$value]?> (<?=$gresult[$gtabid][$key][0]?>)</a>
                
                
                <?php
                # --- Verknüpfungs Pool setzen -------------------------
                $verknpool[$gtabid][] = array($value,0,$gtabid,$gfield[$gtabid]["field_id"][$key],$ID,1,$act,1,0,$count,$gtab["markcolor"][$value]);
            }
        }
    }

    ?>



</nav>
