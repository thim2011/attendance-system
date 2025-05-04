var myApp = {
    LeaveCheck: function() {
        var params = $("#search").serialize();
        $.post("../../BLL/Leave/LeaveList.php", params, function(data, status) {
            if (status === "success") {
                if (data.err === 0) {
                    console.log(data.result);
                    $("#tbody").empty();
                    
                    if (!data.result) {
                        $('#tbody').append("<tr><td colspan='6'><h2>無資料</h2></td></tr>");
                        return;
                    }

                    data.result.forEach(record => {
                        // Tạo hàng dữ liệu chính
                        var leave_row = $('<tr class="main-row"></tr>')
                            .append($('<td style="color: #1E90FF !important"></td>').text(record.Leave_id))
                            .append($("<td></td>").text(record.Employee_name))
                            .append($("<td></td>").text(record.Leave_type))
                            .append($("<td></td>").text(record.Start_date))
                            .append($("<td></td>").text(record.End_date))
                            

                        var statusText;
                        switch (record.Status) {
                            case "Pending":
                                statusText = '<span class="btn btn-outline-warning">待審核</span>';
                                break;
                            case "Accepted":
                                statusText = '<span class="btn btn-outline-success"><i class="fa-solid fa-check" style="color: #63E6BE;"></i> 通過</span>';
                                break;
                            case "Rejected":
                                statusText = '<span class="btn btn-outline-danger"><i class="fa-solid fa-times" style="color: #F03E3E;"></i> 拒絕</span>';
                                break;
                            default:
                                statusText = '<span class="btn btn-outline-primary">完成</span>';
                        }
                        leave_row.append($("<td></td>").html(statusText));

                        if (record.Status === "Pending") {
                            var actionCell = $("<td></td>")
                                .append($("<button></button>").text("同意").addClass("btn btn-success btn-sm").click(function() {
                                    $('#CheckModal').modal('show');
                                    $('#confirmAccept').data({leaveId: record.Leave_id, Employee_id: record.Employee_id, action: 'Accepted'});
                                }))
                                .append($("<button></button>").text("拒絕").addClass("btn btn-danger btn-sm").click(function() {
                                    $('#modal-RejectReason').modal('show');
                                    $('#Reject-Btn').data({leaveId: record.Leave_id, Employee_id: record.Employee_id, action: 'Rejected'});
                                }));
                            leave_row.append(actionCell);
                        } else {
                            leave_row.append($("<td></td>"));
                        }
                        
                        // Tạo hàng chi tiết
                        var detail_row = $("<tr class='detail-row'></tr>")
                            .append($("<td colspan='7'></td>")
                                .append($('<div class="detail-container"></div>')
                                    .append($('<div class="detail-summary"></div>')
                                        .html(`<span class="detail_span">總天數:</span><strong>${record.Total_day}日</strong><br>
                                                <span class="detail_span">假別：</span><strong>${record.Leave_type}</strong><br>
                                                <span class="detail_span">審核人：</span><strong>${record.VerifyBy}</strong><br>
                                                <span class="detail_span">原因:</span><strong>${record.Reason}</strong>`))
                                    .append($('<div class="detail-info"></div>')
                                        .html(`<strong>請假時段</strong><br>`)
                                        .append(record.details.map(item => 
                                            $(`<span>${item.Date}: ${item.Start_time} ~ ${item.End_time}</span><br>`)
                                        ))
                                    )
                                )
                            );

                        $("#tbody").append(leave_row, detail_row);
                    });

                } else {
                    myApp.showMsg(data.msg);
                }
            } else {
                myApp.showMsg("系統發生錯誤！");
            }
            // Ẩn phần tử loading sau khi dữ liệu đã được tải xong
            $('#loading').hide();
        }, "json");
    },
    
    Verifytbtn: function(id, status, Employee_id){
        var VerifyBy = $("#userid").val();
        $.post("../../BLL/Leave/VerifyLeaves.php", 
            {Leave_id   : id, 
             Status     : status, 
             Employee_id :Employee_id,
             VerifyBy   : VerifyBy},
            function(data, status) {
            if( status=="success" ) {
                if( data.err == 0 ) {
                    myApp.SendEmail(data.email,"VerifyLeaves");
                    myApp.showSuccessMsg(data.msg);
                    myApp.LeaveCheck();
                    myApp.SessionCheck();
                    return true;
                } 
                else{
                    myApp.showMsg(data.msg);
                }
            }
            else 
            {
                myApp.showMsg("系統發生錯誤！");
                return false;
            }
        },
    "json");
    },

    RejectLeave: function(id, status, Employee_id, VerifyBy, VerifyReason){
       
        $.post("../../BLL/Leave/RejectLeave.php",
            {   Leave_id   : id,
                Status     : status,
                Employee_id :Employee_id,
                VerifyBy   : VerifyBy,
                VerifyReason     : VerifyReason},
            function(data, status) {
                if( status=="success" ) {
                    if( data.err == 0 ) {
                        myApp.SendEmail(data.email,"RejectLeaves");
                        myApp.showSuccessMsg(data.msg);
                        myApp.LeaveCheck();
                        myApp.SessionCheck();
                        return true;
                    }
                    else{
                        myApp.showMsg(data.msg);
                    }
                }
                else 
            {
                myApp.showMsg("系統發生錯誤！");
                return false;
            }
            },
            "json");
    },
                    

    SendEmail: function(email ,leave) {
        $.post("../../BLL/User/Email.php", 
            {email: email, leave: leave}, 
            function(data, status) {
            if (status == "success") {
                if (data.err == 0) {
                   
                    myApp.showSuccessMsg(data.msg);
                    return true;
                } else {
                
                    console.log(data.msg);
                }
            } else {
                myApp.showMsg("系統發生錯誤！");
                console.log("系統發生錯誤！");
                return false;
            }
        });
    },



    SessionCheck: function () {
        $.post("../../Utils/SessionCheck.php",
            function(data, status) {
                if( status=="success" ) {
                    console.log(data);
                    $('#Noti_count').text(data.Noti);
                    $('#nav_noti_count').text(data.Noti);
                    $('#PendingLeave').text(data.Pending);
                    return true;
                }
            },
            "json");
    },

    Openbtn: function(){
        $('.sidebar').addClass('active');
    },
    Closebtn: function(){
        $('.sidebar').removeClass('active');
    },

    SwitchTheme: function(){
        $('body').toggleClass('dark');
        let mode = $('body').hasClass('dark') ? "dark" : "";
        localStorage.setItem('mode', mode);
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



$(document).ready(function() {
    $('#loading').show();//固定function
   
    //setInterval(myApp.LeaveCheck, 30000);
    myApp.LeaveCheck();
    $('#search-btn').click(myApp.LeaveCheck);

    $('#confirmAccept').click(function() {
        var leaveId = $(this).data('leaveId');
        var action = $(this).data('action') || 'Accepted';
        var Employee_id = $(this).data('Employee_id');
        console.log(leaveId, action, Employee_id);
        if(action == 'Accepted'){
            myApp.Verifytbtn(leaveId, action, Employee_id);
        }
       
        $('#CheckModal').modal('hide');
    });
    
    $('#Reject-Btn').click(function() {
        var leaveId = $(this).data('leaveId');
        var action = $(this).data('action') || 'Rejected';
        var Employee_id = $(this).data('Employee_id');
        var Reason = $('#Reject_Reason').val();
        var VerifyBy = $("#userid").val();
        myApp.RejectLeave(leaveId, action, Employee_id, VerifyBy, Reason);
        
        $('#modal-RejectReason').modal('hide');
    });

    $(document).on('click', '.main-row', function() {
        $(this).next('.detail-row').toggle();
    });
    
    //固定function
    myApp.SessionCheck();
    $('.open-btn').click(myApp.Openbtn);
    $('.close-btn').click(myApp.Closebtn);
    $('#sw-checkbox').change(myApp.SwitchTheme);
    $('#loading').hide(); 
});