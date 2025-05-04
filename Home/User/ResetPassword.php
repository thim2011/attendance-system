<?php 
    require_once dirname(__FILE__).'/../../Utils/Main.php';
    require_once dirname(__FILE__).'/../../DAL/ResetPasswordDAL.php';

    $ResetPasswordDAL = new ResetPasswordDAL();

    $token = isset($_GET['token']) ? $_GET['token'] : '';    

    $VerifyToken = $ResetPasswordDAL->getEmailbyToken($token);

    if($VerifyToken == null){
        echo "已過期, 請重新申請";
        exit();
    }

 include '../../Common/Header.php';
?>

<section class="background-radial-gradient overflow-hidden">
  <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card bg-glass">
                <div class="card-body px-4 py-5 px-md-5">
                    <h2 class="mb-4">重設密碼</h2>
                    <form id="reset_form">
                        <div data-mdb-input-init class="form-outline mb-4">
                            <input type="email" class="form-control" id="newPass" name="newPass" required>
                            <label for="newPass" class="form-label">輸入新密碼</label>
                        </div>
                        <input type="hidden" name="token" value="<?php echo $token ?>">
                    </form>
                    <button id="ResetPassword" class="btn btn-primary mb-4">重設密碼</button><br>
                    <a href="Login.php">回登入頁</a>
                </div>
            </div>
        </div>
    </div>
  </div>
</section>
<script src="ResetPassword.js"></script>
<?php
 include '../../Common/Footer.php';
?>