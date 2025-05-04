<?php
 include '../../Common/Header.php';
?>

<section class="background-radial-gradient overflow-hidden">
  <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card bg-glass">
                <div class="card-body px-4 py-5 px-md-5">
                    <h2 class="mb-4">忘記密碼</h2>
                    <form action="reset_password.php" method="POST">
                        <div data-mdb-input-init class="form-outline mb-4">
                            <input type="email" class="form-control" id="email" name="email" required>
                            <label for="email" class="form-label">輸入電子郵件</label>
                        </div>
                        
                    </form>
                    <button id="ForgotPass_Btn" class="btn btn-primary mb-4">取得驗證</button><br>
                    <a href="Login.php">回登入頁</a>
                </div>
            </div>
        </div>
    </div>
  </div>
</section>
<script src="ForgotPass.js"></script>
<?php
 include '../../Common/Footer.php';
?>
