/**
 * Created by : Ivoglent nguyen.
 * User: longnv
 * Date: 10/21/13
 * Time: 2:06 PM
 * File :
 */
//Constant
var POST= 1,GET=0;
//Urls for ajax request 
var Urls={
    AJAX_URL : base_url + 'ajax'
};
var Configs={

};
//Global variables for each working session
var Common={
    USER_IDLE:true
};
//Common methods
(function( mp, $, undefined ) {
    var _boots=new Array();
    mp.init=function(){

    },
        mp.registerBoot=function(component){
            $(document).bind('ready',component);
        },
        mp.sendRequest=function(type,_url,_data,_callback,_sync){
            var _type="GET";
            var res="";
            if(type==POST) _type="POST";
            try{
                 $.ajax({
                    url: _url,
                    dataType: "JSON",
                    data: _data,
                    type : _type,
                    async:_sync,
                    timeout:30000,
                    success: function(data){
                        res= data;
                        if(_callback) _callback(data);

                    },
                    error: function(x, t, m) {
                        if(t==="timeout") {
                            show_dialog("Hệ thống bận. Vui lòng thử lại");
                        } else {
                             
                        }
                    }
                });
            }
            catch (e){
                return null;
                log(e);
            }
            return res;

        };
    mp.PushState=function(title,url){
        try{
            window.history.pushState('page',title,url);
        }
        catch (ex){

        }
    };

}( window.mp = window.mp || {}, jQuery ));