var myApp = {
  InsertHoliday: function () {
    var params = $("#holiday-form").serialize();
    $.post(
      "../../BLL/Holiday/InsertHoliday.php",
      params,
      function (data, status) {
        if (status == "success") {
          if (data.err == 0) {
            $("#modal-holiday").modal("hide");
            myApp.showSuccessMsg(data.msg);
            myApp.SearchHoliday();
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

  DeleteHoliday: function (holiday_id) {
    $.post(
      "../../BLL/Holiday/DeleteHoliday.php",
      { Holiday_id: holiday_id },
      function (data, status) {
        if (status != null && status == "success") {
          $("#CheckModal").modal("hide");
          if (data.err == 0) {
            myApp.showSuccessMsg(data.msg);
            myApp.SearchHoliday();
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

  SearchHoliday: function () {
    var year = $("#select-year").val();
    var month = $("#select-month").val();
    $.post(
      "../../BLL/Holiday/CalendarHoliday.php",
      { year: year, month: month },
      function (data, status) {
        if (status == "success") {
          if (data.err == 0) {
            $("#tbody").empty();
            data.result.forEach((element) => {
              button =
                '<button class="btn btn-danger deleteHoliday p-2" data-holiday-id="' +
                element.Holiday_id +
                '"><i class="fa-solid fa-minus" style="font-size:10px"></i></button>';
              var leave_row = $('<tr  class="holiday-list"></tr>')
                .append(
                  $("<td></td>").html(element.Is_Government == 0 ? button : "")
                )
                .append(
                  $(
                    "<td style='color: #1E90FF !important; font-size: 15px; font-weight: bold;'></td>"
                  ).text(element.Date)
                )
                .append($("<td></td>").text(element.Name))
                .append(
                  $("<td></td>").text(element.Is_Holiday == 1 ? "是" : "否")
                )
                .append($("<td></td>").text(element.Category))
                .append($("<td></td>").text(element.Description))
                .append(
                  $("<td></td>").text(
                    element.Is_Government == 1 ? "政府公告" : "自主加"
                  )
                );

              $("#tbody").append(leave_row);
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

  UpdateWorkTime: function () {
    var workstart = $("#WorkStartTime").val();
    var workend = $("#WorkEndTime").val();
    $.post(
      "../../BLL/Setting/UpdateWorkTime.php",
      { workstart: workstart, workend: workend },
      function (data, status) {
        if (status == "success") {
          $("#CheckModal").modal("hide");
          if (data.err == 0) {
            myApp.showSuccessMsg(data.msg);
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

  UpdateBreakTime: function () {
    var BreakStartTime = $("#BreakStartTime").val();
    var BreakEndTime = $("#BreakEndTime").val();
    $.post(
      "../../BLL/Setting/UpdateBreakTime.php",
      { BreakStartTime: BreakStartTime, BreakEndTime: BreakEndTime },
      function (data, status) {
        if (status == "success") {
          $("#CheckModal").modal("hide");
          if (data.err == 0) {
            myApp.showSuccessMsg(data.msg);
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

  ListEmployees: function () {
    $.post(
      "../../BLL/Setting/ListPermission.php",
      function (data, status) {
        if (status == "success") {
          if (data.err == 0) {
            $("#permission-tbody").empty();
            data.result.forEach((employee) => {
              var row = $("<tr></tr>")
                .append($("<td></td>").text(employee.Employee_id))
                .append($("<td></td>").text(employee.Name))
                .append($("<td></td>").text(employee.Email))
                .append($("<td></td>").html(employee.Role))
                .append($("<td></td>").html(employee.Status))
                .append($("<td></td>").html(employee.button));
              $("#permission-tbody").append(row);
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

  DeActivate: function (Employee_id, toggle) {
    $.post(
      "../../BLL/Setting/DeActive.php",
      { Employee_id: Employee_id, toggle: toggle },
      function (data, status) {
        if (status == "success") {
          $("#CheckModal").modal("hide");
          if (data.err == 0) {
            myApp.showSuccessMsg(data.msg);
            myApp.ListEmployees();
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

  PermissionsEdit: function (Employee_id) {
    params = $("#permissions-form").serialize();
    $.post(
      "../../BLL/Setting/PermissionsEdit.php",
      params,
      function (data, status) {
        if (status == "success") {
          if (data.err == 0) {
            $("#CheckModal").modal("hide");
            myApp.showSuccessMsg(data.msg);
            myApp.ListEmployees();
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

  SessionCheck: function () {
    $.post(
      "../../Utils/SessionCheck.php",
      function (data, status) {
        if (status == "success") {
          $("#Noti_count").text(data.Noti);
          $("#nav_noti_count").text(data.Noti);
          $("#PendingLeave").text(data.Pending);
          return true;
        }
      },
      "json"
    );
  },

  //
  PersonalRecord: function () {
    var userid = $("#select-employ").val();
    var PersonalMonth = $("#PersonalMonth").val();
    $.post(
      "../../BLL/Home/PersonalRecord.php",
      { userid: userid, PersonalMonth: PersonalMonth },
      function (data, status) {
        if (status == "success") {
          if (data.err == 0) {
            $("#punchtime-tbody").empty();

            if (data.result == null) {
              $("#punchtime-tbody").append(
                "<tr><td colspan='6'><h2>無資料</h2></td></tr>"
              );
              return false;
            }
            data.result.forEach((record) => {
              var editbutton ="<button class='btn btn-primary editPunchTime'  data-attendance-id='" +record.Attendance_id +"' data-date-time='" +record.Date +
                "' data-punchout-time='" +
                record.PunchOut +
                "' data-punchin-time='" +
                record.PunchIn +
                "' ><i class='fa-solid fa-pen-to-square'></i></button>";
              var deletebuttn =
                "<button class='btn btn-danger deletePunchTime' data-attendance-id='" +
                record.Attendance_id +
                "' ><i class='fa-solid fa-trash'></i></button>";

              var row = $("<tr></tr>")
                .append($("<td></td>").text(record.Name))
                .append($("<td></td>").text(record.Date))
                .append($("<td></td>").text(record.PunchIn))
                .append($("<td></td>").text(record.PunchOut))
                .append($("<td></td>").html(editbutton + deletebuttn));
              $("#punchtime-tbody").append(row);
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
  EditPunchtime: function () {
    var editPunchForm = $("#editPunchForm").serialize();
    $.post(
      "../../BLL/Setting/EditPunchtime.php",
      editPunchForm,
      function (data, status) {
        if (status == "success") {
          if (data.err == 0) {
            $("#modal-Punchtime").modal("hide");
            myApp.showSuccessMsg(data.msg);
            myApp.PersonalRecord();
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
  DeletePunchtime: function () {
    var del_id = $("#delete_Attendance_id").val();
    $.post(
      "../../BLL/Setting/DeletePunchtime.php",
      { Attendance_id: del_id },
      function (data, status) {
        if (status == "success") {
          if (data.err == 0) {
            $("#modal-DeletePunchtime").modal("hide");
            myApp.showSuccessMsg(data.msg);
            myApp.PersonalRecord();
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

  FixedLeave: function () {
    $.post(
      "../../BLL/Setting/FixedLeave.php",
      function (data, status) {
        if (status == "success") {
          if (data.err == 0) {
            $("#fixed-leave-tbody").empty();
            
            // Các ngày trong tuần
            const days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
            
            data.result.forEach((employee) => {
              var row = $("<tr></tr>").append($("<td></td>").text(employee.Employee_name));
              var row2 = "<td class='text-center'>早上<br>下午</td>";
              row.append(row2);
              // Tạo ô cho từng ngày
              days.forEach(day => {
                var cell = $("<td class='text-center'></td>");
                
                // Tạo checkbox AM
                var amDiv = $("<div class='mb-1'></div>").append(
                  $("<input type='checkbox'>")
                    .prop("checked", employee[day + "_AM"] == 1)
                    .attr("data-employee-id", employee.Employee_id)
                    .attr("data-day", day)
                    .attr("data-period", "AM")
                    .addClass("fixed-leave-checkbox")
                );
                
                // Tạo checkbox PM
                var pmDiv = $("<div></div>").append(
                  $("<input type='checkbox'>")
                    .prop("checked", employee[day + "_PM"] == 1)
                    .attr("data-employee-id", employee.Employee_id)
                    .attr("data-day", day)
                    .attr("data-period", "PM")
                    .addClass("fixed-leave-checkbox")
                );
                
                cell.append(amDiv).append(pmDiv);
                row.append(cell);
                
              });
              
              $("#fixed-leave-tbody").append(row);
            });
            
            // Xử lý sự kiện thay đổi checkbox
            $(".fixed-leave-checkbox").change(function() {
              var employeeId = $(this).data("employee-id");
              var day = $(this).data("day");
              var period = $(this).data("period");
              var isChecked = $(this).prop("checked") ? 1 : 0;
              
              $.post(
                "../../BLL/Setting/FixedLeaveUpdate.php",
                {
                  employee_id: employeeId,
                  day: day,
                  period: period,
                  value: isChecked
                },
                function(response) {
                  if (response.err != 0) {
                    myApp.showMsg(response.msg);
                    // Đặt lại trạng thái checkbox nếu lỗi
                    $(this).prop("checked", !isChecked);
                  }
                },
                "json"
              );
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

  Openbtn: function () {
    $(".sidebar").addClass("active");
  },
  Closebtn: function () {
    $(".sidebar").removeClass("active");
  },
  SwitchTheme: function () {
    $("body").toggleClass("dark");
    let mode = $("body").hasClass("dark") ? "dark" : "";
    localStorage.setItem("mode", mode);
  },
  showMsg: function (szMsg) {
    $("#modal-message-text").text(szMsg);
    $("#modal-message").modal("show");
  },

  showCheckMsg: function () {
    $("#CheckModal").modal("show");
  },

  showSuccessMsg: function (szMsg) {
    $("#modal-message-success").text(szMsg);
    $("#modal-success").modal("show");
  },
  showEditPunchTime: function (punchin, punchout, date, attendance_id) {
    $("#Punchin_time").val(punchin);
    $("#Punchout_time").val(punchout);
    $("#date_punch").text(date);
    $("#Attendance_id").val(attendance_id);
    $("#modal-Punchtime").modal("show");
  },

  showDeletePunchModal: function (szMsg, attendance_id) {
    $("#model-text").text(szMsg);
    $("#delete_Attendance_id").val(attendance_id);
    $("#modal-DeletePunchtime").modal("show");
  },
};

$(document).ready(function () {
  $("#loading").show();

  $(".navbar-nav a").on("click", function (e) {
    const type = $(this).attr("type-menu");
    $(".navbar-nav a.active").removeClass("active");
    $(this).addClass("active");

    $(".setting-item").each(function () {
      if (type === "all" || $(this).attr("type-item") === type) {
        $(this).removeClass("hide");
      } else {
        $(this).addClass("hide"); 
      }
    });
  });

  //HOliday--start-----------------------
  myApp.SearchHoliday();
  $(document).on("change", "#select-year, #select-month", function () {
    myApp.SearchHoliday();
  });

  $("#insertHoliday").click(function () {
    $("#modal-holiday").modal("show");
  });
  $("#PostHoliday").click(myApp.InsertHoliday);

  $(document).on("click", ".deleteHoliday", function () {
    var holiday_id = $(this).data("holiday-id");
    myApp.showCheckMsg();

    $("#confirmAccept").data("action", "deleteHoliday");
    $("#confirmAccept").data("holiday-id", holiday_id);
  });
  //Holiday---end------------------------
  $("#WorkTime_Btn").click(function () {
    var workstart = $("#WorkStartTime").val();
    var workend = $("#WorkEndTime").val();
    if (workstart == "" || workend == "") {
      myApp.showMsg("請輸入上下班時間！");
      return false;
    } else if (workstart >= workend) {
      myApp.showMsg("上班時間需小於下班時間！");
      return false;
    }

    $("#CheckModal").modal("show");
    $("#confirmAccept").click(myApp.UpdateWorkTime);
  });

  $("#WorkTime_Edit").on("click", function () {
    const $workStartTime = $("#WorkStartTime");
    const $workEndTime = $("#WorkEndTime");

    $workStartTime.prop("readonly", !$workStartTime.prop("readonly"));
    $workEndTime.prop("readonly", !$workEndTime.prop("readonly"));

    const isReadOnly = $workStartTime.prop("readonly");
    if (isReadOnly) {
      $workStartTime.css({
        "background-color": "#A9A9A9",
      });
      $workEndTime.css({
        "background-color": "#A9A9A9",
      });
    } else {
      $workStartTime.css({
        "background-color": "#d4edda", // Light green for editable
        border: "1px solid #c3e6cb",
      });
      $workEndTime.css({
        "background-color": "#d4edda",
        border: "1px solid #c3e6cb",
      });
    }
  });

  $("#BreakTime_Btn").click(function () {
    var BreakStartTime = $("#BreakStartTime").val();
    var BreakEndTime = $("#BreakEndTime").val();
    if (BreakStartTime == "" || BreakEndTime == "") {
      myApp.showMsg("請輸入休息時間！");
      return false;
    } else if (BreakStartTime >= BreakEndTime) {
      myApp.showMsg("休息開始時間需小於結束時間！");
      return false;
    }
    $("#CheckModal").modal("show");
    $("#confirmAccept").click(myApp.UpdateBreakTime);
  });
  $("#BreakTime_Edit").on("click", function () {
    const $breakStartTime = $("#BreakStartTime");
    const $breakEndTime = $("#BreakEndTime");

    $breakStartTime.prop("readonly", !$breakStartTime.prop("readonly"));
    $breakEndTime.prop("readonly", !$breakEndTime.prop("readonly"));

    const isReadOnly = $breakStartTime.prop("readonly");
    if (isReadOnly) {
      $breakStartTime.css({
        "background-color": "#A9A9A9",
      });
      $breakEndTime.css({
        "background-color": "#A9A9A9",
      });
    } else {
      $breakStartTime.css({
        "background-color": "#d4edda", // Light green for editable
        border: "1px solid #c3e6cb",
      });
      $breakEndTime.css({
        "background-color": "#d4edda",
        border: "1px solid #c3e6cb",
      });
    }
  });

  //停用賬戶

  $(document).on("click", ".toggleEmployBtn", function () {
    var Employee_id = $(this).data("employee-id");
    var toggle = $(this).data("toggle");
    myApp.showCheckMsg();
    $("#confirmAccept").data("action", "deactivate");
    $("#confirmAccept").data("employee-id", Employee_id);
    $("#confirmAccept").data("toggle", toggle);

    $("#confirmAccept")
      .off("click")
      .on("click", function () {
        myApp.DeActivate(Employee_id, toggle);
      });
  });

  $(document).on("click", "#confirmAccept", function () {
    //Modal confirm button 實現不同功能，根據data-action，實現“停用賬戶”和“刪除假期”
    var action = $(this).data("action");

    if (action === "deactivate") {
      var Employee_id = $(this).data("employee-id");
      var toggle = $(this).data("toggle");
      myApp.DeActivate(Employee_id, toggle);
    } else if (action === "deleteHoliday") {
      var holiday_id = $(this).data("holiday-id");
      myApp.DeleteHoliday(holiday_id);
    }
  });

  $(document).on("click", ".editpermissions", function () {
    var Employee_id = $(this).data("employee-id");
    $("#employID_hide").val(Employee_id);
    $("#modal-permissions").modal("show");
    $("#comfirm-Permissions")
      .off("click")
      .on("click", function () {
        $("#modal-permissions").modal("hide");
        myApp.showCheckMsg();
      });

    $("#confirmAccept")
      .off("click")
      .on("click", function () {
        myApp.PermissionsEdit(Employee_id);
      });
  });

  //修改打卡時間
  $(document).on("change", "#select-employ, #PersonalMonth", function () {
    myApp.PersonalRecord();
  });

  $(document).on("click", ".editPunchTime", function () {
    var punchin = $(this).data("punchin-time");
    var punchout = $(this).data("punchout-time");
    var date = $(this).data("date-time");
    var attendance_id = $(this).data("attendance-id");

    myApp.showEditPunchTime(punchin, punchout, date, attendance_id);
  });

  $(document).on("click", ".deletePunchTime", function () {
    var attendance_id = $(this).data("attendance-id");
    myApp.showDeletePunchModal("確定要刪除嗎？", attendance_id);
  });

  $("#Edit_punchtime").click(myApp.EditPunchtime);
  $("#comfirm_delete").click(myApp.DeletePunchtime);
  //================================================================

  //固定function

  myApp.ListEmployees();
  myApp.FixedLeave();
  myApp.SessionCheck();
  $(".open-btn").click(myApp.Openbtn);
  $(".close-btn").click(myApp.Closebtn);
  $("#sw-checkbox").change(myApp.SwitchTheme);
  $("#loading").hide();
});
