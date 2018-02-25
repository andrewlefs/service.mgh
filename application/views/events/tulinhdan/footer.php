    <div class="loading">
        <img src="/mgh2/assets/events/tulinhdan/images/loading.gif"/>
    </div>
    <div id="questioninfo" style="display: none; cursor: default">
        <h3 id="mess_quest" style="margin: 15px; font-size: 13px;"></h3>

        <div class='controlnumber'>
        </div>
        <div class="controlbutton">
            <input type="button" id="yes_pet" value="Có"/>
            <input type="button" id="no" class="btn-close" value="Không"/>

            <div class="checknumber"></div>
        </div>
    </div>
    <div class="modal-marker bg_popup" style="display: none" onclick="closePopup();"></div>
    <div class="modal-dialog bg_popup" style="display: none">
        <div class="modal-header">
            <i class="ico-warning"></i>
            THÔNG BÁO
            <i class="ico-close pull-right" onclick="closePopup();"></i>
        </div>
        <div class="modal-content content_error mess_content">
            Nội dung thông báo
        </div>
        <div class="modal-footer">
            <p style="cursor: pointer" onclick="closePopup();">QUAY LẠI</p>
        </div>
    </div>


</div>
</body>
</html>
