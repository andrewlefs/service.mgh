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
                            <a href="javascript:void(0);" rel="get-top" class="top-button" onclick="play_process(<?php echo $value["id"]; ?>)">Đấu</a>
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