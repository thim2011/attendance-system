var myApp = {

    PersonalRecord: function(PersonalMonth) {
        var userid = $("#userid").val();

        $.post("../../BLL/Home/PersonalRecord.php", 
            {userid : userid, PersonalMonth : PersonalMonth}, 
            function(data, status) {
            if( status=="success" ) {
                if( data.err == 0 ) {
                    console.log(data.result);
                    $("#tbody").empty();

                    if (data.result == null) {
                        $('#tbody').append("<tr><td colspan='6'><h2>無資料</h2></td></tr>");
                        return false;
                    }   
                    
                    data.result.forEach(record => {
                        var row = $("<tr></tr>");

                        var userIdCell = $("<td></td>").text(record.Name);
                        var dateCell = $("<td></td>").text(record.Date);
                        var punchInCell = $("<td></td>").text(record.PunchIn);
                        var punchOutCell = $("<td></td>").text(record.PunchOut);

                        row.append( userIdCell, dateCell, punchInCell, punchOutCell);

                        $("#tbody").append(row);
                    });
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
    PersonalChart: function() {
        var userid = $("#userid").val();
        $.post("../../BLL/Home/Chart1.php", 
            {userid : userid}, 
            function(data, status) {
            if( status=="success" ) {
                if( data.err == 0 ) {
                    console.log(data.result);
                    $("#myChart").empty();
                    if (data.result == null) {
                        $('#myChart').append("<h2>尚無資料</h2>");
                        return false;
                    }else{
                        myApp.Chart1(data.result);
                    }
                    return true;
                }
                else 
                {
                    //myApp.showMsg(data.msg);
                    console.log(data.msg);
                }
            } 
            else 
            {
                console.log("系統發生錯誤！");
                return false;
            }
        },
        "json");
    },
    Chart1: function(data) {
        const labels = ['一月', '二月', '三月', '四月', '五月', '六月', '七月','八月', '九月', '十月', '十一月', '十二月']; 
        const workHoursData = Array(12).fill(0);
    
        data.forEach(record => {
            const monthIndex = parseInt(record.Month, 10) - 1; 
            workHoursData[monthIndex] = parseInt(record.TotalWorkHours, 10); 
        });

        const datachart = {
            labels: labels,
            datasets: [{
                label: '工時',
                data: workHoursData,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 205, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(201, 203, 207, 0.2)'
                ],
                borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(255, 159, 64)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(54, 162, 235)',
                    'rgb(153, 102, 255)',
                    'rgb(201, 203, 207)'
                ],
                borderWidth: 1
            }]
        };

        const ctx = document.getElementById('myChart').getContext('2d');
        const stackedBar = new Chart(ctx, {
            type: 'bar',
            data: datachart,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            },
        });
    },
    
    monthNames : ["1 月", "2 月", "3 月", "4 月", "5 月", "6 月", "7 月", "8 月", "9 月", "10 月", "11 月", "12 月"],
    attendanceData: null,
    holidayData: null,

    renderCalendar: async function(month, year) {
        $('#month-year').text(`${myApp.monthNames[month]} ${year}`);
        $('#date').text(`${year} 年 ${myApp.monthNames[month]} `);
        var userid = $("#userid").val();

        try {
           
            myApp.attendanceData = await myApp.getPersonalAttendance(userid, `${year}-${(month + 1).toString().padStart(2, '0')}`);
            myApp.holidayData = await myApp.getHoliday(month+1, year);
            
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            
            $('#days').empty();
            for (let i = 0; i < firstDay; i++) {
                $('#days').append('<div></div>');
            }

            for (let i = 1; i <= daysInMonth; i++) {
                const currentDate = `${year}-${(month + 1).toString().padStart(2, '0')}-${i.toString().padStart(2, '0')}`;
            
                // 判斷是否有打卡
                let isPunch=false;
                if (myApp.attendanceData != null) {
                    isPunch = myApp.attendanceData.find(record => record.Date === currentDate);
                }
                // 判斷是否為假日
                const holidayRecord = myApp.holidayData.find(record => record.Date === currentDate);
                const isHoliday = holidayRecord && holidayRecord.Is_Holiday;
                const holidayName = holidayRecord ? holidayRecord.Name : null;
            
                let dayContent = `${i}`;
                if (isPunch) {
                    dayContent += ' <i class="fa-solid fa-check text-danger"></i>';
                }
            
                if (isHoliday && holidayName) {
                    if(holidayRecord.Is_Government){
                        color="#3D82AB";
                    }else{
                        color="#DC143C";
                    }
                    $('#days').append(`<div class="DaysinMonth" data-date="${currentDate}" style="background-color: ${color}">${dayContent}</div>`);
                } else if (isHoliday && !holidayName) {
                    $('#days').append(`<div class="DaysinMonth" data-date="${currentDate}" style="background-color: #3CB371">${dayContent}</div>`);
                } else {
                    $('#days').append(`<div class="DaysinMonth" data-date="${currentDate}" >${dayContent}</div>`);
                }
            }
        } catch (error) {
            console.error(error);
        }
    },

    getHoliday: function(month, year) {
        return new Promise((resolve, reject) => {
            $.post("../../BLL/Holiday/CalendarHoliday.php", 
                { month: month, year: year }, 
                function(data, status) {
                    if (status == "success") {
                        if (data.err == 0) {
                            resolve(data.result);
                        } else {
                            myApp.showMsg(data.msg);
                            reject(data.msg);
                        }
                    } else {
                        console.log("登入時，系統發生錯誤！");
                        reject("登入時，系統發生錯誤！");
                    }
                },
                "json"
            );
        });
    },    

    getPersonalAttendance: function(userid, PersonalMonth) {
        return new Promise((resolve, reject) => {
            $.post("../../BLL/Home/PersonalRecord.php", 
                {userid : userid, PersonalMonth : PersonalMonth}, 
                function(data, status) {
                    if (status == "success") {
                        if (data.err == 0) {
                            resolve(data.result);
                        } else {
                            myApp.showMsg(data.msg);
                            reject(data.msg);
                        }
                    } else {
                        console.log("登入時，系統發生錯誤！");
                        reject("登入時，系統發生錯誤！");
                    }
                },
                "json"
            );
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
    /*var mode = localStorage.getItem('mode');
        if (mode === 'dark') {
            $('body').addClass('dark');
        }*/
    $('#loading').show();//固定function

    setInterval(myApp.PersonalChart, 30000);
    myApp.PersonalChart();
    

    const date = new Date();
    let currentMonth = date.getMonth();
    let currentYear =date.getFullYear();


    
    var PersonalMonth = $("#PersonalMonth").val();
    myApp.PersonalRecord(PersonalMonth);
    $('#PersonalMonth').on('change', function(e) {
        var PersonalMonth = $("#PersonalMonth").val();
        myApp.PersonalRecord(PersonalMonth);
    });
    setInterval(myApp.PersonalRecord(PersonalMonth), 30000);
//行事曆--------------------------------------------------


    $('#prev-month').click(function() {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        myApp.renderCalendar(currentMonth, currentYear);
    });

    $('#next-month').click(function() {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        myApp.renderCalendar(currentMonth, currentYear);
    });

    myApp.renderCalendar(currentMonth, currentYear);

    $('#calendar').on('click', '.DaysinMonth', function(e) {
        const date = $(this).data('date');
    
        let isPunch = false;
        if (myApp.attendanceData != null) {
            isPunch = myApp.attendanceData.find(record => record.Date === date);
        }
        const holidayRecord = myApp.holidayData.find(record => record.Date === date);
        const isHoliday = holidayRecord && holidayRecord.Is_Holiday;
        const holidayName = holidayRecord ? holidayRecord.Name : null;
    
        let message = `日期: ${date}<br>`;
        if (isHoliday && holidayName) {
            message += `<strong>${holidayRecord.Name}</strong><br>`;
            message += `${holidayRecord.Category}<br>`;
            message += `${holidayRecord.Description}<br>`;
        } else if (isHoliday && !holidayName) {
            message += `${holidayRecord.Category}<br>`;
        }

        if (isPunch) {
            myApp.attendanceData.forEach(record => {
                if (record.Date === date) {
                    message += `簽到: ${record.PunchIn}<br>`;
                    message += `簽退: ${record.PunchOut}<br>`;
                    message += `狀態: ${record.Status === 'Late' ? '<span style="color:red;">遲到</span>' : '正常'}<br>`;
                }
            });
        }
    
        $('#holiday-info').html(message);
    });
    
//行事曆----------------
    //固定function
    myApp.SessionCheck();
    $('.open-btn').click(myApp.Openbtn);
    $('.close-btn').click(myApp.Closebtn);
    $('#sw-checkbox').change(myApp.SwitchTheme);
    $('#loading').hide(); 
});


