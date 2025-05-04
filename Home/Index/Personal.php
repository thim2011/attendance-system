<?php
    require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
    require_once dirname(__FILE__).'/../../DAL/EmWorkHourDAL.php';
    require_once dirname(__FILE__).'/../../DAL/AnnualLeaveDAL.php';

    $employeeDAL = new EmployeeDAL();
    $emWorkHourDAL = new EmWorkHourDAL();
    $annualLeaveDAL = new AnnualLeaveDAL();

    if(!isset($_SESSION['auth_token']) || !isset($_COOKIE['auth_token_test']) || !isset($_SESSION['USERid'])){
        header('Location: ../User/Login.php');
        exit();
    }
    else{ 
        $userId= $_SESSION['USERid'];
    }

    $year_month = date("Y-m");
    $year = date("Y");
    $month = date("m");
    $Employee = $employeeDAL->getOneById($userId);
    $WorkHour = $emWorkHourDAL->getByEmployeeYM($userId, $year, $month);
    if($WorkHour != null){
        $NormalWorkingHours=$WorkHour->NormalWorkingHours;
        $Work_Overtime=$WorkHour->Work_Overtime;
        $TotalHours=$WorkHour->Half_leave_hours + $WorkHour->Full_leave_hours + $WorkHour->Unpaid_leave_hours;
    }
    else{
        $NormalWorkingHours=0;
        $Work_Overtime=0;
        $TotalHours=0;
    }

    $TotalAnnual = $annualLeaveDAL->getAnnualLeaveByEmployee($userId, $year, "reset");
    $NowAnnual = $annualLeaveDAL->getUsedHours($userId, $year);
    include '../../Common/Header.php';
?>
<style>

    tbody {
    display: block;
    height: 300px;
    overflow: auto;
}
thead, tbody tr {
    display: table;
    width: 100%;
    table-layout: fixed;
}
thead {
    width: calc( 100%  )
}
table {
    width: 400px;
}


</style>
  
  <!--載入頁面小圓圈-->
  <div class="spinner-bg" id="loading" >
        <div class="spinner">
            <span>Loading...</span>
        </div>
    </div> 
 <!--載入頁面小圓圈-->    
<body>
    <div class="main-container d-flex">
        <div class="sidebar" id="side_nav">
            <?php include '../../Common/SideBar.php'; ?>
        </div>
        
        <div class="content">
            <?php include '../../Common/NavTop.php'; ?>
            <?php include '../../Common/Chatbot.php'; ?>
            <div class="row m-1 m-md-3">
                <div class="col-12 col-md-6 ">
                    <div class="aluxubu shadow rounded mt-3 p-3" >
                        <h2>員工資料</h2>
                        <div class="PersonalInfo d-flex justify-content-center">
                            <div class="m-3">
                                <h3 style="color: #007bff !important"><?php echo $Employee->Name ?></h3>
                                <p>員編：<span><?php echo $Employee->Employee_id ?></span></p>
                                <p><i class="fa-solid fa-envelope"></i>信箱：<span><?php echo $Employee->Email ?></span></p>
                                <p>廠別：<span><?php echo $Employee->Department ?></span></p>
                                <p>部門：<span><?php echo $Employee->Position ?></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="aluxubu shadow rounded mt-3 p-3" >
                    
                            <h2><?php echo date("Y-m") ?>月出勤</h2>
                      
                        <div class="PersonalInfo d-flex justify-content-center">
                            <div class="m-3">
                                <p>正常上班時數：<span><?php echo $NormalWorkingHours ?></span></p>
                                <p>加班時數：<span><?php echo $Work_Overtime	 ?></p>
                                <p>請假時數：<span><?php echo $TotalHours	 ?></p>
                                <p>出席積分：<span><?php echo $Employee->Point ?></span></p>
                                <!--<p>縂特休時數：<span><?php echo $TotalAnnual->TotalHours ?></span></p>-->
                                <p>已休特休時數：<span><?php echo $NowAnnual ?></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!--打卡記錄-->
                <div class="col-12 col-md-7">  
                    <div class="aluxubu shadow rounded mt-3 p-1 p-lg-3" >
                        <div id="PersonalTable" style="min-height:400px; max-height: 550px">
                            <h3><?php echo isset($_SESSION['USERname']) ? $_SESSION['USERname'] : '???' ?>出席表</h3>
                            <input type="month" class="inputcustom" id="PersonalMonth" value="<?php echo $year_month?>">
                            <table class="table table-striped" >
                                <thead>
                                    <tr>
                                        <th scope="col">人員</th>
                                        <th scope="col">日期</th>
                                        <th scope="col">上班時間</th>
                                        <th scope="col">下班時間</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody" style="padding:5px;">
                                </tbody>    
                            </table>
                        </div>
                    </div>
                </div>
                    <!--圖表-->
                <div class="col-12 col-md-5">
                    <div class="aluxubu shadow rounded mt-3 p-3">
                        <h3>統計個月上班時數</h3>
                        <canvas id="myChart" width="400" height="300"></canvas>
                    </div>
                </div>
                    <!--行事曆-->
                <div class="col-12 col-md-12">  
                    <div class="aluxubu shadow rounded mt-3 p-2">
                        <h3>行事曆</h3>
                        
                        <div class="d-flex flex-wrap justify-content-center" >
                            
                            <div id="calendar">
                                <div id="calendar-header">
                                    <button class="btn btn-success" id="prev-month">上月</button>
                                    <p id="month-year"></p>
                                    <button class="btn btn-success" id="next-month">下月</button>
                                </div>
                                <div id="calendar-body">
                                    <div class="day-names">
                                        <div>日</div>
                                        <div>一</div>
                                        <div>二</div>
                                        <div>三</div>
                                        <div>四</div>
                                        <div>五</div>
                                        <div>六</div>
                                    </div>
                                    <div id="days"></div>
                                </div>
                                <div class="detail d-flex flex-wrap p-3">
                                    <div class="d-flex align-items-center m-1">
                                        <div class="square" style="background-color: #3D82AB"></div>
                                        <span>國定假日</span>
                                    </div>
                                    <div class="d-flex align-items-center m-1">
                                        <div class="square" style="background-color: #3CB371"> </div>
                                        <span>普通假日</span>
                                    </div>
                                    <div class="d-flex align-items-center m-1">
                                        <div class="square" style="background-color: #DC143C"></div>
                                        <span>自定假日</span>
                                    </div>
                                    <div class="d-flex align-items-center m-1">
                                        <div class="square" style="background-color: white"></div>
                                        <span>平日</span>
                                    </div>
                                </div>
                            </div>

                            <div id="info">
                                <h4>資訊</h4>
                                <div id="info-div" >
                                   
                                    <p id="holiday-info"></p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>       
                <!--XXXX-->         
            </div>
        </div>
    </div>
    <input type="hidden" id="userid" value="<?php echo $userId ?>">
<section>
<script src="Personal.js"></script>
<?php
    include '../../Common/Footer.php';
?>
   
   </body>