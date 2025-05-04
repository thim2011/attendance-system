<?php
    require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/EmployeeDAL.php';

    $EmployeeDAL    = new EmployeeDAL();
    
    if(!isset($_SESSION['auth_token']) || !isset($_COOKIE['auth_token_test']) || !isset($_SESSION['USERid'])){
        header('Location: ../User/Login.php');
        exit();
    }
    else{ 
        $userId= $_SESSION['USERid'];
        $role= $EmployeeDAL->checkRole($userId, 1);
    }   

    $Employees       = $EmployeeDAL->getAll();

    include '../../Common/Header.php';
?>
<style>
    .excel-form{
        width: 50%;
    }
    @media screen and (max-width: 768px) {
        .excel-form{
            width: 100%;
        }
        
    }
    .excel-label{
        min-width:70px;
        margin-right: 10px;
    }
    .excel-checkbox{
        width:20px;
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
            <div class="row m-1 m-md-3">
                <div class="col-12 col-md-12 p-1 p-lg-2">
                    <div class="aluxubu shadow rounded p-3" style="min-height: 300px">
                        <h3 class="d-flex">打卡時間匯出</h3>
                        <div class="excel-form">
             
                            <form class="d-flex flex-column" id="excel-form">
                                <div id="employee-container">
                                    <div class="d-flex m-1 copy-group" >
                                     <!--<span id="addButton" class="btn btn-danger"><i class="fa-solid fa-plus"></i></span>-->
                                       <label for="Employee" class="excel-label text-end">員工: </label>
                                        <select class="selectcustom p-2" name="Employee" id="Employee">
                                            <?php 
                                            if($role){
                                                echo '<option value="all">全部</option>';
                                                foreach($Employees as $key => $value){
                                                    echo '<option value="'.$value->Employee_id.'">'.$value->Name.'</option>';
                                                }
                                            }
                                            else{
                                                echo '<option value="'.$userId.'">'.$_SESSION['USERname'].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="d-flex m-1">
                                    <label for="export_time" class="excel-label text-end">時間: </label>
                                    <input type="date" class="inputcustom p-2 " name="StartDate" id="StartDate">~
                                    <input type="date" class="inputcustom p-2 " name="EndDate" id="EndDate">
                                </div>
                                
                                <div class="d-flex m-1">
                                    <label for="Language" class="excel-label text-end">語言: </label>
                                
                                    <select class="selectcustom p-2 width100" name="Language" id="Language">
                                        <option value="chinese">中文</option>
                                        <option value="english">英文</option>
                                    </select>
                                </div>
                                <!--<div class="d-flex m-1">
                                    <label for="isChart" class="excel-label text-end">是否要統計: </label> 
                                    <input type="checkbox" class="excel-checkbox" name="isChart" id="isChart">需要
                                </div>   -->
                            </form> 
                            <button class="btn btn-secondary m-2" style="font-size: 16px" id="HandsonBtn">記錄預覽</button>
                        </div>
                    </div>               
                </div>

                <div class="col-12 col-md-12 mt-1 p-1 p-lg-2">
                    <div class="aluxubu shadow rounded p-1" style="overflow: scroll; color: black">
                        <div class="d-flex justify-content-around mb-1">
                            <ul class="nav nav-tabs text-dark" id="sheetTabs" role="tablist"></ul>
                            <button class="btn btn-primary hide" style="font-size: 15px" id="ExportBtn" onclick="myApp.ExportPunchin(XLSX)" ><i class="fa-solid fa-download"></i> 匯出表格 </button> 
                        </div>
                        <div id="handson"></div>    
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="userid" value="<?php echo $userId ?>">
    </div>

    <script src="ExcelExport.js"></script>

<?php
    include '../../Common/Footer.php';
?>