<?php
//var_dump(APPPATH . 'views/event/event_hot/header.php');die;
//var_dump(file_exists(APPPATH . 'views/event/event_hot/header.php'));
//die;
include APPPATH . 'views/event/event_hot/header.php';
?>
<style type="text/css">
    #main-content{
        padding-top: 10px;
    }
</style>  
<style type="text/css">
    .thumb{
        width: 100px;
        height: 61px;
    }
    .media{
        width: 100px;
        height: 61px;
        float: left;
    }
    .media-body{
        float: left;
        width: 80%;
        position: relative;
        height: 70px;
    }
    .clear{
        clear: both;
    }
    .title{
        font-size: 16px;
        position: absolute;
        top: 0px;
        left: 28px;
        width: 100%;
        text-align: left;
        vertical-align: top;
        height: 20px;
        line-height: 20px;
        overflow: auto;
        text-overflow: ellipsis;
        color: #337ab7 !important;
        font-weight: bold;
    }
    .media-content{
        display: block;
        display: -webkit-box;
        max-width: 75%;
        height: 43px;
        margin: 0 auto;
        font-size: 14px;
        line-height: 20px;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 14px;
        position: absolute;
        top: 20px;
        left: 28px;
        text-align: left;
        left: 28px;
        text-align: left;
    }
    .date_news{
        display: block;
        display: -webkit-box;
        max-width: 80%;
        height: 70px;
        margin: 0 auto;
        font-size: 14px;
        line-height: 70px;
        /* float: right; */
        position: absolute;
        right: 0px;
        top: 0px;
    }
</style>
<div id="main-content"  class="col-xs-12">
    <div id="thele">    
        <?php
        $datas = get_link_contents("http://data.mobo.vn/home/get_category/422/1");
        $rows = json_decode($datas, true);
        $row = -1;

        foreach ($rows as $key => $value) {
            $row ++;
            ?>
            <div class="row-menu right-arrow" style="text-align: center; background:none;height: 67px; <?php echo (($row % 2) == 0) ? "" : "" ?> ">
                <div class="media">
                    <a href='/giangma/event/event_hot/view?ids=<?php echo $value["original_id"] ?>&<?php echo http_build_query($_GET) ?>'><img class="thumb" src="http://lk.mobo.vn<?php echo $value["feature_image"] ?>"><?php echo $value["title"] ?></a>
                </div>                
                <div class="media-body"> 
                    <a class="title" href="/giangma/event/event_hot/view?ids=<?php echo $value["original_id"] ?>&<?php echo http_build_query($_GET) ?>"><?php echo $value["title"] ?></a><br>
                    <span class="media-content"><i><?php echo $value["description"] ?></i></span>
                    <span class="date_news"><?php
                        $date = new DateTime($value["publish_time"]);
                        echo $date->format("d-m-Y");
                        ?></span> 
                </div>                
            </div>
            <div class="clear"></div>
            <?php
        }
        ?>
    </div>        
</div>
<?php
include APPPATH . 'views/event/event_hot/footer.php';
?>
