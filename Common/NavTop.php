

<nav class="navbar navbar-expand-md navbar-light ">
    <div class="container-fluid d-flex flex-row">
        <div class="d-flex">
            <button class="btn px-1 py-0 open-btn d-lg-none me-2">
                <span><i class="fa-solid fa-bars-staggered"></i></span>
            </button>    
        </div>
        
        <div class="ml-auto d-flex">
            <div class="NotifiNav">
                <span class="text-light" id="nav_noti_count"></span>
                <button class="btn p-2 mx-1" id="Notification-Btn" >
                    <span ><i class="fa-solid fa-bell"></i></span>
                </button>
            </div>
            <?php include '../../Common/Notification.php'; ?>
            
            <div class="dropdown dropstart"  >
                <button class="btn btn-light dropdown-toggle d-flex align-items-center p-2" type="button" id="MenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                
                    <?php echo isset($_SESSION['USERname']) ? $_SESSION['USERname'] : '???' ?>
                </button>
                
 
                <ul id="logout-bg" class="dropdown-menu " style="width:50px" aria-labelledby="MenuButton" >
                    <li class="">
                        <div class="dropdown-item d-flex">
                            <i class="fa-solid fa-sun mt-1 mx-1"></i>
                            
                            <div class="switch-themes">
                                <input type="checkbox" id="sw-checkbox" hidden>
                                <label for="sw-checkbox"></label>
                            </div>
                            <i class="fa-solid fa-moon mt-1 mx-1"></i> 
                        </div>
                        
                    </li>
                    <li><button class="btn btn-link dropdown-item mt-2"><a href="../User/LogOut.php"><i class="fa-solid fa-right-from-bracket"></i> 登出</a></button></li>
                    
                </ul>
            </div> 
        </div>
    </div>
</nav>

