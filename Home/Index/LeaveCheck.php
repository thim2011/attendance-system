<?php
    require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/LeaveTypeDAL.php';
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';

    $leaveTypeDAL = new LeaveTypeDAL();
    $EmployeeDAL = new EmployeeDAL();

    if(!isset($_SESSION['auth_token']) || !isset($_COOKIE['auth_token_test']) || !isset($_SESSION['USERid'])){
        header('Location: ../User/Login.php');
        exit();
    }
    else{ 
        $userId= $_SESSION['USERid'];
        $role= $EmployeeDAL->checkRole($userId, 1);
        if($role == false){
            header('Location: ../User/Login.php');
            exit();
        }
    }   

    $result = $leaveTypeDAL->getLeaveType();
    $employee = $EmployeeDAL->getAll();

   
    include '../../Common/Header.php';
?>
<style>
    .table th:nth-child(5),
    .table td:nth-child(5) {
        min-width: 150px;
        max-width: 300px;
        width: 200px; /* Giá trị trung bình giữa min và max */
    }
    button {
        padding: 10px !important; 
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
                <div class="col-12 col-md-12 p-lg-3">
                    <div class="aluxubu shadow rounded p-2" >
                        <h2 class="text-center">請假審核</h2>
                        <div class="m-1 p-3">
                            <form id="search" class="d-flex flex-wrap align-items-center justify-content-center">
                                <div>
                                    <label for="leaveStatus">狀態：</label>
                                    <select class="selectcustom m-1 me-4 p-2 width100"  name="leaveStatus" id="leaveStatus">
                                        <option value="Pending">待審核</option>
                                        <option value="">全部</option>
                                        <option value="Accepted">同意</option>
                                        <option value="Rejected">拒絕</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="time_start">起：</label>
                                    <input type="date" class="inputcustom p-2 width100" name="time_start" id="time_start">
                                </div>
                                <div>
                                    <label for="time_end">至：</label>
                                    <input type="date" class="inputcustom p-2 width100" name="time_end" id="time_end">
                                </div>
                                <div>
                                    <label for="employee_id">人員：</label>
                                    <select class="selectcustom p-2 width100" name="employee_id" id="employee_id">
                                        <option value="0">全部</option>
                                        <?php foreach($employee as $item){
                                            echo "<option value='".$item->Employee_id."'>".$item->Name."</option>";
                                        } ?>
                                    </select>
                                </div>
                            </form>
                            <button class="btn btn-primary p-2 m-1" style="width:70px; font-size:13px" id="search-btn">查詢</button>
                        </div>
                        <div id="LeaveVerify" style="overflow: scroll; min-height: 500px">
                            <table class="table table-striped" id="check_table">
                                <thead class="table">
                                    <tr>
                                        <th scope="col">編號</th>
                                        <th scope="col">人員</th>
                                        <th scope="col">請假類型</th>
                                        <th scope="col">起日</th>
                                        <th scope="col">結束日</th>
                                        <th scope="col">狀態</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead >
                                <tbody id="tbody" style=" padding: 10px">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>     
            </div>
        </div>
    </div>
    <input type="hidden" id="userid" value="<?php echo $userId ?>">

<script src="LeaveCheck.js"></script>

<?php
    include '../../Common/Footer.php';
?>
</body>