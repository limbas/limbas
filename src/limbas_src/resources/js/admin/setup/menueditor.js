/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

function lmb_loadMenueditor(el,id,parent,act) {

    let linkid = $('[data-linkid]').data('linkid');

    container = 'lmbMainContainer';
    if(!id){id = '';}
    if(!act){act = '';}
    if(el){
        actid = "menuEditor&id="+id+"&linkid="+linkid+"&act="+act+"&parent="+parent+"&menudetail[name]="+el.value;
    }else{
        actid = "menuEditor&id="+id+"&linkid="+linkid+"&act="+act+"&parent="+parent;
    }

    ajaxGet(null,"main_dyns_admin.php",actid,null,function(data) {
        document.getElementById(container).innerHTML = data;
    },"form11");
    if($('.ui-dialog').length) {
        $('#lmbDetailContainer').dialog("destroy").remove();
    }

}


function lmb_loadMenueDetail(id,act){
    if(!id){id='';}
    if(!act){act='';}

    let linkid = $('[data-linkid]').data('linkid');

    actid = "menuEditor&id="+id+"&detail=1&linkid="+linkid+"&act="+act;

    ajaxGet(null,"main_dyns_admin.php",actid,null, function(data){
        if($('.ui-dialog').length) {
            document.getElementById('lmbDetailContainer').innerHTML = data;

        }else {

            $("<div id='lmbDetailContainer'></div>").html(data).dialog({
                title: 'Menupunkt',
                width: 500,
                height: 350,
                resizable: false,
                modal: true,
                zIndex: 10,
                close: function () {
                    $('#lmbDetailContainer').dialog("destroy").remove();
                }
            });

        }
    },"form11");
}
