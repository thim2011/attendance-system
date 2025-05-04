<?php 
    require_once dirname(__FILE__).'/../Utils/Main.php';
    require_once dirname(__FILE__).'/../DAL/NotificationDAL.php';

    $notificationDAL = new NotificationDAL();

    $userId= $_SESSION['USERid'];
    $NotificationList = $notificationDAL->getNotification($userId);
    
?>
<style>
 .read-Btn{
 position: absolute;
 top: 1px;
 right: 1px;
 font-size: 13px;
 text-decoration: underline;
 }
.notread-bg{
    background-color:#bbdde7;
}
.notread-bg:hover{
    background-color:#8dcadd;
}
.readed-bg{
    background-color:#EBECED;
}
.readed-bg:hover{
    background-color:#b6b6b7;
}
</style>

<div class="NotificationBox rounded px-2 py-3" id="NotificationBox">
    <div class="d-flex justify-content-between">
        <h3>訊息<span class="text-light text-center bg-danger rounded-circle" id="Noti_count"><?php echo isset($_SESSION['notification']) ? $_SESSION['notification'] : 0 ?></span></h3>
        <button class="btn close-btn" id="Notification-Close-Btn"><span><i class="fa-solid fa-times"></i></span></button>
    </div>
    <div style="overflow: scroll; max-height:500px">
        <?php 
        foreach($NotificationList as $value){       
            if($value->Mes_type == "Punch"){
                $value->Mes_type = "打卡訊息";
            }else{
                $value->Mes_type = "請假訊息";
            }
            ?>
            <div class="alert text-start <?php echo $value->Status == "unread" ? "notread-bg" : "readed-bg" ?> px-2 py-2 m-0" role="alert">
                <?php 
                    if($value->Status == "unread"){
                        echo '<button class="btn read-Btn" data-noti-id="'. $value->Notification_id .'">設為已讀</button>';
                    }
                ?>
                
                <h6 class="fw-bold"><?php echo $value->Mes_type ?></h6>
                <p class="my-0"><?php echo html_entity_decode($value->Message)?></p>
                <p class="mb-0 text-secondary" style="font-size: 13px !important"><?php echo $value->Created_at ?></p>

            </div>
        <?php  }?>
    </div>
</div>
<script>
    $(document).ready(function() {
        localStorage.setItem('Noti-open','Close');  
        if(localStorage.getItem('Noti-open') === "Open") {
            $("#NotificationBox").show();
            $('body').addClass('Noti-open');
        } else {
            $("#NotificationBox").hide();
        }

        $("#Notification-Btn").on('click', function() {
            
            if(localStorage.getItem('Noti-open') === "Open") {
                localStorage.setItem('Noti-open', "Close");
                $("#NotificationBox").hide();
               
            } else if(localStorage.getItem('Noti-open') === "Close") {
                localStorage.setItem('Noti-open', "Open");
                $("#NotificationBox").show();
            }
        }); 

        $(".read-Btn").on('click', function() {
            var Noti_id = $(this).data('noti-id');
            var $btn = $(this);
            let userId = $('#userid').val();
            $.post('../../BLL/Home/ReadNotification.php', 
                {Noti_id: Noti_id, userId: userId}, 
                function(data, status) {
                if( status=="success" ) {
                    if( data.err == 0 ) {
                        console.log(data.msg);
                        myApp.SessionCheck();
                        var alertDiv = $btn.closest(".alert");
                        alertDiv.removeClass("notread-bg").addClass("readed-bg"); 
                        $btn.remove();
                    } 
                    else {
                        console.log(data.msg);
                    }
                }
                else {
                    console.log("error");
                }
            }, "json");
        });

        $('#Notification-Close-Btn').on('click', function() {
            localStorage.setItem('Noti-open', "Close");
            $("#NotificationBox").hide();
        });
    });

</script>