var myApp={
    Holiday: function() {
        var params = $("#holiday").serialize();
        $.post("../../BLL/Leave/InsertLeave.php", 
            params, 
            function(data, status) {
            if( status=="success" ) {
                if( data.err == 0 ) {
                    console.log(data.result);
                    $('#CheckModal').modal('hide');
                    myApp.SendEmail(data.email, "NewLeave");
                    myApp.showSuccessMsg(data.msg);
                    return true;
                }
                else 
                {
                    $('#CheckModal').modal('hide');
                    myApp.showMsg(data.msg);
                    console.log(data.msg);
                }
            } 
            else 
            {
                myApp.showMsg("系統發生錯誤！");
                console.log("系統發生錯誤！");
                return false;
            }
        },
        "json");
    },

    checkInput : function() {
        var startdate = $("input[name='holiday-startdate']").val();
        if (startdate == "") {
            myApp.showMsg("請輸入【請假開始日期】！");
            return false;
        }

        var enddate = $("input[name='holiday-enddate']").val();
        if (enddate == "") {
            myApp.showMsg("請輸入【請假結束日期】！");
            return false;
        }

        var szTemp = $("#holiday-select").val();
        if (szTemp == "none") {
            myApp.showMsg("請輸入【請假類別】！");
            return false;
        }
        szTemp = $("textarea[name='holiday-reason']").val();
        if (szTemp == "") {
            myApp.showMsg("請輸入【請假理由】！");
            return false;
        }

        if(Date.parse(startdate) > Date.parse(enddate)) {
            myApp.showMsg("請假開始日期不能大於請假結束日期！");
            return false;
        }   

        return true;

    },

    HolidaySelect: function() {
        let startDate = $('#holiday-startdate').val();
        let endDate = $('#holiday-enddate').val();

        if (startDate && endDate) {
            let start = new Date(startDate);
            let end = new Date(endDate);
            let timeDiff = end - start;
            let days = Math.ceil(timeDiff / (1000 * 60 * 60 * 24))+1; // Tính số ngày nghỉ, bao gồm cả ngày bắt đầu và kết thúc

            let container = $('#holiday-time');
            container.empty();  // Xóa các thẻ input cũ

            for (let i = 0; i < days; i++) {
                let currentDate = new Date(start.getTime() + i * (24 * 60 * 60 * 1000));
                let formattedDate = currentDate.toISOString().split('T')[0];
                let inputGroup = `
                    <div class="mb-3">
                        <label for="holiday-starttime-${i}" class="form-label"><span>時間 (${formattedDate}):</span></label>
                        <select name="holiday-starttime-${i}" id="holiday-starttime-${i}">
                            <option value="08:00">08:00</option>
                            <option value="09:00">09:00</option>
                            <option value="10:00">10:00</option>
                            <option value="11:00">11:00</option>
                            <option value="12:00">12:00</option>
                            <option value="13:00">13:00</option>
                            <option value="14:00">14:00</option>
                            <option value="15:00">15:00</option>
                            <option value="16:00">16:00</option>
                            <option value="17:00">17:00</option>
                        </select>
                        <label>結束時間:</label>
                        <select name="holiday-endtime-${i}" id="holiday-endtime-${i}">
                            <option value="08:00">08:00</option>
                            <option value="09:00">09:00</option>
                            <option value="10:00">10:00</option>
                            <option value="11:00">11:00</option>
                            <option value="12:00">12:00</option>
                            <option value="13:00">13:00</option>
                            <option value="14:00">14:00</option>
                            <option value="15:00">15:00</option>
                            <option value="16:00">16:00</option>
                            <option value="17:00">17:00</option>
                        </select>
                    </div>
                `;
                container.append(inputGroup);
            }
            container.append("<input type='hidden' name='days' value='" + days + "'>");
            container.removeClass('d-none');  // Hiển thị container
        } else {
            myApp.showMsg("請選擇請假開始日期和請假結束日期！");
        }

    },

    HolidayTimeFormat: function() {
        let time = $(this).val();
        let [hours, minutes] = time.split(':');

        if (minutes !== '00' && minutes !== '30') {
            minutes = minutes < 30 ? '00' : '30';
        }

        $(this).val(`${hours}:${minutes}`);
    },

    /*CompleteLeave: function() {
        var leave_id = $('#CompleteBtn').attr('data-id');
        $.post("../../BLL/Leave/CompleteLeave.php", 
            {leave_id: leave_id}, 
            function(data, status) {
            if( status=="success" ) {
                if( data.err == 0 ) {   
                    console.log(data.result);
                    myApp.showSuccessMsg(data.msg);
                    window.location.reload(1);
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
                console.log("系統發生錯誤！");
                return false;
            }
        }, "json");
    },*/

    CancelLeave: function() {
        var leave_id = $('#CancelBtn').attr('data-id');
        $.post("../../BLL/Leave/CancelLeave.php",
            {leave_id: leave_id},
            function(data, status) {
            if( status=="success" ) {
                if( data.err == 0 ) {
                    console.log(data.result);
                    myApp.showSuccessMsg(data.msg);
                    window.location.reload(1);
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
                console.log("系統發生錯誤！");
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
    },

    showCheckMsg : function (  ) { 
        $('#CheckModal').modal('show');
    },

}



$(document).ready(function() {
    $('#loading').show();//固定function

    $('.navbar-nav a').on('click', function(e) {
        const type = $(this).attr('type-menu');
        console.log(type);
        // Remove and set active for button
        $('.navbar-nav a.active').removeClass('active');
        $(this).addClass('active');
        // Filter elements
        $('.leave-item').each(function() {
       
          if (type === 'all' || $(this).attr('type-item') === type) {
            $(this).removeClass('hide');
            
          } else {
            $(this).addClass('hide');
          }
        });
      });

      $('#leaveStatus').on('change', function() {
        const status = $(this).val();
      
        $('.d')
        // Filter elements
        $('.main-row').each(function() {
          if (status === 'all' || $(this).attr('status') === status) {
            $(this).removeClass('hide');
          } else {
            $(this).addClass('hide');
        
          }
        });
      });

    $(document).on('click', '#CancelBtn', function() {
        myApp.showCheckMsg();
        $('#confirmAccept').off('click').on('click', myApp.CancelLeave);
    });

    $('#holiday-submit').on('click', function(e) {
        if( !myApp.checkInput() ) {
            return false;
        }
        myApp.showCheckMsg();
        $('#confirmAccept').click(myApp.Holiday);
    });

    $('#holiday-select').change(myApp.HolidaySelect);
//confirmAccept
    $('.main-row').on('click', function() {
        $('.detail-row').not($(this).next('.detail-row')).hide();
        $(this).next('.detail-row').toggle();
    });



    //固定function
    myApp.SessionCheck();
    $('.open-btn').click(myApp.Openbtn);
    $('.close-btn').click(myApp.Closebtn);
    $('#sw-checkbox').change(myApp.SwitchTheme);
    $('#loading').hide(); 
});