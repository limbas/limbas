<?php
# tooltips
$tooltips = array(
    1 => "OK",
    2 => "OK, but will be better to change",
    3 => "Necessary. You can not continue until this function works!",
    4 => "Function or tool does not work or exist, you can install later"
);

# messages
$msgOK = '<span style="color: green; ">' . $tooltips[1] . '</span>';
$msgWarn = '<span style="color: orange; ">' . $tooltips[2] . '</span>';
$msgWarnHeavy = '<span style="color: orange; ">' . $tooltips[4] . '</span>';
$msgError = '<span style="color: red; ">' . $tooltips[3] . '</span>';

# function to insert icons
function insIcon($code=null) {
    global $tooltips;

    if($code) {
        $tooltip = $tooltips[$code];

        return "<td title=\"$tooltip\" style=\"width: 20px;\"><i class=\"lmb-icon lmb-status-$code\"></i></td>";
    } else {
        return "<td style=\"width: 20px;\"></td>";
    }
}