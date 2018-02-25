<?php
include APPPATH . 'views/events/tulinhdan/header.php';
?>

        <div class="container">
            <div id="content">
                <div class="children">
                    <div style="margin: 0 auto; font-size: 15px">
                        <br/><br/>
                        <div id="the-le" class="content">
                            <div id="my_content" style="text-align: justify; width: 95%; margin: auto; margin-top: 10px;"></div>
                        </div>

                        <div id="tham-gia" class="content" style="display: none">
                            <div id="tournament_list" style="margin: auto; width: 288px; font-size: 13px; margin-top: 10px;">Chọn giải đấu:
                                <select style="width: 160px" id="tournament_id_1" name="tournament_id_1" class="span4 validate[required]" /></select></div>
                            <div id="team_list"></div>
                        </div>

                        <div id="lich-su" class="content" style="display: none; text-align: center;">
                            <div id="tournament_list_2" style="margin: auto; width: 288px; font-size: 13px; margin-top: 10px; text-align: left;">Chọn giải đấu:
                                <select style="width: 160px" id="tournament_id_2" name="tournament_id_2" class="span4 validate[required]" /></select></div>
                            <div id="history-content"></div>
                        </div>

                        <div id="get-top" class="content" style="display: none; text-align: center;">                           
                            <div id="tournament_list_3" style="margin: auto; width: 288px; font-size: 13px; margin-top: 10px; text-align: left;">
                                <table style="width: 100%;">
                                    <tr>
                                        <td>Chọn giải đấu:</td>
                                        <td><select style="width: 160px" id="tournament_id_3" name="tournament_id_3" class="span4 validate[required]" /></select></td>                                       
                                    </tr>                                                                                           
                                </table>                            
                                </div>
                            <div id="top-content"></div>
                        </div>
                        
                        <div id="nap-the" class="content" style="display: none;">
                            <div id="napthe-content" style="text-align: justify; width: 288px; margin: auto; margin-top: 10px;"></div>
                        </div>

                        <div class="clearboth"></div>
                    </div>
                </div>

            </div>
            <div class="clearboth"></div>
        </div>
        <div class="clearboth"></div>
    </div>




<?php
include APPPATH . 'views/events/tulinhdan/footer.php';
?>

