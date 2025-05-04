<?php
$currentUrl = $_SERVER['REQUEST_URI'];

$pathParts = explode('/', $currentUrl);
$filename = end($pathParts);

$filename = explode('?', $filename)[0];

?>
<div class="header-box  px-2 pt-3 pb-4 d-flex justify-content-center shadow">
        <h1 class="fs-4"><span class="bg-white text-dark rounded shadow px-2 me-2">YL</span> <h2
                class="text-bold">打卡系統</h2></h1>
        <button class="btn close-btn d-lg-none px-1 py-0 text-dark"><span><i class="fa-solid fa-bars-staggered"></i></span></button>
</div>

<ul class="list-unstyled px-2">
        <li class="<?php echo $filename == 'Index.php' ? 'active' : '' ?>">
                <a href="../Index/Index.php" class="text-decoration-none px-3 py-2 d-block">
                        <i class="fa-solid fa-building-circle-check"></i> 打卡區
                </a>
        </li>
        <li class="<?php echo $filename == 'Personal.php' ? 'active' : '' ?>">
                <a href="../Index/Personal.php" class="text-decoration-none px-3 py-2 d-block">
                        <i class="fa-solid fa-address-card"></i> 個人檔案
                </a>
        </li>
        <?php if( $_SESSION['Role'] == 1) { ?>
                <li class="<?php echo $filename == 'LeaveCheck.php' ? 'active' : '' ?> d-flex align-items-center">
                        <a href="../Index/LeaveCheck.php" class="text-decoration-none px-3 py-2 d-block">
                                <i class="fa-solid fa-circle-check"></i> 審核區
                        </a>
                        <span class="text-light text-center bg-danger rounded-circle" id="PendingLeave" style="width:25px"><?php echo isset($_SESSION['pendingLeave']) ? $_SESSION['pendingLeave'] : 0 ?></span>
                </li>
      
        <?php } ?>
        <li class="<?php echo $filename == 'LeaveForm.php' ? 'active' : '' ?>">
                <a href="../Index/LeaveForm.php" class="text-decoration-none px-3 py-2 d-block">
                        <i class="fa-solid fa-circle-check"></i> 請假區
                </a>
        </li>
</ul>

<hr class="h-color mx-2">

<ul class="list-unstyled px-2">
        <li class="<?php echo $filename == 'ExcelExport.php' ? 'active' : '' ?>"><a href="../Index/ExcelExport.php" class="text-decoration-none px-3 py-2 d-block">
                <i class="fa-solid fa-table-cells"></i>
                匯出報表</a>
        </li>
        <?php if( $_SESSION['Role'] == 1) { ?>
        <li class="<?php echo $filename == 'Setting.php' ? 'active' : '' ?>"><a href="../Index/Setting.php" class="text-decoration-none px-3 py-2 d-block">
                <i class="fa-solid fa-gears"></i>
                管理</a>
        </li>
        <?php } ?>

</ul>