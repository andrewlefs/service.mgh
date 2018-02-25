/**
 * Created by : Ivoglent nguyen.
 * User: longnv
 * Date: 10/18/13
 * Time: 11:37 AM
 * File :
 */
var card_id=1;
var card_name="";
var sms_content="";
var sms_phone="";
$(document).ready(function(){
	$('input[type="text"]').click(function(){
       //showNativeInput($(this).attr('id'),$(this).val());
    });
    $('input[type="password"]').click(function(){
        //showNativeInput($(this).attr('id'),$(this).val());
    });
	
	
	
	
    $("#close_dialog").click(function(){
        hide_dialog();
    });
    $(".sub_ct").click(function(){
       // $(this).children().find('a').first().trigger('click');
    });
    $(".select_payment").click(function(e){
        $(".container_pp").hide();
        var element=$(this).attr('href');
        $(element).css({"display":"block"});
        if($(this).attr('card-type')){
            card_id=$(this).attr('card-type');
        }
        if($(this).attr('card-name')){
            card_name=$(this).attr('card-name');
        }
        if($(this).attr('title')){
            var title=$(this).attr('title');
            if($(this).attr('title')=="Khoá tài khoản"){
                $(".sms_pp").html("Bạn muốn khóa tài khoản???");
            }
            else
                $(".sms_pp").html("Bạn muốn nạp <b id=\"price\">"+$(this).attr('title')+"</b> vào tài khoản?");
        }
        if($(this).attr('data-content')){
            sms_content=$(this).attr('data-content');
        }
        if($(this).attr('data-phone')){
            sms_phone=$(this).attr('data-phone');
        }
        $("#overlay").show();
        return false;
    })
    $("#back_topnav").click(function(){
        back();
    });
    $(".sms_payment").click(function(){
        var content=$(this).attr('title');
        back_e[idx]=$(this);
        idx++;
        if(content!=""){
            mp.sendRequest(POST,Urls.AJAX_URL + "/sms","sms_content="+content,function(response){
                if(response.error==0){
                    showSuccess(response.message);
                }
                else{
                    showError(response.message);
                }
            });
        }
        return false;

    });
    
    $("#result").click(function(){
        $(this).hide();
    });
});
function back(){
        $("#title").html("MoPay");
        $(".payment").hide();
        hideResult();
}
function showError(error){
    $("#pay_result").show();
    $(".sus_pp").show().html(error);
}
function showSuccess(success){
    $("#pay_result").show();
    $(".sus_pp").show().html(success);
}
function hideResult(){
    $("#result").hide();
    $(".rc").hide();
}
function hidePopup(){
    $(".container_pp").hide();
    $("#overlay").hide();
    $(".loading").hide();
}
function popupConfirm(message){

}
function setCard(type){
    card_id=type;
}

function show_dialog(_string){
    $("#dialog").show();
    $("#dialog_message").html(_string);
}
function hide_dialog(){
	console.log('log ne');
    $("#dialog").hide();
    $("#dialog_message").html("");
}

function showNativeInput( textboxId,text)  {
    try{
        console.log(textboxId)
        Android.showNativeInput( textboxId ,text)
    }
    catch(e){
        console.log(e);
    }
}
function setText(textboxId,text){
	console.log("Called : " + textboxId + ","+ text);
    var tb=document.getElementById(textboxId);
    if(typeof (tb)!='undefined')
        tb.value=text;
}

