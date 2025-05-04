<?php
    require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/EmLeaveDAL.php';
    require_once dirname(__FILE__).'/../../DAL/LeaveDetailsDAL.php';
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';
    require_once dirname(__FILE__).'/../../DAL/LeaveTypeDAL.php';
    require_once dirname(__FILE__).'/../../DAL/AnnualLeaveDAL.php';

    $annualLeaveDAL = new AnnualLeaveDAL();
    $EmLeaveDAL      = new EmLeaveDAL();
    $LeaveDetailsDAL = new LeaveDetailsDAL();
    $EmployeeDAL     = new EmployeeDAL();
    $LeaveTypeDAL    = new LeaveTypeDAL();

    if(!isset($_SESSION['auth_token']) || !isset($_COOKIE['auth_token_test']) || !isset($_SESSION['USERid'])){
        header('Location: ../User/Login.php');
        exit();
    }
    else{ 
        $userId= $_SESSION['USERid'];
    }   

    $LeaveType = $LeaveTypeDAL->getLeaveType();
    $EmployeeLeave = $EmLeaveDAL->getLeaveByEmployee($userId);
    $year = date("Y");
    $TotalAnnual = $annualLeaveDAL->getAnnualLeaveByEmployee($userId, $year, "reset");
    $NowAnnual = $annualLeaveDAL->getUsedHours($userId, $year);

    include '../../Common/Header.php';
?>
<style>

    button {
        padding: 10px !important; 
    }
    .navbar-nav li{
        border-right: 1px solid #e0e0e0;
    }

    .leaveform{
        width: 35%;
    }

    @media screen and (max-width: 1200px) {
        .leaveform{
            width: 70%;
        }
        
    }
    @media screen and (max-width: 600px) {
        .leaveform{
            width: 90%;
        }
        
    }


</style>
<div class="spinner-bg" id="loading">
        <div class="spinner">
            <span>Loading...</span>
        </div>
    </div>
