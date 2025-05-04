var myApp={
    ForgotPass : function() {
        var email = $('#email').val();
        $.post("../../BLL/User/ForgotPass.php", 
            {email: email}, 
            function(data, status) {
            if( status=="success" ) {
                if( data.err == 0 ) {
                    myApp.SendEmail(email, "Reset", data.token);
                    myApp.showSuccessMsg(data.msg);
                    return true;
                }
                else 
                {
                    myApp.showMsg(data.msg);
                }
            } 
            else 
            {
                myApp.showMsg("系統發生錯誤！");
                return false;
            }
        }, "json");
    },


    SendEmail: function(email ,leave, token) {
        $.post("../../BLL/User/Email.php", 
            {email: email, leave: leave, token: token}, 
            function(data, status) {
            if (status == "success") {
                if (data.err == 0) {
                    console.log(data.msg);
                    return true;
                } else {                   
                }
            } else {
                myApp.showMsg("系統發生錯誤！");
                console.log("系統發生錯誤！");
                return false;
            }
        });
    },
    showMsg : function ( szMsg ) {
        $("#modal-message-text").text(szMsg);
        $('#modal-message').modal('show');   
    },

    showSuccessMsg : function ( szMsg ) {
        $("#modal-message-success").text(szMsg);
        $('#modal-success').modal('show'); 
    }
}

$(document).ready(function(){
    $('#ForgotPass_Btn').click(function(){
        myApp.ForgotPass();
    });

})