<?php


$curDate = time();
?>

<div class="p-3">
    
    
    <?php if(!$remdata): ?>
    <div class="card card-body">
        <?=$lang[98]?>
    </div>
    <?php else:
    
        foreach ($remdata["id"] as $groupid => $groupData): ?>
            <div class="card card-body mb-3">
            <h4><?=$tabgroup['beschreibung'][$groupid]?></h4>
        
        <?php
    
            foreach ($groupData as $tabid => $tabData): ?>

                <h5><a href="main.php?action=gtab_erg&source=root&gtabid=<?=$tabid?>&category=1" ><?=$gtab['desc'][$tabid]?></a></h5>
    
                <?php foreach ($tabData as $remKey => $remval):
                    $reminderFrist = $remdata['validdate'][$groupid][$tabid][$remKey];
                    $readableDate = get_date($reminderFrist, 4);
    
                    # reminder in past?
                    $class = '';
                    if ($curDate >= get_stamp($reminderFrist)) {
                        $class = 'alert-warning';
                    }
                    
                    ?>
                    <div class="row cursor-pointer alert <?=$class?> mb-1 p-1" onclick="document.location.href='main.php?action=gtab_change&gtabid=<?=$tabid?>&ID=<?=$remdata['dat_id'][$groupid][$tabid][$remKey]?>'">

                        <div class='col-sm-2'  ><?=$readableDate?></div>
                        <div class="col-sm-2"><?=$greminder[ $greminder["argresult_id"][$remdata['category'][$groupid][$tabid][$remKey]] ]["name"][ $remdata['category'][$groupid][$tabid][$remKey] ]?></div>
                        <div class="col-sm-2"><?=$remdata['content'][$groupid][$tabid][$remKey]?></div>
                        <div class="col-sm-2"><?=$userdat['bezeichnung'][$remdata['fromuser'][$groupid][$tabid][$remKey]]?></div>
                        <div class="col-sm-1" onclick="event.preventDefault();event.stopPropagation();return false;">
                            <i class="lmb-icon lmb-trash cursor-pointer" onclick="document.location.href='main.php?action=user_w_vorlage&category=<?=$category?>&valid=<?=$valid?>&del_id=<?=$remdata['id'][$groupid][$tabid][$remKey]?>'"></i>
                        </div>
                    </div>
                
                    <?php
    
                    # reminder description
                    if (!empty($remdata['desc'][$groupid][$tabid][$remKey])): ?>
                        <div class="fst-italic ps-3">
                            <?=$remdata['desc'][$groupid][$tabid][$remKey]?>
                        </div>
                        <?php
                    endif;
                endforeach;
            endforeach; ?>

    </div>
        
        <?php endforeach;
        
        
    endif;
    ?>



<div class="form-check">
  <input class="form-check-input" type="checkbox" value="1" <?php if(!$valid){echo 'checked';}?> onclick="document.location.href='main.php?action=user_w_vorlage&category=<?=$category?>&valid='+this.checked" >
  <label class="form-check-label" for="flexCheckDefault">
    <?=$lang[3052]?>
  </label>
</div>

    
</div>


