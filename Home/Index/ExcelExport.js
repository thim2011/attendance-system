var myApp = {
    ExcelData:[],
    FileName:'',
    GetData: function () {
        CheckInput = myApp.CheckInput();
        if( CheckInput == false ) {
            return false;
        }
        var params = $("#excel-form").serialize();
        $.post("../../BLL/Home/ExcelForm.php", 
            params, 
            function(data, status) {
                if( status=="success" ) {
                    if( data.err == 0 ) { 
                       
                        myApp.ExcelData = data.data;
                        myApp.FileName = data.fileName;

                        myApp.TabCreate(data.data);
                        let Firstkey = Object.keys(myApp.ExcelData)[0];    
                        myApp.Handson(myApp.ExcelData[Firstkey]);
                        
                        $('#ExportBtn').removeClass('hide');
                        return true;
                    } 
                    else 
                    {
                        myApp.showMsg(data.msg);
                        console.log(data.msg);
                        return false;
                    }
                }
                else {
                    myApp.showMsg("系統發生錯誤！");
                    console.log("系統發生錯誤！");
                    return false;
                }
            },
            "json");
    },

    ExportPunchin: function (XLSX) {
        const sheetRow = ["日期","星期","姓名", "上班時間", "下班時間", "上班備注", "下班備注"];
        const wb = XLSX.utils.book_new();
        var ws;
    
        Object.keys(myApp.ExcelData).forEach(Employee => {
            let sheetData = [["上下班打卡表"], sheetRow]; 
            var employeeData = myApp.ExcelData[Employee];
    
            employeeData.forEach(item => {
                sheetData.push([item.Date, item.DayofWeek, item.Name, item.Punch_in, item.Punch_out, item.IsLeaveMorning, item.IsLeaveEvening]);
            });
    
            var Name = employeeData[0].Name;
            ws = XLSX.utils.aoa_to_sheet(sheetData); 
            
            ws['!merges'] = [
                { s: { r: 0, c: 0 }, e: { r: 0, c: 6 } } 
            ];
    
            const defaultFont = { name: "Arial", sz: 20 };
    
            ws['A1'].s = {
                font: { name: "Arial", bold: true, sz: 28 }, 
                alignment: { horizontal: "center" }
            };

            ["A2", "B2", "C2", "D2", "E2", "F2", "G2"].forEach(cell => {
                ws[cell].s = {
                    font: { ...defaultFont, bold: true, sz: 23 },
                    alignment: { horizontal: "center" }
                };
            });
    
            for (let i = 3; i < sheetData.length + 1; i++) {
                ["A", "B", "C", "D", "E"].forEach(col => {
                    ws[`${col}${i}`].s = {
                        font: { ...defaultFont, sz: 20 } ,
                        alignment: { horizontal: "center" } 
                    };
                });

                ["F", "G"].forEach(col => {
                    ws[`${col}${i}`].s = {
                        font: { ...defaultFont, sz: 14 } ,
                        alignment: { horizontal: "center" } 
                    };
                });
            }
    
            ws['!cols'] = [
                { wch: 30 }, 
                { wch: 14 },  
                { wch: 30 },
                { wch: 25 },
                { wch: 25 },
                { wch: 35 },
                { wch: 35 }
            ];
    
            XLSX.utils.book_append_sheet(wb, ws, `${Name}打卡記錄.xlsx`);
        });
    
        XLSX.writeFile(wb, myApp.FileName+'.xlsx');
    },
        

    Handson: function (ExcelData) {
        
        var array2=[];
       
       ExcelData.forEach(item => {
        array2.push([
            item.Date, 
            item.DayofWeek, 
            item.Name, 
            item.Punch_in, 
            item.Punch_out, 
            item.IsLeaveMorning,
            item.IsLeaveEvening
        ]);
    });
        /*Object.keys(ExcelData).forEach( Employee => {
            var employeeData = ExcelData[Employee];
            console.log(employeeData);

            employeeData.forEach(item => {
                
                 Object.values(ExcelData).forEach(employeeData => {
                    employeeData.forEach(item => {
                array2.push([
                    item.Date, 
                    item.DayofWeek, 
                    item.Name, 
                    item.Punch_in, 
                    item.Punch_out, 
                    item.IsLeaveMorning,
                    item.IsLeaveEvening
                ]);
            });
        });*/
        $('#handson').empty();
        const container = document.querySelector('#handson');

        const data = [
            ["日期", "星期", "姓名", "上班時間", "下班時間", "上班備注", "下班備注"]
        ].concat(array2);

        const hot = new Handsontable(container, {
            data: data, 
            width: '100%',
            height: '600px',
            rowHeaders: true,
            colHeaders: true,
            autoWrapRow: true,
            autoWrapCol: true,
            colWidths: [110, 60, 100, 100, 100, 180, 180],
            maxRows: data.length,   
            licenseKey: 'non-commercial-and-evaluation',
            afterChange: function (changes, source) {
                if (source === 'edit') {
                    changes.forEach(([row, prop, oldValue, newValue]) => {
                        if (row > 0) { 
                            const employeeIndex = row - 1;
                            
                            switch (prop) {
                                case 0:
                                    ExcelData[employeeIndex].Date = newValue;
                                    break;
                                case 1:
                                    ExcelData[employeeIndex].DayofWeek = newValue;
                                    break;
                                case 2:
                                    ExcelData[employeeIndex].Name = newValue;
                                    break;
                                case 3:
                                    ExcelData[employeeIndex].Punch_in = newValue;
                                    break;
                                case 4:
                                    ExcelData[employeeIndex].Punch_out = newValue;
                                    break;
                                case 5:
                                    ExcelData[employeeIndex].IsLeaveMorning = newValue;
                                    break;
                                case 6:
                                    ExcelData[employeeIndex].IsLeaveEvening = newValue;
                                    break;
                            }
                        }
                    });
                    console.log("Updated ExcelData: ", ExcelData);
                }
            }
        });

    },

    TabCreate: function (data) {
        const ul = $('#sheetTabs');

        ul.empty();

        Object.values(data).forEach((employeeData) => {

            const tabItem = `
                <li class="nav-item" role="presentation">
                    <button class="nav-link nav-Excel " data-employee-id="${employeeData[0].Employee_id}">
                        ${employeeData[0].Name}
                    </button>
                </li>
            `;
            ul.append(tabItem);
        });

    },

    CheckInput: function () {
        var date1 = new Date($("#StartDate").val());
        var date2 = new Date($("#EndDate").val());
        if(date1 > date2) {
            myApp.showMsg("開始日期不能大於結束日期！");
            return false;
        }
        return true
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

    showSuccessMsg : function ( szMsg ) {
        $("#modal-message-success").text(szMsg);
        $('#modal-success').modal('show'); 
    }
}

$(document).ready(function () {

    $('#HandsonBtn').click(myApp.GetData);

    myApp.SessionCheck();
    $('#sw-checkbox').change(myApp.SwitchTheme);
    $('.open-btn').click(myApp.Openbtn);
    $('.close-btn').click(myApp.Closebtn);


});

$(document).on('click', '.nav-Excel', function() {
    var Employee_id = $(this).data('employee-id');
    myApp.Handson(myApp.ExcelData[Employee_id]);
    

  });