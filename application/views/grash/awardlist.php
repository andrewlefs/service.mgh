<?php

use GraphShare\Object\Values\BaseLinks;
use GraphShare\Object\Fields\DBTableFields;

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
    <div class="award_title">Vật phẩm đã nhận</div>
    <div class="col-xs-12 list_event friend">
        <div class="list_event">
            <ul>
                <li id="ls-share" data-open="closed" class="ls-award" data-bind="shared">
                    <div class="col-xs-12">
                        <div class="col-xs-10">
                            <div class="ls-title">Share nhận quà (<?php echo count($shareLists) ?>)</div>                             
                        </div>  
                        <div class="col-xs-2">
                            <img class="img-award img-up" style="display: none" data-bind="shared-up" data-class="img-up" src="<?php echo BaseLinks::BASE_ASSET_URL ?>/images/up.png" />  
                            <img class="img-award img-down"  data-class="img-down" data-bind="shared-down" src="<?php echo BaseLinks::BASE_ASSET_URL ?>/images/down.png" /> 
                        </div>  
                    </div>                       
                    <div class="clear"></div>                       
                </li> 
                <?php
                if($shareLists)
                foreach ($shareLists as $key => $value) {
                    ?>
                    <li class="awars-item" data-bind="row-shared" style="display: none"> 
                        <div class="col-xs-12">                             
                            <span class="lsa-message"><?php echo $value[DBTableFields::MESSAGE] ?></span>
                            <span class="lsa-span date"><?php echo date("H:i d/m/Y", strtotime($value[DBTableFields::CREATE_DATE])); ?></span>
                        </div>
                        <div class="clear"></div>   
                    </li>
                    <?php
                }
                ?>

                <li id="ls-invite" data-open="closed" class="ls-award" data-bind="invited">
                    <div class="col-xs-12">
                        <div class="col-xs-10 col-left">
                            <div class="ls-title">Mời bạn nhận quà (<?php echo count($inviteLists) ?>)</div>                            
                        </div>  
                        <div class="col-xs-2">
                            <img class="img-award img-up" style="display: none" data-bind="invited-up" data-class="img-up" src="<?php echo BaseLinks::BASE_ASSET_URL ?>/images/up.png" />  
                            <img class="img-award img-down"  data-class="img-down" data-bind="invited-down" src="<?php echo BaseLinks::BASE_ASSET_URL ?>/images/down.png" /> 
                        </div>    
                    </div>        

                    <div class="clear"></div>                       
                </li>  
                <?php
                if($inviteLists)
                foreach ($inviteLists as $key => $value) {
                    ?>
                    <li class="awars-item" data-bind="row-invited" style="display: none"> 
                        <div class="col-xs-12">                             
                            <span class="lsa-message"><?php echo $value[DBTableFields::MESSAGE] ?></span>
                            <span class="lsa-span date"><?php echo date("H:i d/m/Y", strtotime($value[DBTableFields::CREATE_DATE])); ?></span>
                        </div>
                        <div class="clear"></div>   
                    </li>
                    <?php
                }
                ?>
                <li id="ls-accept" data-open="closed" class="ls-award" data-bind="accepted">
                    <div class="col-xs-12">
                        <div class="col-xs-10 col-left">
                            <div class="ls-title">Nhận quà bạn mời (<?php echo count($acceptLists) ?>)</div>                            
                        </div>  
                        <div class="col-xs-2">
                            <img class="img-award img-up" style="display: none" data-bind="accepted-up" data-class="img-up" src="<?php echo BaseLinks::BASE_ASSET_URL ?>/images/up.png" />  
                            <img class="img-award img-down"  data-class="img-down" data-bind="accepted-down" src="<?php echo BaseLinks::BASE_ASSET_URL ?>/images/down.png" /> 
                        </div>
                    </div>                       
                    <div class="clear"></div>                       
                </li>  
                <?php
                if($acceptLists)
                foreach ($acceptLists as $key => $value) {
                    ?>
                    <li class="awars-item" data-bind="row-accepted" style="display: none"> 
                        <div class="col-xs-12">   
                            <span class="lsa-span byacc">từ <a href="#" class=""><?php echo $value[DBTableFields::NAME] ?></a></span>
                            <span class="lsa-message"><?php echo $value[DBTableFields::MESSAGE] ?></span>
                            <span class="lsa-span date"><?php echo date("H:i d/m/Y", strtotime($value[DBTableFields::CREATE_DATE])); ?></span>
                        </div>
                        <div class="clear"></div>   
                    </li>
                    <?php
                }
                ?>
                <li id="ls-like" data-open="closed" class="ls-award" data-bind="liked">
                    <div class="col-xs-12">
                        <div class="col-xs-10 col-left">
                            <div class="ls-title">Like (<?php echo count($likeLists) ?>)</div>                        
                        </div>
                        <div class="col-xs-2">
                            <img class="img-award img-up" style="display: none" data-bind="liked-up" data-class="img-up" src="<?php echo BaseLinks::BASE_ASSET_URL ?>/images/up.png" />  
                            <img class="img-award img-down"  data-class="img-down" data-bind="liked-down" src="<?php echo BaseLinks::BASE_ASSET_URL ?>/images/down.png" /> 
                        </div>
                    </div>                       
                    <div class="clear"></div>                       
                </li>  
                <?php
                if($likeLists)
                foreach ($likeLists as $key => $value) {
                    ?>
                    <li class="awars-item" data-bind="row-liked" style="display: none"> 
                        <div class="col-xs-12">                             
                            <span class="lsa-message"><?php echo $value[DBTableFields::MESSAGE] ?></span>
                            <span class="lsa-span date"><?php echo date("H:i d/m/Y", strtotime($value[DBTableFields::CREATE_DATE])); ?></span>
                        </div>
                        <div class="clear"></div>   
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="note">Chỉ hiển thị vật phẩm trong 3 ngày gần nhất</div>
    <div style="display: inline-block; height: 40px"></div>
    <div class="footer-panel">
        <div class="col-xs-12 friend-footer" id="btn-friend-close" style="border-right: 1px solid #fff;">Đóng</div>                   
        <div class="clear"></div>		
    </div>   
</div>  

<?php
include APPPATH . 'views/grash/footer.php';
?>
