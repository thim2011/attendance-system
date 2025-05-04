var myApp={
    Signin : function() {
        var params = $("#LoginForm").serialize();
        $.post("../../BLL/User/SignIn.php", 
            params, 
            function(data, status) {
            if( status=="success" ) {
                if( data.err == 0 ) {
                    sessionStorage.setItem('auth_token_test', data.message);
                    console.log(data.msg);
                    console.log("登入成功");
                    location.href = '../Index/Index.php';
                    return true;
                }
                else 
                {
                    $('#notice').text(data.msg);
                    console.log(data.msg);
                }
            } 
            else 
            {
                console.log("登入時，系統發生錯誤！");
                return false;
            }
        },
        "json");
    },

}

$(function() {
    $("#LoginBtn").click( myApp.Signin );
});