<?php
    if($droplocktab AND $droplockid){
        if($gtab["lock"][$droplocktab] AND $LINK[271]){
            $lock = lock_data_check($droplocktab,$droplockid,$session["user_id"]);
            if($lock["isselflocked"] OR !$lock){
                lock_data_set($droplocktab,$droplockid,$session["user_id"],"unlock");
            }
        }
    }
?>



<script>

    
function newwin(gtabid,ID){
	newwindata=open("main.php?action=gtab_change&ID="+ID+"&gtabid="+gtabid,"datadetail","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=700,height=700");
}

</script>

<FORM ACTION="main.php" METHOD="post" name="form1">
    <input type="hidden" name="action" value="user_lock">

    <div class="p-3">

        <?php

        $stamp = mktime(date("H"),intval(date("i")) + $umgvar["inusetime"],date("s"),date("m"),date("d"),date("Y"));
        $iuse = "'".convert_stamp($stamp)."'";

        $extension["where"][] = "INUSE_USER = ".$session["user_id"]." AND INUSE_TIME > $iuse";

        foreach ($gtab["table"] as $gtabid => $table):
            if(!$gtab["lockable"][$gtabid]){continue;}
            $onlyfield = null;

            if($gfield[$gtabid]["mainfield"]){
                $onlyfield[$gtabid] = array($gfield[$gtabid]["mainfield"]);
            }elseif($gfield[$gtabid]["fieldkey_id"]){
                $onlyfield[$gtabid] = array($gfield[$gtabid]["fieldkey_id"]);
            }

            $extension["order"][] = "INUSE_TIME";
            ######### gresult Abfrage ##########
            $gresult = get_gresult($gtabid,1,null,null,null,$onlyfield,null,$extension);
            if($maxCount = $gresult[$gtabid]["res_count"]): ?>

                <div class="card card-body mb-3">
                <h5><?= $gtab["desc"][$gtabid] ?></h5>

                <?php  for ($i=0;$i<$maxCount;$i++): ?>
                    <div class="row cursor-pointer">
                        <div class="col-sm-1" OnClick="newwin(<?=$gtabid?>,<?=$gresult[$gtabid]["id"][$i]?>)"><?=$gresult[$gtabid]["id"][$i]?></div>
                        <?php if($onlyfield): ?>
                            <div class="col-sm-1" onclick="newwin(<?=$gtabid?>,<?=$gresult[$gtabid]["id"][$i]?>)"><?=$gresult[$gtabid][$onlyfield[$gtabid][0]][$i]?></div>
                        <?php endif; ?>
                        <div class="col-sm-2" OnClick="newwin(<?=$gtabid?>,<?=$gresult[$gtabid]["id"][$i]?>)"><?=get_date($gresult[$gtabid]["INUSE_TIME"][$i],2)?></div>
                        <div class="col-sm-1"><i class="lmb-icon lmb-trash" OnClick="document.location.href='main.php?action=user_lock&droplocktab=<?=$gtabid?>&droplockid=<?=$gresult[$gtabid]["id"][$i]?>'"></i></div>
                    </div>

                <?php endfor;

                $cnt = 1;?>
                </div>
            <?php endif;
            endforeach;
        
        if(!$cnt): ?>
            <div class="card card-body">
                <?=$lang[98]?>
            </div>
        <?php endif; ?>

    </div>
</FORM>


