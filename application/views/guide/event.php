<?php
//var_dump(APPPATH . 'views/guide/header.php');die;
//var_dump(file_exists(APPPATH . 'views/guide/header.php'));
//die;
include APPPATH . 'views/guide/header.php';
?>   
<div class="menu">
    <a class="sukien active" href="/onepiece/su-kien">Sự kiện</a>
    <a class="tintuc" href="/onepiece/tin-tuc">Thông báo</a>
    <a class="huongdan" href="/onepiece/huong-dan">Hướng dẫn</a>
</div>
<div class="bottom">
    <div class="sub ing">
        <div class="content">
            <div class="clear"></div>
            <?php
            $datas = get_link_contents("http://data.mobo.vn/home/get_category/421/1");
            $rows = json_decode($datas, true);
            $row = -1;
            ?>
            <input type="hidden" id="count" value="<?php echo count($rows) ?>"/>
            <?php
            foreach ($rows as $key => $value) {
                $row ++;
                //var_dump($value);die;
                ?>
                <div class="ct" data-bind="<?php echo $row ?>" style="<?php echo ($row > 4 ) ? "display:none" : "" ?>">
                    <img src="http://data.mobo.vn<?php echo $value["feature_image"] ?>">
                    <div>
                        <a href="http://game.mobo.vn/onepiece/v/<?php echo $value["original_id"] ?>.html"><?php echo $value["title"] ?></a>
                        <span class="desc">
                            <?php echo $value["description"] ?>
                        </span>
                    </div>
                    <span class="clear"></span>
                </div>		            
                <div class="clear line1"></div>
                <?php
            }
            if (count($rows) == 0) {
                ?>
                <div class="ctlm">
                    <a start="0" related="0" link="huong-dan" update=".update_loadAll" class="" href="#">Đang cập nhật</a>
                </div>
                <?php
            } else if (count($rows) >= 5) {
                ?>
                <div class="ctlm">
                    <a start="0" related="0" link="huong-dan" update=".update_loadAll" class="loadmore loadAll" href="#">Xem thêm</a>
                </div>
                <?php
            }
            ?>            
            <script type="text/javascript">
                $(document).ready(function() {
                    $('.loadmore').click(function() {
                        loadAll(this);
                        return false;
                    });
                });

                function loadAll(obj)
                {
                    var start = parseInt($(obj).attr("start"));
                    for (var i = start + 5; i < (start + 10); i++) {
                        $(".ct[data-bind=" + i + "]").fadeIn();
                    }
                    $(obj).attr("start", start + 5);
                    var max = parseInt($("#count").val());
                    if ((start + 10) >= max) {
                        $(obj).hide();
                    }
                }
            </script>
        </div>
    </div>   
</div>  
<?php
include APPPATH . 'views/guide/footer.php';
?>
