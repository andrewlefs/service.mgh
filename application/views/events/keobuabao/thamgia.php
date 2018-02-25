<div id="tournament" style="margin: auto; width: 288px; font-size: 13px; ">
    <?php
    foreach ($tournament as $key => $value) {
        ?>
        <div style="font-size: 18px; font-weight: bold; color: #C20C0C; margin-top: 10px;">
            <?php echo $value["tournament_name"]; ?>
        </div>
        <div>
            <table style="width: 288px; border: 1px solid #F79646;padding: 4px;margin-top: 10px;" cellpadding="4" cellspacing="0">
                <tr>
                    <td>*Bắt đầu giải đấu:</td>
                    <td style="text-align: right;"><span style="font-weight: bold;"><?php
                            $date = new DateTime($value["tournament_date_start"]);
                            echo $date->format('d-m-Y H:i:s');
                            ?></span></td>               
                </tr>
                <tr>
                    <td>*Kết thúc giải đấu: </td>
                    <td style="text-align: right;"><span style="font-weight: bold;"><?php
                            $date = new DateTime($value["tournament_date_end"]);
                            echo $date->format('d-m-Y H:i:s');
                            ?></span></td>               
                </tr>
            </table>
        </div>

    <?php } ?>   
    <div style="text-align: center; margin-top: 10px;font-size: 12px; text-align: center;">
        <div style="text-align: center;margin-bottom: 10px;">*Ngân Lượng của bạn: <span id="user_point_2" style="color: red; font-weight: bold;"><?php echo $user_point; ?></span></div>
        *Chọn mức cược: 
        <select id="moccuoc_group" name="moccuoc_group" class="span4 validate[required]">
            <?php foreach ($moccuoc_group as $key => $value) { ?>
                <option value="<?php echo $value["id"]; ?>"><?php echo $value["moccuoc_required"]; ?></option>
            <?php } ?>
        </select> Ngân Lượng</div>
    <div style="margin-top: 5px; text-align: center;">
        <table style="width: 288px; padding: 4px;margin-top: 10px;" cellpadding="4" cellspacing="0">
            <tr>
                <td><img src="/mgh2/assets_dev/events/keobuabao/images/bao.png" /></td>
                <td><img src="/mgh2/assets_dev/events/keobuabao/images/bua.png" /></td> 
                <td><img src="/mgh2/assets_dev/events/keobuabao/images/keo.png" /></td>
            </tr>
            <tr>
                <td><input type="radio" name="imgsel" value="bao" /></td>
                <td><input type="radio" name="imgsel" value="bua" checked="checked" /></td>
                <td><input type="radio" name="imgsel" value="keo" /></td>
            </tr>          
        </table>
        <a class="pet-button" href="javascript:;" onclick="join_process()">Đặt cược</a>          
    </div>   
</div>
