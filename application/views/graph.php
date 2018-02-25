<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">                     
        <style type="text/css">
            .container-narrow,.loading,.dialog{
                position: absolute;
                top: 50%;
                transform: translateY(-50%) translateX(-50%);
                left: 50%;
                z-index: 9999;    
                -webkit-transform: translateX(-50%) translateY(-50%);
                -ms-transform: translateX(-50%) translateY(-50%);
                -o-transform: translateX(-50%) translateY(-50%);
                -moz-transform: translateX(-50%) translateY(-50%);
            }
            .loading,.dialog{
                //display: none;
            }
            .overplay{
                position: fixed;
                left: 0;
                top: 0;
                right: 0;
                bottom: 0;
                z-index: 99;
                background-color: #A9A9A9;
                opacity: 0.3;
                display: none;
            }
            .active{
                display: block !important;
            }
            .dialog{
                width: 50%;
                min-width: 280px;
                background-color: #e9eaed;
                border-radius: 13px;
                position: fixed;
            }
            .d-header{
                width: 100%;
                text-align: center;
                background-color: #133783;
                color: #fff;
                height: 30px;
                line-height: 30px;
                border-radius: 13px 13px 0px 0px;
                border: 1px solid #133783;
            }
            .d-title{

            }
            .d-body{
                border: 1px solid #133783;
                height: 73px;
                color: #696969;
                background-color: #e9eaed;
                border-bottom: 0px;
                border-top: 0px;
                width: 100%;
            }
            .d-footer{
                height: 45px;
                border: 1px solid #133783;
                color: #696969;
                background-color: #e9eaed;
                border-top: 0px;
                text-align: center;
                position: relative;
                width: 100%;
                border-radius: 0px 0px 10px 10px;
                border-top: 1px solid #dcdee3;
            }
            .d-img-close{
                width: 22px;
                position: absolute;
                right: 4px;
                top: 4px;
            }
            .d-message{
                position: absolute;
                top: 50%;
                transform: translateY(-50%) translateX(-50%);
                -webkit-transform: translateX(-50%) translateY(-50%);
                -ms-transform: translateX(-50%) translateY(-50%);
                -o-transform: translateX(-50%) translateY(-50%);
                -moz-transform: translateX(-50%) translateY(-50%);
                left: 50%;
                width: 100%;
                text-align: center;
                padding: 0px 10px;
            }
            .btn-confirm,.btn-confirm:hover,btn-confirm:active,btn-confirm:visited{
                background-color: #133783;
                color: #fff;
                padding: 5px;
                width: 85px;
                border-radius: 2px;
                position: absolute;
            }
            .d-confirm{
                top: 50%;
                left: 50%;
                transform: translateY(-50%) translateX(-50%);
                -webkit-transform: translateX(-50%) translateY(-50%);
                -ms-transform: translateX(-50%) translateY(-50%);
                -o-transform: translateX(-50%) translateY(-50%);
                -moz-transform: translateX(-50%) translateY(-50%);
            }
        </style>
    </head>
    <body>        
        <div class="overplay"></div>        
        <div id="dialog" class="dialog">
            <div class="d-header">                
                <span class="d-title">Thông báo</span>  
<!--                <img class="d-img-close" src="/moba/img/close_btn.png"></img>-->
            </div>
            <div class="d-body">
                <div id="d-message" class="d-message">
                    <p style="width: 100%;    white-space: normal;    /* text-overflow: ellipsis; */    overflow-y: auto;"><?php echo $link ?></p>
                    <?php echo $message ?>
                </div>
            </div>
            <div class="d-footer">
                <a href="<?php echo $link ?>" class="btn-confirm d-confirm">Đồng ý</a>
            </div>
        </div> 
    </body>
</html>
