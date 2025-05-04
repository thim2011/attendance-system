var myApp = {
    UpdateClock: function() {
        var now = new Date();
        var hours = now.getHours();
        var minutes = now.getMinutes();
        var seconds = now.getSeconds();

        hours = hours < 10 ? '0' + hours : hours;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;

        var time = hours + ':' + minutes + ':' + seconds;
        document.getElementById('clock').innerHTML = time;
    },

    PunchIn: function() {
        var userid = $("#userid").val();
        console.log(userid);
        $.post("../../BLL/Home/PunchIn.php", 
            {userid : userid}, 
            function(data, status) {
            if( status=="success" ) {
                if( data.err == 0 ) {
                    myApp.showSuccessMsg(data.msg);
                    myApp.TodayRecord();
                    myApp.SendEmail(data.email, "PunchIn");
                    return true;
                }
                else 
                {
                    myApp.showMsg(data.msg);
                    console.log(data.msg);
                }
            } 
            else 
            {
                myApp.showMsg("系統發生錯誤！");
                console.log("登入時，系統發生錯誤！");
                return false;
            }
        },
        "json");
    },

    PunchOut: function() {
        var userid = $("#userid").val();
        console.log(userid);
        $.post("../../BLL/Home/PunchOut.php", 
            {userid : userid}, 
            function(data, status) {
            if( status=="success" ) {
                if( data.err == 0 ) {
                    console.log(data.msg);
                    myApp.showSuccessMsg(data.msg);
                    myApp.TodayRecord();
                    return true;
                }
                else 
                {
                    myApp.showMsg(data.msg);
                    console.log(data.msg);
                }
            } 
            else 
            {
                myApp.showMsg("系統發生錯誤！");
                console.log("登入時，系統發生錯誤！");
                return false;
            }
        },
        "json");
    },

    ForgotPunchin: function() {
        if( !myApp.checkInput() ) {
            return false;
        }
        params= $('#forgot-punchin-form').serialize();
        console.log(params);        
        $.post("../../BLL/Home/ForgotPunchin.php", 
            params,
            function(data, status) {
            if( status=="success" ) {
                $('#CheckModal').modal('hide');
                if( data.err == 0 ) {
                    myApp.showSuccessMsg(data.msg); 
                    console.log(data.result);
                    return true;
                }
                else 
                {
                    myApp.showMsg(data.msg);
                    console.log(data.msg);
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

    TodayRecord: function( ) {
        userid = $("#userid").val();
        $.post("../../BLL/Home/TodayRecord.php", 
            {userid : userid},
            function(data, status) {
            if( status=="success" ) {
                if( data.err == 0 ) {
                    console.log("data.result");

                    if(data.PersonalPunch==='Checked-in'){
                        $('#punchIn').prop('disabled', true);
                        $('#punchOut').prop('disabled', false);
                    }else if(data.PersonalPunch==='Checked-out'){
                        $('#punchIn').prop('disabled', false);
                        $('#punchOut').prop('disabled', true);
                    }else{
                        $('#punchIn').prop('disabled', false);
                        $('#punchOut').prop('disabled', false);
                    }

                    $('#punchTime').text(data.PersonalPunchTime);
                    // 顯示今日總打卡人數
                    $('#today_Punchin').text(data.CountPunchIn);
                    myApp.CircleChart('today_circle', data.CountPunchIn);
                    // 顯示今日請假人數
                    $('#today_Leave').text(data.CountLeave);
                    myApp.CircleChart('leave_circle', data.CountLeave);

                    
                    if (data.result != '') {
                        data.result.forEach(record => {
                            $("td[data-employ='" + record.Employee_id + "'][data-punchin]").text(record.PunchIn);
                            $("td[data-employ='" + record.Employee_id + "'][data-punchout]").text(record.PunchOut === '00:00:00' ? '' : record.PunchOut);
                            var statusCell   = $("td[data-employ='" + record.Employee_id + "'][data-status]");

                            var statusHtml = '';
                            if (record.Status === 'Late') {
                                statusHtml = '<span class="text-success">在場</span>';
                            } else if (record.Status === 'On Time') {
                                statusHtml = '<span class="text-success">在場</span>';
                            } else if (record.Status === 'Leave') {
                                statusHtml = '<span class="text-danger">請假</span>';
                            }
                            statusCell.html(statusHtml);
                        });
                    }
                    return true;
                }
                else 
                {
                    myApp.showMsg(data.msg);
                    console.log(data.msg);
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

    SearchLeave: function(){
        var date = $('#Leave_ChooseDay').val();
        $.post("../../BLL/Leave/SearchLeave.php",
            { date : date },
            function(data, status) {
                if( status=="success" ) {
                    console.log("SearchLeave: "+data);
                    $('#tbody-leave').empty();
                    if(data.resultLeave == ''){
                        $('#tbody-leave').append("<tr><td colspan='6'><h2>無請假單</h2></td></tr>");
                    }
                    else{
                        data.resultLeave.forEach(record => {
                            var row = $("<tr></tr>");
    
                            var IdCell      = $("<td></td>").text(record.date);
                            var userIdCell  = $("<td></td>").text(record.Name);
                            var dateCell    = $("<td></td>").text(record.LeaveType);
                            var punchInCell = $("<td></td>").text(record.StartTime);
                            var punchOutCell= $("<td></td>").text(record.EndTime);
                            var AttendanceStatus= $("<td></td>").text(record.AttendanceStatus);
    
                            row.append(IdCell, userIdCell, dateCell, punchInCell, punchOutCell, AttendanceStatus);
    
                            $("#tbody-leave").append(row);
                        });
                    }      
                    return true;
                }
            }, "json"
        );
    },

    CircleChart: function(data, value){
        var total = $(`#${data}`).data('total');
        var percentage = (value / total) * 100;
        var color = 'red';
        if(percentage > 70){
            color = 'green';
        }else if(percentage > 20){
            color = 'orange';
        }
        $(`#${data}`).css('background', 'conic-gradient(' + color + ' 0% ' + percentage + '%, #e0e0e0 ' + percentage + '%)');
    },

    IndexChart2: function(){
        $.post("../../BLL/Home/Chart2.php",
            function(data, status) {
            if( status=="success" ) {
                if( data.err == 0 ) {
                    console.log(data.result);
                    $("#myChart").empty();
                    if (data.result == null) {
                        $('#myChart').append("<h2>尚無資料</h2>");
                        return false;
                    }else{
                        myApp.Chart2(data.result);
                    }
                    return true;
                }
                else 
                {
                    myApp.showMsg(data.msg);
                    console.log(data.msg);
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
    Chart2:function(data){
        const ctx = document.getElementById('myChart').getContext('2d');
        const LeaveCount = Array(12).fill(0);
    
        data.forEach(record => {
            const monthIndex = parseInt(record.Month, 10) - 1; 
            LeaveCount[monthIndex] = parseInt(record.LeaveCount); 
        });
        const myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['一月', '二月', '三月', '四月', '五月', '六月', '七月','八月', '九月', '十月', '十一月', '十二月'],
                datasets: [{
                    label: '請假次數',
                    data: LeaveCount,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true

                    }
                }
            }
        });
    },
    // Ranking: function(){
    //     $.post("../../BLL/Home/RankingPoints.php",
    //         function(data, status) {
    //         if( status=="success" ) {
    //             if( data.err == 0 ) {
    //                 console.log(data.result);
    //                 $("#tbody-ranking").empty();
    //                 if (data.result == null) {
    //                     $('#tbody-ranking').append("<h2>尚無資料</h2>");
    //                     return false;
    //                 }
    //                 data.result.forEach(record => {
    //                     var row = $("<tr></tr>");

    //                     var userName = $("<td></td>").html(record.Name);
    //                     var totalPoint = $("<td></td>").text(record.Point);

    //                     row.append( userName, totalPoint);

    //                     $("#tbody-ranking").append(row);
    //                 });
    //                 return true;
    //             }
    //             else 
    //             {
    //                 myApp.showMsg(data.msg);
    //                 console.log(data.msg);
    //             }
    //         } 
    //         else 
    //         {
    //             myApp.showMsg("系統發生錯誤！");
    //             return false;
    //         }
    //     },
    //     "json");
    // },

    FixedLeaveSummary: function () {
        $.post("../../BLL/Home/FixedLeaveShow.php",
          function (data, status) {
            if (status == "success") {
              if (data.err == 0) {
                $("#tbody-fixed-leave").empty();
                console.log(data.result);
                data.result.forEach((item) => {
                  var employeeName = item.Employee_name;
                  var leaveSummary = item.LeaveSummary;
                  
                  var row = $("<tr></tr>")
                    .append($("<td></td>").css('width', '20%').css('font-weight', 'bold').text(employeeName))
                    .append($("<td></td>").css('width', '80%').text(leaveSummary));
                  
                  $("#tbody-fixed-leave").append(row);
                });
                
                return true;
              } else {
                myApp.showMsg(data.msg);
              }
            } else {
              myApp.showMsg("系統發生錯誤！");
              return false;
            }
          },
          "json"
        );
    },

    ForgotPunch: function(){
        $.post("../../BLL/Home/ForgotPunch.php",
            function(data, status) {
            if( status=="success" ) {
                if( data.err == 0 ) {
                    console.log(data.result);
                    $("#notification").text(data.count);
                    return true;
                }
                else 
                {
                    myApp.showMsg(data.msg);
                    console.log(data.msg);
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

    checkInput : function() {
        var szTemp = $("select[name='forgot-punchin-type']").val();
        if (szTemp == "") {
            $('#punchin-type-notice').text("*請選擇【上下班】！");
            return false;
        }
    
        var szTemp = $("input[name='forgot-punchin-date']").val();
        if (szTemp == "") {
            $('#punchin-date-notice').text("*請輸入【日期】！");
            return false;
        }
        var szTemp = $("input[name='forgot-punchin-time']").val();
        if (szTemp == "") {
            $('#punchin-time-notice').text("*請輸入【時間】！");
            return false;
        }

        return true;
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
    SwitchTheme: function(){
        $('body').toggleClass('dark');
        let mode = $('body').hasClass('dark') ? "dark" : "";
        localStorage.setItem('mode', mode);
    },
    Openbtn: function(){
        $('.sidebar').addClass('active');
        $('.content').addClass('active');
    },
    Closebtn: function(){
        $('.sidebar').removeClass('active');
        $('.content').removeClass('active');

    },

    showMsg : function ( szMsg ) {
        $("#modal-message-text").text(szMsg);
        $('#modal-message').modal('show');   
    },

    showCheckMsg : function (  ) { 
        $('#CheckModal').modal('show');
    },

    showSuccessMsg : function ( szMsg ) {
        $("#modal-message-success").text(szMsg);
        $('#modal-success').modal('show'); 
    }

}

$(document).ready(function() {
    $('#loading').show();//固定function

    $('#punchIn').click(myApp.PunchIn);
    $('#punchOut').click(myApp.PunchOut);
  
    setInterval(myApp.UpdateClock, 1000);
    myApp.UpdateClock();

    setInterval(myApp.TodayRecord, 300000); // 300000ms = 5 phút
    myApp.TodayRecord();

    setInterval(myApp.IndexChart2, 600000); // 600000ms = 10 phút
    myApp.IndexChart2();

    setInterval(myApp.Ranking, 600000); 
    // myApp.Ranking();
   
    myApp.ForgotPunch();

    myApp.SearchLeave();

    myApp.FixedLeaveSummary();

    $('#Leave_ChooseDay').change(myApp.SearchLeave);

    $('#Leave_NextDay').click(function(){
        var newDate = new Date($('#Leave_ChooseDay').val());
        newDate.setDate(newDate.getDate() + 1);
        $('#Leave_ChooseDay').val(newDate.toISOString().slice(0, 10));
        myApp.SearchLeave();
    });

    $('#Leave_PrevDay').click(function(){
        var newDate = new Date($('#Leave_ChooseDay').val());
        newDate.setDate(newDate.getDate() - 1);
        $('#Leave_ChooseDay').val(newDate.toISOString().slice(0, 10));
        myApp.SearchLeave();
    });

    $(document).on('click', '#forgot-punchin-comfirm', function() {
        $('#modal-ForgotPunchin').modal('hide');
        myApp.showCheckMsg();
        $('#confirmAccept').off('click').on('click', function() {
            myApp.ForgotPunchin();
        });
    });
    //固定function
    myApp.SessionCheck();
    $('.open-btn').click(myApp.Openbtn);
    $('.close-btn').click(myApp.Closebtn);
    $('#sw-checkbox').change(myApp.SwitchTheme);
    $('#loading').hide();   
});

