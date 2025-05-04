<?php
    require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
    require_once dirname(__FILE__).'/../../DAL/SettingDAL.php';

    if(!isset($_SESSION['auth_token']) || !isset($_COOKIE['auth_token_test']) || !isset($_SESSION['USERid'])){
        header('Location: ../User/Login.php');
        exit();
    }
    else{ 
        $userId= $_SESSION['USERid'];
    }   
    $userDAL    = new EmployeeDAL();
    $settingDAL = new SettingDAL();

    $AllEmploy   = $userDAL->getAll();
    $user       = $userDAL->getOneById($userId);
    $countall  = $userDAL->countAllEmployee();

    $WorkTime   = $settingDAL->getWorkTime();
    $BreakTime  = $settingDAL->getBreakTime();

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
/*    
    #todayRecord table td, #todayLeave table td{
        padding: 15px 4px !important;
    }
    #todayRecord table th, #todayLeave table th{
        padding: 15px 4px !important;
    }
*/
    
</style>

<body>
<!--載入頁面小圓圈-->
    <div class="spinner-bg" id="loading" >
        <div class="spinner">
            <span>Loading...</span>
        </div>
    </div> 
 <!--載入頁面小圓圈-->
         
    <div class="main-container d-flex">
        <div class="sidebar" id="side_nav">
            <?php include '../../Common/SideBar.php'; ?>
        </div>
        <div class="content">
            <?php include '../../Common/NavTop.php'; ?>
            <?php include '../../Common/Chatbot.php'; ?>
            <div class="row m-1">
                <div class="col-12 col-md-7 order-md-1 order-2 p-1 p-lg-3">

                    <div class="chart mt-3 p-3 shadow rounded aluxubu" style="min-height:380px">
                        <h3 class="text-start">簽到狀態</h3>

                        <div id="todayRecord" style="">
                           <table class="table table-striped " > 
                                <thead>
                                    <tr>
                                        <th scope="col">人員</th>
                                        <th scope="col">日期</th>
                                        <th scope="col">上班</th>
                                        <th scope="col">下班</th>
                                        <th scope="col">狀態</th>
                                        <th scope="col">備注</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody">
                                    <?php
                                        foreach($AllEmploy as $employ){
                                            echo "<tr>";
                                            echo "<td class='fw-bold'>".$employ->Name."</td>";
                                            echo "<td data-employ=".$employ->Employee_id.">".date('Y-m-d')."</td>";
                                            echo "<td data-punchin='' data-employ=".$employ->Employee_id."></td>"; 
                                            echo "<td data-punchout='' data-employ=".$employ->Employee_id."></td>"; 
                                            echo "<td class='fw-bold' data-status='' data-employ=".$employ->Employee_id.">未到</td>";   
                                            echo "<td data-remark='' data-employ=".$employ->Employee_id."></td>";
                                            echo "</tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="chart p-3 mt-3 shadow rounded aluxubu" style="min-height:400px" >
                        <div class="d-flex justify-content-between">
                            <h3 class="text-start">今天請假</h3>
                            <div>
                                <button class="btn" id="Leave_PrevDay"><i class="fa-solid fa-circle-chevron-left" style="color:#166F8C"></i></button>
                                <input type="date"  id="Leave_ChooseDay" class="inputcustom p-2 width100" value="<?php echo date("Y-m-d") ?>">
                                <button class="btn" id="Leave_NextDay"><i class="fa-solid fa-circle-chevron-right" style="color:#166F8C"></i></button>
                            </div>
                        </div>
                    
                        <div id="todayLeave" style="">
                            <table class="table table-striped " >
                                <thead>
                                    <tr>
                                        <th scope="col">日期</th>
                                        <th scope="col">人員</th>
                                        <th scope="col">請假類型</th>
                                        <th scope="col">時間</th>
                                        <th scope="col">結束</th>
                                        <th scope="col">狀態</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-leave">
                                </tbody>
                            </table>
                          </div>  
                    </div>

                    <div class="chart mt-3 p-3 shadow rounded aluxubu" style="min-height:250px;" >
                        <h3 class="text-start" >個月請假</h3>
                        <canvas id="myChart" width="400" height="150"></canvas>
                    </div>
                </div>

                <div class="col-12 col-md-5 order-md-2 order-1 p-1 p-lg-3">

                    <div class="mt-3 p-1 shadow rounded aluxubu" >
                        <div class="row mt-3 justify-content-center p-0">
                            <div class="col-3 col-md-3 mx-1 shadow rounded d-flex align-items-center justify-content-center">
                                <div class="circle-container">
                                    <div class="circle" id="circle" data-value="<?php echo $countall ?>">
                                        <div class="circle-inner">
                                            <span class="number"><?php echo $countall ?></span>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold">總人數</h6>
                                </div>
                            </div>
                            <div class="col-3 col-md-3 mx-1 shadow rounded d-flex align-items-center justify-content-center">
                                <div class="circle-container">
                                    <div class="circle" id="today_circle" data-total="<?php echo $countall ?>">
                                        <div class="circle-inner">
                                            <span class="number" id="today_Punchin"></span>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold">今天簽到</h6>
                                </div>                       
                            </div>
                            <div class="col-3 col-md-3 mx-1 shadow rounded d-flex align-items-center justify-content-center">
                                <div class="circle-container">
                                    <div class="circle" id="leave_circle" data-total="<?php echo $countall ?>">
                                        <div class="circle-inner">
                                            <span class="number" id="today_Leave"></span>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold">今天請假</h6>
                                </div>
                            </div>
                        </div>
                    </div> 
                            
                    <div class="chart p-3 mt-3 shadow rounded aluxubu" style="min-height:300px">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="text-start"><?php echo date("m")."月".date("d")."日"?></h3>
                            <div class=" ">
                                <button class="btn btn-light btn-sm" type="button" id="dropdownMenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                        
                                    <i class="fa-solid fa-ellipsis"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuBtn">
                                    <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal-ForgotPunchin">補打卡</button></li>
                                </ul>
                            </div>
                        </div>
                       
                        <div id="ground-clock" class="mb-2"><span id="clock"></span></div>
                        <p>上班時間：<span style="font-weight:bold"><?php echo $WorkTime->Work_start_time ?></span>-下班時間：<span style="font-weight:bold"><?php echo $WorkTime->Work_end_time ?></span></p>
                        <div class="d-grid gap-2 d-md-block">
                            <button class="btn btn-success me-md-2" id="punchIn"><i class="fa-solid fa-right-to-bracket"></i>上班</button>
                            <button class="btn btn-danger" id="punchOut"><i class="fa-solid fa-arrow-right-from-bracket"></i>下班</button>
                        </div>
                        <p>打卡時間:<span style="font-weight:bold" id="punchTime"> </span></p>
                    </div>
                   <!-- <div class="chart mt-3 p-3 shadow rounded aluxubu">
                       <h3>分數累計排名</h3>
                        <div id="RankTable" style="overflow: scroll; min-height: 300px; max-height:300px; ">
                           <table class="table table-striped" style="text-align: left"> 
                                <thead class="table">
                                    <tr>
                                        <th scope="col">人員</th>
                                        <th scope="col">分數</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-ranking" >
                                </tbody>
                            </table>
                        </div>
                    </div> -->
                    <div class="chart mt-3 p-3 shadow rounded aluxubu">
                       <h3>員工固定排假</h3>
                        <div id="FixedLeaveTable" style="overflow: scroll; min-height: 300px; max-height:300px; ">
                           <table class="table table-striped" style="text-align: left"> 
                                <thead class="table">
                                    <tr>
                                        <th scope="col" width="20%">人員</th>
                                        <th scope="col" width="80%">休假</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-fixed-leave" >
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="userid" value="<?php echo $userId ?>">
    

    <script src="Index.js"></script>
<?php
    include '../../Common/Footer.php';
?>
</body>
</html>