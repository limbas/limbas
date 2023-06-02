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

                <h5><a href="main.php?action=gtab_erg&source=root&gtabid=<?=$tabid?>&gfrist=1" ><?=$gtab['desc'][$tabid]?></a></h5>
    
                <?php foreach ($tabData as $remKey => $remval):
                    $reminderFrist = $remdata['frist'][$groupid][$tabid][$remKey];
                    $readableDate = get_date($reminderFrist, 4);
    
                    # reminder in past?
                    $color = '';
                    if ($curDate >= get_stamp($reminderFrist)) {
                        $color = 'style="color:green"';
                    }
                    
                    ?>
                    <div class="row mb-1 cursor-pointer" onclick="document.location.href='main.php?action=gtab_change&gtabid=<?=$tabid?>&ID=<?=$remdata['dat_id'][$groupid][$tabid][$remKey]?>'">

                        <div class='col-sm-2' <?=$color?> ><?=$readableDate?></div>
                        <div class="col-sm-2"><?=$remdata['content'][$groupid][$tabid][$remKey]?></div>
                        <div class="col-sm-2"><?=$userdat['bezeichnung'][$remdata['fromuser'][$groupid][$tabid][$remKey]]?></div>
                        <div class="col-sm-1" onclick="event.preventDefault();event.stopPropagation();return false;">
                            <i class="lmb-icon lmb-trash cursor-pointer" onclick="document.location.href='main.php?action=user_w_vorlage&del_id=<?=$remdata['id'][$groupid][$tabid][$remKey]?>'"></i>
                        </div>
                    </div>
                
                    <?php
    
                    # reminder description
                    if (!empty($remdata['description'][$groupid][$tabid][$remKey])): ?>
                        <div>
                            <?=$remdata['description'][$groupid][$tabid][$remKey]?>
                        </div>
                        <?php
                    endif;
                endforeach;
            endforeach; ?>

    </div>
        
        <?php endforeach;
        
        
    endif;
    ?>
    
    
</div>


