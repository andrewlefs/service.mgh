<?php

use GraphShare\Definition;
use GraphShare\Object\Fields\FriendFields;
use GraphShare\Object\Values\BaseLinks;

include APPPATH . 'views/grash/header.php';
?>

<div class="page_wrapper">
    <div class="col-xs-12 header">        
        <span class="col-xs-3 num1"></span>
        <div class="col-xs-9 account_info">
            <?php if ($isLogin == true) { ?>
                <a href="#"><img class="icon-profile" src="<?php echo $profile["link_picture"] ?>" />
                    <?php echo $profile["fbname"] ?></a>|
                <a href="<?php echo BaseLinks::BASE_HOME_URI ?>/logout?k=<?php echo $key ?>">Đăng xuất</a>
            <?php } else { ?>
                <a href="<?php echo BaseLinks::BASE_HOME_URI ?>/login?k=<?php echo $key ?>">Đăng nhập</a>
            <?php }
            ?>
        </div>
    </div>  
    <div class="col-xs-12 searching">
        <input id="search" type="text" class="search-box" data="row-friend" placeholder="Search" />
        <span class="icon-suggested"></span>
        <div class="friends_paging">
            <select id="paging" class="form-control paging" data="row-friend">
                <option value="0">Suggested Friends</option>
                <?php
                $paging = BaseLinks::PAGING;
                if ($friendLists == true) {
                    $length = count($friendLists) - count($excludeds);
                    $lenpaging = ceil($length / $paging);

                    for ($index = 0; $index < $lenpaging; $index ++) {
                        ?>
                        <option value="<?php echo $index ?>"><?php echo ((($index + 1) * $paging) - $paging) + 1 ?>-<?php echo ($index + 1) * $paging ?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
    </div>
    <div class="note">Lưu ý: mỗi ngày chỉ nhận quà invite <?php echo \GraphShare\Object\Values\InviteRoles::$MAX ?> <?php echo \GraphShare\Object\Values\InviteRoles::$SHORT_TITLE ?></div>
    <div class="col-xs-12 list_event friend">
        <ul style="padding-bottom: 25px">
            <?php
            if ($friendLists == true) {
                //echo $lenpaging;
                $pagingIndex = 0;
                $pageNext = 0;
                //echo count($friendLists);
                foreach ($friendLists as $key => $value) {
                    if ($excludeds == true && in_array($value["token"], $excludeds)) {
                        continue;
                    }
                    if ($pageNext >= $paging) {
                        $pagingIndex++;
                        $pageNext = 0;
                    }
                    $pageNext++;
                    ?> 
                    <li data-token="<?php echo $value[FriendFields::TOKEN] ?>" class="<?php echo $pagingIndex == 0 ? "friend-active" : "friend-disable" ?> row-friend" data-name="<?php echo $value[FriendFields::NAME] ?>" data-name-latin="<?php echo $value[FriendFields::NAME_LATIN] ?>"  id="friend-<?php echo $key ?>" data-bind="<?php echo $value[FriendFields::ID] ?>" page-index="<?php echo $pagingIndex ?>" data-status-choice="unselected">
                        <div class="col-xs-12 friend-item"> 
                            <img class="avatar" src="<?php
                            if ($value[FriendFields::PICTURE] == FALSE) {
                                echo BaseLinks::BASE_ASSET_URL . "/images/default_user.png";
                            } else {                                
                                echo $pagingIndex == 0 ? $value[FriendFields::PICTURE] : "";
                            }
                            ?>" data="<?php echo $value[FriendFields::PICTURE] ?>" />
                            <span class="friend-name"><?php echo $value[FriendFields::NAME] ?></span>
                            <a href="#" class="friend-choice">
                                <span class="UFITouchable"></span>
                                <span class="btn-status-choice UFIUnSelect"></span>                                
                            </a>
                        </div>  
                    </li>             
                    <?php
                }
            }
            ?>

        </ul>
    </div>

    <div class="footer-panel">
        <div class="col-xs-4 friend-footer" id="btn-friend-close" style="border-right: 1px solid #fff;">Đóng</div>
        <div class="col-xs-4 friend-footer" id="btn-friend-invite" style="border-right: 1px solid #fff;">Mời</div>     
        <div class="col-xs-4 friend-footer" id="btn-friend-checkall">Chọn Tất Cả</div>       
        <div class="clear"></div>		
    </div>   
</div>  

<?php
include APPPATH . 'views/grash/footer.php';
?>
