<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


unset($linkid);
?>

<Script language="JavaScript">


var c_id = new Array();
var c_val = new Array();
var a_col = new Array();
var d_col = new Array();
var maxid = <?=$result_colors['maxid']?>;

<?php
$key = 0;
if($result_colors['id']){
	foreach($result_colors['id'] AS $key => $value) {
		$tkey = "[".$key."]";
		echo "c_id".$tkey." = \"".$result_colors['id'][$key]."\";\n";
		echo "c_val".$tkey." = \"".$result_colors['wert'][$key]."\";\n";
	}
}?>

var maxkey = <?=$key?>;


function cslice(el){
	var cc = null;
	var ar = document.getElementsByTagName('span');
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		if(cc.id.substring(0,5) == 'slice'){
			cc.style.display = 'none';
		}
	}
	document.getElementById(el).style.display='';
}

function sel_color(col){
	document.getElementById('selcolor').style.backgroundColor = col;
	document.form1.selcolor.value = col;
}

function addcolor(col){
    col = col.replace('#','');
    col = '#'+col;
	a_col.push(col);
	maxid = maxid + 1;
	maxkey = maxkey + 1;
	c_id[maxkey] = maxid;
	c_val[maxkey] = col;
	create_list();
}

function delcolor(ev){
	var el = getElement(ev);
	var key = el.id.substring(2,10);
	d_col.push(c_val[key]);
	c_val[key] = 0;
	create_list();
}

function submit_changes(){
	document.form1.add_color.value = a_col.join(';');
	document.form1.del_color.value = d_col.join(';');
	document.form1.submit();
}

// --- Farben -----------------------------------
var color = new Array();
color[1] = "<?=$farbschema['WEB1']?>";
color[2] = "<?=$farbschema['WEB2']?>";
color[3] = "<?=$farbschema['WEB3']?>";
color[4] = "<?=$farbschema['WEB4']?>";
color[5] = "<?=$farbschema['WEB5']?>";
color[6] = "<?=$farbschema['WEB6']?>";
color[7] = "<?=$farbschema['WEB7']?>";
color[8] = "<?=$farbschema['WEB8']?>";
color[9] = "<?=$farbschema['WEB9']?>";
color[10] = "<?=$farbschema['WEB10']?>";


// --- lösche alten Inhalt -----------------------------------
function del_body(el){
	while(el.firstChild){
		el.removeChild(el.firstChild);
	}
}

function getElement(ev) {
	if(window.event && window.event.srcElement){
		el = window.event.srcElement;
	} else {
		el = ev.target;
	}
	return el;
}

function addEvent(el, evname, func) {
	if (el.attachEvent) { // IE
	el.attachEvent("on" + evname, func);
	} else if (el.addEventListener) { // Gecko / W3C
	el.addEventListener(evname, func, true);
	} else {
		el["on" + evname] = func;
	}
}

// ----- Listeneintrag -------
function make_list(kontainer) {
	var td_h = 15;
	
	// ---- Tabelle -------
	var el_tab = document.createElement("table");
    el_tab.className += 'table table-sm';
	var el_body = document.createElement("tbody");
	el_tab.appendChild(el_body);
	
	if(c_id){
	for (var key in c_id){
		if(c_val[key]){
		// ---- Zeilen -------
		var aktuTR = document.createElement("tr");
		el_body.appendChild(aktuTR);
		
		let aktuTD = document.createElement("td");
		
		var txt = c_val[key];
		aktuTD.appendChild(document.createTextNode(txt));
		aktuTR.appendChild(aktuTD);
		
		aktuTD = document.createElement("td");
		aktuTD.style.backgroundColor = c_val[key];
        aktuTD.className += 'w-25';
		aktuTR.appendChild(aktuTD);
		
		aktuTD = document.createElement('td');
        aktuTD.className += 'text-center';
		aktuTD.style.cursor = 'pointer';	
		
                var aktuImg = document.createElement("i");		
                $(aktuImg).addClass('lmb-icon');
                $(aktuImg).addClass('lmb-trash');
		aktuImg.id = "ce"+key;
		addEvent(aktuImg,"click", delcolor);
		aktuTD.appendChild(aktuImg);
		aktuTR.appendChild(aktuTD);
		
		}
	}
	}
	kontainer.appendChild(el_tab);
}

