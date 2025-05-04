var myApp={
    LogOut : function() {
        sessionStorage.removeItem('auth_token_test');
        console.log("登出成功");
        location.href = '../Index/Index.php';
    },
}