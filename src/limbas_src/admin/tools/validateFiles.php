<?php

require_once (COREPATH . 'extra/explorer/filestructure.lib');


function lmb_validateFilesExists(){
    global $db;
    global $gtab;
    global $gmimetypes;
    global $filestruct;

    get_filestructure();

    if($GLOBALS['gtab']['multitenant'][$GLOBALS['gtab']['argresult_id']['LDMS_FILES']]){
        $sql = 'LDMS_FILES.LMB_MID';
    }

    $sqlquery = "SELECT LDMS_FILES.ID, LDMS_FILES.LEVEL, LDMS_FILES.ERSTDATUM, LDMS_FILES.SECNAME, LDMS_FILES.NAME, LDMS_FILES.MIMETYPE FROM LDMS_FILES ORDER BY ERSTDATUM DESC";
    $rs = lmbdb_exec($db,$sqlquery);

    $mid = null;
    $bzm = 0;
    $bzm1 = 0;
    while(lmbdb_fetch_row($rs)) {

        if($GLOBALS['gtab']['multitenant'][$GLOBALS['gtab']['argresult_id']['LDMS_FILES']]){
            $mid = lmbdb_result($rs,'LMB_MID');
        }

        $file = lmb_getFilePath(lmbdb_result($rs,'ID'),lmbdb_result($rs,'LEVEL'),lmbdb_result($rs,'SECNAME'),$gmimetypes['ext'][lmbdb_result($rs, 'MIMETYPE')],$mid);

        if(!file_exists($file)){
            $LID = lmbdb_result($rs,'LEVEL');
            $level = $filestruct["level"][$LID];
            $path = implode('/',array_reverse(lmb_getPathFromLevel($level,$LID)));
            echo "<b>".lmbdb_result($rs,"NAME").'</b> - '.str_replace(UPLOADPATH,'',$file) .'<br>';
            echo lmbdb_result($rs,"ERSTDATUM").' - <i>'.$path.'</i>';
            echo "<br><br>";

            $bzm1++;
        }

        $bzm++;
    }

    echo "<hr>missing files: <b>$bzm1 / $bzm </b>";

}