function create_list(){
	del_body(document.getElementById("itemlist_area"));
	make_list(document.getElementById("itemlist_area"));
}

</Script>


<FORM ACTION="<?=$main_action?>" METHOD=post name="form1">
<input type="hidden" name="action" value="<?=$action?>">
<INPUT TYPE="hidden" NAME="del_color">
<INPUT TYPE="hidden" NAME="add_color">



    <div class="container-fluid p-3">
        <div class="row">
            <div class="col-sm-3">
                <div class="card">
                    <div class="card-header">
                        <?=$lang[154]?>
                    </div>
                    <div id="itemlist_area">
                        
                    </div>
                </div>
            </div>
            <div class="col-sm-9 col-md-5 order-first order-sm-last">
                <div class="card">
                    <div class="card-header">
                        <?=$lang[155]?>
                    </div>
                    <div class="card-body">
                        
                        <div class="d-flex gap-5">
                            <?php
                            $bbzm = 1;
                            $b = 0;
                            while($b <= 255){
                                if($b > 0){$disp = "none";}else{$disp = "";}
                                echo "<SPAN ID=\"slice_$bbzm\" STYLE=\"display:$disp\"><TABLE BORDER=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
                                $g = 0;
                                while($g <= 255){
                                    echo "<TR>";
                                    $r = 0;
                                    while($r <= 255){
                                        $rcol = dechex($r);
                                        if(!$rcol){$rcol = "00";}elseif(lmb_strlen($rcol) == 1){$rcol = "0".$rcol;}
                                        $gcol = dechex($g);
                                        if(!$gcol){$gcol = "00";}elseif(lmb_strlen($gcol) == 1){$gcol = "0".$gcol;}
                                        $bcol = dechex($b);
                                        if(!$bcol){$bcol = "00";}elseif(lmb_strlen($bcol) == 1){$bcol = "0".$bcol;}
                                        $col = '#'.lmb_strtoupper($rcol.$gcol.$bcol);
                                        echo "<TD OnClick=\"addcolor('$col');\" OnMouseOver=\"sel_color('$col');\" STYLE=\"height:10px;width:10px;overflow:hidden;cursor:pointer;background-color:$col\"></TD>";
                                        $r += 15;
                                    }
                                    echo "</TR>\n";
                                    $g += 15;
                                }
                                echo "</TABLE></SPAN>\n";
                                $b += 30;
                                $bbzm++;
                            }
                            ?>

                            <TABLE BORDER="0" cellspacing="0" cellpadding="0" STYLE="height:180px;width:20px;border:1px solid grey;">
                                <?php
                                $bbzm = 1;
                                $b = 0;
                                while($b <= 255){
                                    $bcol = dechex($b);
                                    if(!$bcol){$bcol = "00";}elseif(lmb_strlen($bcol) == 1){$bcol = "0".$bcol;}
                                    $col = "0000".$bcol;
                                    echo "<TR><TD STYLE=\"width:20px;height:20px;background-color:#$col;cursor:pointer\" OnMouseOver=\"cslice('slice_$bbzm')\"></TD></TR>";
                                    $b += 30;
                                    $bbzm++;
                                }
                                ?>
                            </TABLE>
                        </div>
                        
                        

                        <DIV><INPUT ID="selcolor" NAME="selcolor" TYPE="TEXT" OnChange="addcolor(this.value);this.style.backgroundColor=this.value;" STYLE="width:180px;height:15px;border:none;background-color:FFFFFF"></DIV>
                    </div>
                    <div class="card-footer text-end">
                        <button type="button" OnClick="submit_changes()" class="btn btn-primary"><?=$lang[33]?></button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    
</FORM>

