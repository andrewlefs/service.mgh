<div class="col-xs-12">
    <div class="row-menu">
        <div class="col-xs-12">
            <form action="" method="POST">
                <select id="report-select" name="report-select">
                    <?php
                    foreach ($listAction as $key => $value) {
                        ?>
                        <option value="<?php echo $value["action"] ?>" <?php echo $_POST["report-select"] == $value["action"] ? "selected=selected" : "" ?> ><?php echo $value["name"] ?></option>
                        <?php
                    }
                    ?>
                </select>                
                <input type="submit" name="buttonSubmit" value="Xem" >                
            </form>
        </div>
    </div>                  
</div>