<?php
include $controller->getPathView() . '/header.php';
include $controller->getPathView() . '/navigation.php';
?>
<style type="text/css">
    #main-content{
        padding-top: 10px;
    }
    .deny.table{
        text-align: center;
        color: red;
        height: 100%;
        vertical-align: middle;        
        bottom: 0px;
        width: 100%;
        left: 0px;
        top: 45px;
        display: table;
        min-height: 100%;
        justify-content: center;
        align-content: center;
        overflow: hidden;
        background-color: transparent;
    }
    .deny.cell{
        display: table-cell;
        vertical-align: middle;
    }
</style>      
<div class="deny table">
    <div class="deny cell"><?php echo $message ?></div>    
</div>        
<?php
include $controller->getPathView() . '/footer.php';
?>
