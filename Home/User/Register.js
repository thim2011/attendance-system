var myApp={
    SignUp:function(){
       
        if( !myApp.checkInput() ) {
            return false;
       }

        var params = $("#SignUpForm").serialize();
        console.log(params);
        $.post("../../BLL/User/SignUp.php",
            params,
            function(data, status){
                if( status=="success"){	
					if( data.err == 0 ) 
                    {	
                        myApp.showSuccessMsg(data.msg);
                        console.log("SignUp Success");
                        location.href = "Login.php";
						return true;
					} 
                    else 
                    {
                        myApp.showMsg(data.msg);
					}
                    return false;
                } 
                else {
                    myApp.showMsg("儲存時，系統發生錯誤！");
                    return false;
                }
            },
            "json");
    },

    checkInput : function() {
        var szTemp = $("input[name='Email']").val();
        if (szTemp == "" || szTemp.indexOf("@") == -1 || szTemp.indexOf(".") == -1) {
            myApp.showMsg("【Email】不可爲空，需包含‘@’和‘.’符號！");
            return false;
        }
				
		szTemp = $("input[name='Account']").val();
        if (szTemp == "" || szTemp.length < 5) {
            myApp.showMsg("【帳號】不可爲空，需多過五個字元！");
            return false;
        }
        
		szTemp = $("input[name='Password']").val();
        if (szTemp == "" || szTemp.length < 5) {
            myApp.showMsg("【密碼】不可爲空，需多過五個字元！");
            return false;
        }

		szTemp = $("input[name='FirstName']").val();
        if (szTemp == "") {
            myApp.showMsg("請輸入【姓名】！");
            return false;
        }

		szTemp = $("input[name='LastName']").val();
        if (szTemp == "") {
            myApp.showMsg("請輸入【姓名】！");
            return false;
        }

		szTemp = $("input[name='Position']").val();
        if (szTemp == "") {
            myApp.showMsg("請輸入【部門】！");
            return false;
        }

        szTemp = $("input[name='Department']").val();
        if (szTemp == "") {
            myApp.showMsg("請輸入【廠別】！");
            return false;
        }
		
		return true;
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

$(function() {
    $("#SignUpBtn").click( myApp.SignUp );
});
