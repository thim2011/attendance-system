
<?php
 include '../../Common/Header.php';
?>
<body>
<section class="background-radial-gradient overflow-hidden">
  <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
    <div class="row gx-lg-5 align-items-center mb-5">
      <div class="col-lg-6 mb-5 mb-lg-0" style="z-index: 10">
        <h1 class="my-5 display-5 fw-bold ls-tight" style="color: hsl(218, 81%, 95%) !important">
          怡良電機 <br />
          <span style="color: hsl(218, 81%, 75%) !important">每日點擊打卡系統</span>
        </h1>
      </div>

      <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
        <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
        <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

        <div class="card bg-glass">
          <div class="card-body px-4 py-5 px-md-5">
            <form id="LoginForm">
                <div class="my-2 display-5 fw-bold ls-tight text-center">登入</div>
                    <div class="row">
                        <div  class="form-outline mb-4">
                          <label class="form-label" for="Account">帳號</label>
                          <input type="text" name="Account" class="form-control" />
                        </div>

                        <div class="form-outline mb-4">
                          <label class="form-label" for="Password">密碼</label>
                          <input type="password" name="Password" class="form-control" />
                        </div>
                    </div>
                    <p id="notice" class="text-danger"></p>
            </form>
            <button id="LoginBtn" class="btn btn-primary btn-block mb-4">
                登入
              </button>
              <a class="text-center m-3" href="Register.php">前往注冊</a>
              <a class="text-center" href="ForgotPass.php">忘記密碼</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script src="Login.js"></script>
<?php
 include '../../Common/Footer.php';
?>
    