<body>
    <div class="main-container d-flex">
        <div class="sidebar" id="side_nav">
            <?php include '../../Common/SideBar.php'; ?>
        </div>
        
        <div class="content">
            <?php include '../../Common/NavTop.php'; ?>
            <?php include '../../Common/Chatbot.php'; ?>
            <div class="row m-1 m-md-3">
                <div class="col-12 col-md-12 p-1 p-lg-3 ">
                    <div class="aluxubu shadow rounded p-0" >
                        <!-- -->
                        <nav class="navbar navbar-expand ">
                            <div class="container-fluid">
                                <div class="collapse navbar-collapse" id="navbarNav">
                                    <ul class="navbar-nav">
                                        <li class="nav-item active">
                                            <a class="nav-link active" href="#" type-menu="history-leave">歷史</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#" type-menu="leave-form">我要請假</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </nav>
                        
                        <!-- -->
                        <div class="leave-list justify-content-center p-2">
                            <div class="leave-item" type-item="history-leave">
                                <h2 class="text-center">歷史假單</h2>
                                <select class="form-select form-select-sm" id="leaveStatus">
                                    <option value="Pending" >待審核</option>
                                    <option value="Accepted">同意</option>
                                    <option value="Rejected">拒絕</option>
                                    <option value="all">全部</option>
                                </select>
                                <div id="PersonalLeave" style="overflow: scroll; min-height: 500px">
                                    <table class="table table-striped " >
                                        <thead class="table">
                                            <tr>
                                                <th scope="col">編號</th>
                                                <th scope="col">人員</th>
                                                <th scope="col">請假類</th>
                                                <th scope="col">起日</th>
                                                <th scope="col">結束日</th>
                                                <th scope="col">狀態</th>
                                            </tr>
                                        </thead >
                                        <tbody id="tbody" style="padding:10px">
                                            <?php foreach($EmployeeLeave as $item){ 
                                                    $Employee = $EmployeeDAL->getOneById($item->Employee_id);
                                                    $leavetype = $LeaveTypeDAL->getOneById($item->Leave_type);


                                                    if($item->Status == 'Pending'){
                                                        $Status = '<button class="btn btn-outline-warning">待審核</button>';
                                                    }
                                                    else if($item->Status == 'Accepted'){
                                                        $Status = '<button class="btn btn-outline-success"><i class="fa-solid fa-check" style="color: #63E6BE;"></i> 已通過</button>';
                                                    }
                                                    else if($item->Status=='Completed'){
                                                        $Status = '<button class="btn btn-outline-secondary"><i class="fa-solid fa-circle-check" style="color: #A9A9A9"></i> 結束</button>';
                                                    }
                                                    else if($item->Status=='Cancelled'){
                                                        $Status = '<button class="btn btn-outline-secondary"><i class="fa-solid fa-circle-xmark" style="color: #A9A9A9"></i> 取消</button>';

                                                    }
                                                    else{
                                                        $Status = '<button class="btn btn-outline-danger"><i class="fa-solid fa-times" style="color: #F03E3E;"></i> 拒絕</button>';
                                                    }

                                                    if($item->Status != 'Pending'){
                                                        $verifyBy = $EmployeeDAL->getOneById($item->VerifyBy);
                                                    }
                                                ?>
                                                <tr class="main-row" status="<?php echo $item->Status ?>">
                                                    <td style="color: #1E90FF !important"><?php echo $item->Leave_id; ?></td>
                                                    <td><?php echo $Employee->Name; ?></td>
                                                    <td><?php echo $leavetype->Leave_type_name; ?></td>
                                                    <td><?php echo $item->Start_date; ?></td>
                                                    <td><?php echo $item->End_date; ?></td>
                                                    <td><?php echo $Status; ?></td>
                                                </tr>
                                                <tr class="detail-row">
                                                    <td colspan="6">
                                                        <div class="detail-container">
                                                            <div class="detail-summary">
                                                                <span class="detail_span">總天數:</span><strong> <?php echo $item->Total_day; ?>日</strong><br>
                                                               <span class="detail_span">假別：</span><strong><?php echo $leavetype->Leave_type_name ?></strong><br>
                                                               <span class="detail_span">審核人：</span><strong><?php echo ($item->Status != 'Pending' && $item->Status != 'Cancelled') ? $verifyBy->Name : "未" ?></strong><br>
                                                               <span class="detail_span">審核時間:</span><strong> <?php echo $item->Status != 'Pending' ? $item->VerifyTime : ""?></strong><br>
                                                               <span class="detail_span">原因:</span><strong> <?php echo $item->Reason; ?></strong>
                                                            </div>
                                                            <div class="detail-info">
                                                                <strong>請假時段</strong>
                                                                <?php 
                                                                    $details = $LeaveDetailsDAL->getDetailsByID($item->Leave_id);
                                                                    foreach($details as $row): 
                                                                ?>
                                                                    <p><?php echo $row->Date . ': ' . $row->Start_time . ' -> ' . $row->End_time; ?></p>
                                                                <?php endforeach; ?>
                                                            </div> 
                                                        </div>  
                                                        <?php if($item->Status == 'Pending'){ 
                                                            $details_json = json_encode($details);
                                                            ?>
                                                             
                                                            <!--<button id="CompleteBtn" data-id="<?php echo $item->Leave_id ?>" class="btn btn-primary">確認無吳</button>
                                                             <button id="EditBtn" class="btn btn-warning" onclick='myApp.showEditModal()' >修改時間</button>-->
                                                            <button id="CancelBtn" data-id="<?php echo $item->Leave_id ?>" class="btn btn-danger">取消請假</button>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                             <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="leave-item hide p-6 d-flex justify-content-center " type-item="leave-form">
                              
                                        <div class="leaveform">
                                            <h3>請假區</h3>
                                            <div class="card p-2">
                                                <form id="holiday">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">姓名：</label>
                                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($_SESSION['USERname']) ? $_SESSION['USERname'] : '???' ?>" readonly>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="holiday-startdate" class="form-label">開始日期：</label>
                                                        <input type="date" class="form-control" id="holiday-startdate" name="holiday-startdate">
                                                        
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="holiday-endtime" class="form-label">結束日期：</label>
                                                        <input type="date" class="form-control" id="holiday-enddate" name="holiday-enddate">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="holiday-select" class="form-label">假別:</label>
                                                        <select class="form-select" id="holiday-select" name="holiday-select">
                                                            <option value="none" selected>--請選擇假別--</option>
                                                            <?php
                                                                foreach($LeaveType as $row){
                                                                    if($row->Leave_type_id == 13){
                                                                        if($NowAnnual >= $TotalAnnual->TotalHours){
                                                                            echo '<option disabled value="'.$row->Leave_type_id.'">'.$row->Leave_type_name.'(已沒時數)</option>';
                                                                        }else{
                                                                            echo '<option value="'.$row->Leave_type_id.'">'.$row->Leave_type_name.'(剩'.$TotalAnnual->TotalHours-$NowAnnual.'小時)</option>';
                                                                        }
                                                                    }
                                                                    else
                                                                    echo '<option value="'.$row->Leave_type_id.'">'.$row->Leave_type_name.'</option>';
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3" id="holiday-time" >
                                                        
                                                    </div>
                                                    <div class="mb-2">
                                                        <label for="holiday-reason" class="form-label">原因：</label>
                                                        <textarea class="form-control" id="holiday-reason" name="holiday-reason" rows="3"></textarea>
                                                    </div>
                                                    <input type="hidden" value="<?php echo $userId ?>" name="userID">             
                                                </form>
                                                <button id="holiday-submit" class="btn btn-primary btn-block mb-2">
                                                    提交
                                                </button>
                                            </div>  
                                        </div>
                                
                            </div>

                            
                        </div>
                    </div>
                </div>     
            </div>
        </div>
    </div>
    <input type="hidden" id="userid" value="<?php echo $userId ?>">

    <script src="LeaveForm.js"></script>
    <script>

    
</script>
<?php
    include '../../Common/Footer.php';
?>
</body>