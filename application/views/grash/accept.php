<?php

function vn_remove($str) {
    $unicode = array(
        'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
        'd' => 'đ',
        'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'i' => 'í|ì|ỉ|ĩ|ị',
        'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
        'D' => 'Đ',
        'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
        'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
        'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
        'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
        'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
    );

    foreach ($unicode as $nonUnicode => $uni) {
        $str = preg_replace("/($uni)/i", $nonUnicode, $str);
    }
    return strtolower($str);
}

use GraphShare\Definition;
use GraphShare\Object\Fields\FriendFields;
use GraphShare\Object\Values\BaseLinks;
use GraphShare\Object\Fields\DBTableFields;
use GraphShare\Object\Values\GameApps;

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
        <input id="search" type="text" class="search-box" data="row-accept" placeholder="Search" />
        <span class="icon-suggested"></span>
        <div class="friends_paging">
            <select id="paging" class="form-control paging" data="row-accept">
                <option value="0">Suggested Friends</option>
                <?php
                $paging = BaseLinks::PAGING;
                if ($acceptLists == true) {
                    $length = count($acceptLists);
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
    <div class="note">Lưu ý: mỗi ngày chỉ nhận <?php echo \GraphShare\Object\Values\AcceptRoles::$MAX ?> lần</div>
    <div class="col-xs-12 list_event friend">
        <ul style="padding-bottom: 25px">
            <?php
            if ($acceptLists == true) {
                //echo $lenpaging;
                $pagingIndex = 0;
                $pageNext = 0;
                //echo count($friendLists);
                foreach ($acceptLists as $key => $value) {
                    if ($pageNext >= $paging) {
                        $pagingIndex++;
                        $pageNext = 0;
                    }
                    $pageNext++;
                    ?> 
            <li  data-id="<?php echo $value[DBTableFields::ID] ?>" data-name="<?php echo $value[DBTableFields::NAME] ?>" data-unique="<?php echo $value[DBTableFields::UNIQUE_KEY] ?>" data-exclude-token="<?php echo $value[DBTableFields::EXCLUDED_TOKEN] ?>" data-day="<?php echo $value[DBTableFields::DAY] ?>" class="<?php echo $pagingIndex == 0 ? "friend-active" : "friend-disable" ?> row-accept" data-name-latin="<?php echo vn_remove($value[DBTableFields::NAME]) ?>"  id="friend-<?php echo $key ?>" token="<?php echo md5($value[DBTableFields::ID] .$value[DBTableFields::UNIQUE_KEY]. $value[DBTableFields::EXCLUDED_TOKEN] . $value[DBTableFields::DAY] . GameApps::GAME_SECRET_KEY) ?>"  page-index="<?php echo $pagingIndex ?>" data-status-choice="unselected">
                        <div class="col-xs-12 friend-item"> 
                            <div class="div-avatar"> <img class="avatar" src="<?php
                            if ($value[DBTableFields::LINK_PICTURE] == FALSE) {
                                echo BaseLinks::BASE_ASSET_URL . "/images/default_user.png";
                            } else {
                                echo $value[DBTableFields::LINK_PICTURE];
                            }
                            ?>" /></div>
                            <span class="friend-name"><?php echo $value[DBTableFields::NAME] ?></span>
                            <span class="accept-date"><?php echo date("H:i d/m/Y", strtotime($value[DBTableFields::CREATE_DATE])) ?></span>
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
        <div class="col-xs-12 friend-footer" id="btn-friend-close" style="border-right: 1px solid #fff;">Đóng</div>                   
        <div class="clear"></div>		
    </div>   
</div>  

<?php
include APPPATH . 'views/grash/footer.php';
?>
