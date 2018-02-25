<?php //
use GraphShare\Object\Values\BaseLinks;
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
    <div class="list_event">
        <ul>
            <li>
                <div class="col-xs-12">
                    <div class="col-xs-9 col-left">
                        <div class="ls-title">Thể lệ tham gia</div>
                        <div class="ls-short">Giới thiệu sự kiện Chia sẻ/Mời bạn Facebook</div>
                    </div>
                    <div class="col-xs-3 col-right">                                
                        <a href="<?php echo BaseLinks::BASE_HOME_URI ."/" . GraphShare\Object\Values\BaseViews::INTRO ?>?k=<?php echo $key ?>" class="button-img "><span class="UFIRuleIcon icon-list"></span>Xem</a>
                    </div>
                </div>                       
                <div class="clear"></div>                           
            </li>  
            <li>
                <div class="col-xs-12">
                    <div class="col-xs-9 col-left">
                        <div class="ls-title">Like</div>
                        <div class="ls-short">Like có cơ hội nhận được phần thưởng hấp dẫn</div>
                    </div>
                    <div class="col-xs-3 col-right">                                
                        <a id="btn-liked" href="#" class="button-img" >                            
                            <img class="like-img" alt="Like Fanpage" src="<?php echo BaseLinks::BASE_ASSET_URL ?>/images/like.gif" />
                        </a>
                    </div>
                </div>                       
                <div class="clear"></div>                       
            </li>  
            <li>
                <div class="col-xs-12">
                    <div class="col-xs-9 col-left">
                        <div class="ls-title">Share nhận quà</div>
                        <div class="ls-short">Chia sẻ Tân Mộng Giang Hồ để nhận quà ngay!</div>
                    </div>
                    <div class="col-xs-3 col-right">                                
                        <a id="btn-share" href="#" class="button-img" ><span class="UFIShareIcon icon-list"></span>Share</a>
                        <span id="share-countdown" class="countdown" style="display: none"></span>
                    </div>
                </div>                       
                <div class="clear"></div>                       
            </li>    
            <li>
                <div class="col-xs-12">
                    <div class="col-xs-9 col-left">
                        <div class="ls-title">Mời bạn nhận quà</div>
                        <div class="ls-short">Mời càng nhiều bạn, nhận càng nhiều quà!</div>
                    </div>
                    <div class="col-xs-3 col-right">                                
                        <a id="btn-invite" href="#" onclick="" class="button-img "><span class="UFIInviteIcon icon-list"></span>Mời</a>
                        <span id="invite-countdown" class="countdown" style="display: none"></span>
                    </div>
                </div>                       
                <div class="clear"></div>                       
            </li>   
<!--            <li>
                <div class="col-xs-12">
                    <div class="col-xs-9 col-left">
                        <div class="ls-title">Nhận quà bạn mời</div>
                        <div class="ls-short">Xác nhận yêu cầu từ bạn bè để nhận quà!</div>
                    </div>
                    <div class="col-xs-3 col-right">                                
                        <a href="<?php echo BaseLinks::BASE_HOME_URI ."/" . GraphShare\Object\Values\BaseViews::ACCEPT ?>?k=<?php echo $key ?>" class="button-img "><span class="UFISaveCodeIcon icon-list"></span>Nhận</a>                        
                    </div>
                </div>                       
                <div class="clear"></div>                       
            </li>-->
            <li>
                <div class="col-xs-12">
                    <div class="col-xs-9 col-left">
                        <div class="ls-title">Phần thưởng đã nhận</div>
                        <div class="ls-short">Danh sách phần thưởng đã nhận.</div>
                    </div>
                    <div class="col-xs-3 col-right">                                
                        <a href="<?php echo BaseLinks::BASE_HOME_URI ."/" . GraphShare\Object\Values\BaseViews::AWARDLIST ?>?k=<?php echo $key ?>" id="giftcode" class="button-img" title=""><span class="UFISaveCodeIcon icon-list"></span>Xem</a>
                    </div>
                </div>                       
                <div class="clear"></div>
            </li>            
        </ul>
    </div>    	
</div>  

<?php
include APPPATH . 'views/grash/footer.php';
?>
