
/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

$(function() {

    $('#userCompare_select').select2();
    
    $('.dropdown-menu').click(function(e) {
        e.stopPropagation();
    });
    
});

function selected(cal, date) {
    eval("document.form1.elements['" + elfieldname + "'].value = date;");
}

function closeHandler(cal) {
    cal.hide();
}

function showCalendar(event, sell, fieldname, value) {
    elfieldname = fieldname;
    var sel = document.getElementById('diagv');
    var cal = new Calendar(true, null, selected, closeHandler);
    calendar = cal;
    cal.create();
    calendar.setDateFormat("%d.%m.%Y");
    calendar.sel = sel;
    if (value) {
        calendar.parseDate(value);
    }
    calendar.showAtElement(sel);
    return false;
}

function delete_user(ID, USER) {
    del = confirm(jsvar["lng_908"]+" \"" + USER + "\" " + jsvar["lng_160"] + "?");
    if (del) {
        document.form1.user_del.value = ID;
        document.form1.action.value = 'setup_user_erg';
        document.form1.submit();
    }
}

function gurefresh(DATA) {
    gu = confirm(jsvar["lng_896"]);
    var id = document.form1.ID.value;
    if (gu) {
        document.location.href = "main_admin.php?action=setup_grusrref&user="+id+"&datarefresh=" + DATA;
    }
}

function lrefresh() {
    link = confirm(jsvar["lng_896"]);
    var id = document.form1.ID.value;
    if (link) {
        document.location.href = "main_admin.php?action=setup_linkref&user="+id;
    }
}

function srefresh() {
    link = confirm(jsvar["lng_899"]);
    var id = document.form1.ID.value;
    if (link) {
        document.location.href = "main_admin.php?action=setup_user_change_admin&ID="+id+"&srefresh=1";
    }
}

function createpass() {
    var x = 0;
    var pass = "";
    var laenge = 8;
    var zeichen = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    while (x != laenge) {
        pass += zeichen.charAt(Math.random() * zeichen.length);
        x++;
    }
    document.form1.elements['userdata[passwort]'].value = pass
}

function send(action) {
    if (action == 'setup_user_neu') {
        document.form1.user_add.value = '1';
    }
    if ((document.form1.elements['userdata[passwort]'].value.length < 5 && document.form1.elements['userdata[passwort]'].value.length > 0) || document.form1.elements['userdata[username]'].value.length < 5) {
        lmbShowWarningMsg(jsvar["lng_1315"]);
    } else {
        document.form1.submit();
    }
}

function newwin1(USERID) {
    tracking = open("main_admin.php?action=setup_user_tracking&typ=1&userid=" + USERID, "Tracking", "toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=600,height=600");
}

//function newwin2(USERID) { //deprecated
//    userstat = open("main.php?action=userstat&userstat=" + USERID, "userstatistic", "toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=750,height=550");
//}

function deleteuserfilesave(evt) {
	link = confirm(jsvar["lng_2328"]);
	if(link) {
        document.form1.delete_user_filesave.value = 1;
        document.form1.submit();
	}
}

function userCompare(userid,compareid){

    $.ajax({
        url: 'main_dyns_admin.php',
        type: 'GET',
        data: {
            'actid' : "userComparePermission",
            'user_id' : userid,
            'compare_id' : compareid
        },
        success: function (data) {
            $('#userCompareModalContent').html(data);

            // tooltips
            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
            const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))

        }
    });

    $('#userCompareModal').modal('show');


}
