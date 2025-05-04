var myApp={
    ResetPassword : function() {
        var params = $('#reset_form').serialize();
        $.post("../../BLL/User/ResetPassword.php", 
            params, 
            function(data, status) {
            if( status=="success" ) {
                if( data.err == 0 ) {
                    myApp.SendEmail(data.email, "SuccessReset");
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


    SendEmail: function(email ,leave) {
        $.post("../../BLL/User/Email.php", 
            {email: email, leave: leave}, 
            function(data, status) {
            if (status == "success") {
                if (data.err == 0) {
                    console.log(data.msg);
                    location.href = "Login.php";
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
    $('#ResetPassword').click(function(){
        myApp.ResetPassword();
    });

})