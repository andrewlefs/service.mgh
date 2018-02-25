<script>
    $(document).ready(function () {
        $("#moccuoc_group").change(function () {
             $(".loading").show();
                $.ajax({
                    url: "/mgh2/event/keobuabao/thachdau_reload?moccuoc_group=" + $("#moccuoc_group").val(),
                    type: "GET",
                    contentType: 'application/json; charset=utf-8'
                })
                        .done(function (data) {                           
                            $('#wysiwyg-content').html(data);
                            $(".loading").hide();

                        }).fail(function (data) {
                    $('#wysiwyg-content').html('Không thể thách đấu, vui lòng thử lại sau.');
                    $(".loading").hide();
                });
        });
    });
</script>
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
    <div id="wysiwyg-content" style="margin-top: 5px; text-align: center;">
        <?php if (count($join_history) > 0) { ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nhân Vật</th>
                        <th>Thưởng</th>
                        <th></th>
                    </tr>
                </thead>  
                <tbody>
                    <?php
                    $date_now = date('Y-m-d H:i:s');
                    foreach ($join_history as $key => $value) {
                        $play_date_start = date('Y-m-d H:i:s', strtotime($value["play_date_start"]));
                        $play_date_end = date('Y-m-d H:i:s', strtotime($value["play_date_end"]));
                        ?>
                        <tr>
                            <td><div style="font-weight: bold;"><?php echo $value["char_name"]; ?> - Server: <?php echo $value["server_id"]; ?></div>
                                <div id="timer">                          
                                    <?php if (strtotime($date_now) <= strtotime($play_date_end)) { ?>                          
                                        Thời gian kết thúc: <div id="count_down" style="font-size: 10px; font-weight: bold; color: #ff0000; "> <?php echo date('d-m-Y H:i:s', strtotime($value["play_date_end"])); ?></div>
                                    <?php } ?>
                                </div></td>
                            <td><span id="span_point_<?php echo $value["id"]; ?>"><?php echo $value["point_cuoc"]; ?></span></td>
                            <td>
                                <?php if (strtotime($date_now) >= strtotime($play_date_end)) { ?>
                                    <a href="javascript:void(0);" rel="get-top" class="top-button disable">Kết Thúc</a>
                                <?php } else if (strtotime($date_now) < strtotime($play_date_start)) { ?>
                                    <a href="javascript:void(0);" rel="get-top" class="top-button nonopen">Chưa Tới</a>
                                <?php } else { ?>
                                    <a href="javascript:void(0);" rel="get-top" class="top-button" onclick="play_process('<?php echo $value["char_name"]; ?>', <?php echo $value["id"]; ?>)">Đấu</a>
                                <?php } ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>  <?php } else { ?>   
            <table class="table table-bordered table-striped">
                <tr>
                    <td>Không có lượt đặt cược</td>
                </tr>
            </table>
        <?php } ?>
    </div>   
</div>
