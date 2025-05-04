<?php 
    require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/HolidaysDAL.php';
    require_once dirname(__FILE__).'/../../DAL/SettingDAL.php';
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';

    $HolidaysDAL = new HolidaysDAL();
    $SettingDAL = new SettingDAL();
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

    $year= date("Y");
    $year_month= date("Y-m");   
    $employee = $EmployeeDAL->getAll();

    $distinctyear = $HolidaysDAL->getdistinctYear();

    $BreakTime = $SettingDAL->getBreakTime();
    $WorkTime = $SettingDAL->getWorkTime();

    include '../../Common/Header.php'; 
?>
<style>
.table th:nth-child(6),
.table td:nth-child(6) {
    min-width: 100px;
    max-width: 200px;
    width: 13px;
}

/*.table th:nth-child(1),
    .table td:nth-child(1) {
        min-width: 30px;
        max-width: 30px;
        width: 30px; 
    }*/

input[type="text"] {
    width: 300px;

    background-color: #A9A9A9;
}
</style>

<body>
    <div class="main-container d-flex">
        <div class="sidebar" id="side_nav">
            <?php include '../../Common/SideBar.php'; ?>
        </div>
        <div class="content">
            <?php include '../../Common/NavTop.php'; ?>
            <?php include '../../Common/Chatbot.php'; ?>
            <div class="row m-1 m-md-2">
                <div class="col-12 col-md-12 p-0 p-lg-2">
                    <div class="aluxubu shadow rounded p-0">
                        <!-- -->
                        <nav class="navbar navbar-expand navbar-light">
                            <div class="container-fluid">
                                <div class="collapse navbar-collapse" id="navbarNav">
                                    <ul class="navbar-nav">
                                        <li class="nav-item">
                                            <a class="nav-link active" type-menu="general-setting">一般設定</a>
                                        </li>
                                        <li class="nav-item active">
                                            <a class="nav-link" type-menu="permissions-setting">權限管理</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link active" type-menu="punchtime-setting">打卡時間</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link " type-menu="fixed-leave">員工固定排假</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" type-menu="holiday-setting">假期</a>
                                        </li>


                                    </ul>
                                </div>
                            </div>
                        </nav>
                        <!---------------------------------------------------------------------------------->
                        <div class="setting-list p-2">
                            <div class="setting-item" type-item="general-setting">
                                <h2>設定</h2>
                                <h5 style="text-align: left;">上下班時間</h5>
                                <div class="d-flex">
                                    <div class="input-group mb-3" style="width:300px">
                                        <span class="input-group-text" id="basic-addon1">上班時間</span>
                                        <input type="text" class="form-control" id="WorkStartTime"
                                            value="<?php echo $WorkTime->Work_start_time ?>"
                                            aria-describedby="basic-addon1" readonly>
                                    </div>
                                    -
                                    <div class="input-group mb-3" style="width:300px">
                                        <input type="text" class="form-control" id="WorkEndTime"
                                            value="<?php echo $WorkTime->Work_end_time ?>"
                                            aria-describedby="basic-addon1" readonly>
                                        <span class="input-group-text" id="basic-addon1">下班時間</span>
                                    </div>
                                </div>
                                <button id="WorkTime_Edit" class="btn btn-primary mx-1 mb-4">修改</button>
                                <button id="WorkTime_Btn" class="btn btn-primary mx-1 mb-4">設定</button>

                                <!---------------------------------------------------------------------------------->

                                <h5 style="text-align: left;">休息時間</h5>
                                <div class="d-flex">
                                    <div class="input-group mb-3" style="width:300px">
                                        <span class="input-group-text" id="basic-addon1">休息開始</span>
                                        <input type="text" class="form-control" id="BreakStartTime"
                                            value="<?php echo $BreakTime->Break_start_time ?>" aria-label="Username"
                                            aria-describedby="basic-addon1" readonly>
                                    </div>
                                    -
                                    <div class="input-group mb-3" style="width:300px">
                                        <input type="text" class="form-control" id="BreakEndTime"
                                            value="<?php echo $BreakTime->Break_end_time ?>" aria-label="Username"
                                            aria-describedby="basic-addon1" readonly>
                                        <span class="input-group-text" id="basic-addon1">休息結束</span>
                                    </div>
                                </div>
                                <button id="BreakTime_Edit" class="btn btn-primary mx-1 mb-4">修改</button>
                                <button id="BreakTime_Btn" class="btn btn-primary mx-1 mb-4">設定</button>
                                <!---------------------------------------------------------------------------------->
                            </div>
                            <!---------------------------------------------------------------------------------->
                            <div class="setting-item hide" style="overflow: scroll; min-height: 500px"
                                type-item="holiday-setting">
                                <h2>假期設定</h2>
                                <div class="d-flex">
                                    <select id="select-year" class="form-select form-select-sm">
                                        <?php
                                            $currentYear = date("Y");
                                            foreach($distinctyear as $item){
                                                $selected = ($item->Year == $currentYear) ? "selected" : "";
                                                echo "<option value='".$item->Year."' $selected>".$item->Year."</option>";
                                            }
                                        ?>
                                    </select>

                                    <select id="select-month" class="form-select form-select-sm">
                                        <?php
                                            $currentMonth = date("n");
                                            for($i = 1; $i <= 12; $i++){
                                                $selected = ($i == $currentMonth) ? "selected" : "";
                                                echo "<option value='$i' $selected>".$i."月</option>";
                                            }
                                        ?>
                                    </select>
                                    <button class="btn btn-primary w-25" id="insertHoliday"><i class="fa-solid fa-plus"
                                            style="font-size:15px"></i>添加</button>
                                </div>
                                <table class="table table-striped ">
                                    <thead class="table">
                                        <tr>
                                            <th scope="col"></th>
                                            <th scope="col">日期</th>
                                            <th scope="col">假名</th>
                                            <th scope="col">是否放假</th>
                                            <th scope="col">類別</th>
                                            <th scope="col">描述</th>
                                            <th scope="col">資料來源</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody" style="padding:10px">

                                    </tbody>
                                </table>
                            </div>

                            <div class="setting-item hide" type-item="permissions-setting" style="overflow: scroll;">
                                <h2>權限管理</h2>
                                <table class="table table-striped ">
                                    <thead class="table">
                                        <tr>
                                            <th scope="col">編號</th>
                                            <th scope="col">姓名</th>
                                            <th scope="col">信箱</th>
                                            <th scope="col">權限</th>
                                            <th scope="col">帳號狀態</th>
                                            <th scope="col">編輯</th>
                                        </tr>
                                    </thead>
                                    <tbody id="permission-tbody" style="padding:10px">

                                    </tbody>
                                </table>
                            </div>

                            <div>
                                <div class="setting-item hide" type-item="fixed-leave" style="overflow: scroll;">
                                    <h2>員工固定排假</h2>
                                    <table class="table table-striped ">
                                        <thead class="table">
                                            <tr>
                                                <th scope="col">姓名</th>
                                                <th scope="col"></th>
                                                <th scope="col">星期一</th>
                                                <th scope="col">星期二</th>
                                                <th scope="col">星期三</th>
                                                <th scope="col">星期四</th>
                                                <th scope="col">星期五</th>
                                            </tr>
                                        </thead>
                                        <tbody id="fixed-leave-tbody" style="padding:10px">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                                            
                            <div class="setting-item hide" type-item="punchtime-setting" style="overflow: scroll;">
                                <h2>打卡時間修改</h2>
                                <form id="punch-searchtime" class="d-flex">
                                    <select id="select-employ" name="select-employ" class="form-select form-select-sm">
                                        <option value="0">請選擇員工</option>
                                        <?php
                                            foreach($employee as $item){
                                                echo "<option value='".$item->Employee_id."'>".$item->Name."</option>";
                                            }
                                        ?>
                                    </select>
                                    <input type="month" class="inputcustom" name="PersonalMonth" id="PersonalMonth"
                                        value="<?php echo $year_month?>">
                                </form>

                                <table class="table table-striped ">
                                    <thead class="table">
                                        <tr>
                                            <th scope="col">姓名</th>
                                            <th scope="col">日期</th>
                                            <th scope="col">上班時間</th>
                                            <th scope="col">下班時間</th>
                                            <th scope="col">編輯</th>
                                        </tr>
                                    </thead>
                                    <tbody id="punchtime-tbody" style="padding:10px">

                                    </tbody>
                                </table>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="userid" value="<?php echo $userId ?>">
    </div>

    <script src="Setting.js"></script>
    <?php
    include '../../Common/Footer.php';
?>
</body>