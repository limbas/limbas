<nav>
    <ul class="pagination pagination-sm justify-content-center">
        <li class="page-item <?=($page<=1)?'disabled':''?>">
            <a class="page-link" href="#" data-page="1">
                <i class="lmb-icon lmb-first"></i>
            </a>
        </li>
        <li class="page-item <?=($page<=1)?'disabled':''?>">
            <a class="page-link" href="#" data-page="<?=(($page-1)>1?$page-1:1)?>">
                <i class="lmb-icon lmb-previous"></i>
            </a>
        </li>
        <li class="page-item">
            <input type="text" class="form-control form-control-sm" value="<?=$page.'/'.$maxPage?>" data-cur-page="<?=$page?>" data-max-page="<?=$maxPage?>">
        </li>
        <li class="page-item <?=($page>=$maxPage)?'disabled':''?>">
            <a class="page-link" href="#" data-page="<?=(($page+1)>=$maxPage?$maxPage:$page+1)?>">
                <i class="lmb-icon lmb-next"></i>
            </a>
        </li>
        <li class="page-item <?=($page>=$maxPage)?'disabled':''?>">
            <a class="page-link" href="#" data-page="<?=($maxPage)?>">
                <i class="lmb-icon lmb-last"></i>
            </a>
        </li>
    </ul>
</nav>
