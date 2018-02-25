<?php
include APPPATH . 'views/events/tulinhdan/header.php';
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
        margin-top: 50px;
        bottom: 0px;
        width: 100%;
        font-style: italic;
        font-size: 18px;
        left: 0px;
        top: 40px;
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

<div class="container">
<div class="deny table">
    <div class="deny cell"><?php echo $message ?></div>    
</div>
</div>

<?php
include APPPATH . 'views/events/tulinhdan/footer.php';
?>

