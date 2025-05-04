<?php
 include '../../Common/Header.php';
?>

<section class="background-radial-gradient overflow-hidden">
  <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
    <div class="row gx-lg-5 align-items-center mb-5">
      <div class="col-lg-6 mb-5 mb-lg-0" style="z-index: 10">
        <h1 class="my-5 display-5 fw-bold ls-tight" style="color: hsl(218, 81%, 95%) !important">
          怡良電機 <br/>
          <span style="color: hsl(218, 81%, 75%) !important">每日點擊打卡系統</span>
        </h1>
      </div>

      <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
        <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
        <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

        <div class="card bg-glass">
          <div class="card-body px-4 py-5 px-md-5">
            <form id="SignUpForm">
              <div class="my-2 display-5 fw-bold ls-tight text-center">注冊帳號</div>
              <!-- 2 column grid layout with text inputs for the first and last names -->
              <div class="row">
                <div class="form-outline mb-4">
                  <label class="form-label" for="Email">電子郵件</label>
                  <input type="email" name="Email" class="form-control" />
                </div>

                <div class="form-outline mb-4">
                  <label class="form-label" for="Account">帳號</label>
                  <input type="text" name="Account" class="form-control" /> 
                </div>

                <div class="form-outline mb-4">
                  <label class="form-label" for="Password">密碼</label>
                  <input type="password" name="Password" class="form-control" />
                </div>

                <div class="form-outline mb-4">
                  <label class="form-label" for="Password">確認密碼</label>
                  <input type="password" name="Re-Password" class="form-control" />
                </div>

                <div class="col-md-6 mb-4">
                  <div class="form-outline">
                    <label class="form-label" for="FirstName">姓</label>
                    <input type="text" name="FirstName" class="form-control" />
                  </div>
                </div>
                <div class="col-md-6 mb-4">
                  <div class="form-outline">
                    <label class="form-label" for="LastName">名</label>
                    <input type="text" name="LastName" class="form-control" />
                  </div>
                </div>
              
                <div class="form-outline mb-4">
                  <label class="form-label" for="Position">部門</label>
                  <input type="text" name="Position" class="form-control" />
                </div>

                <div class="form-outline mb-4">
                  <label class="form-label" for="Department">廠別</label>
                  <input type="text" name="Department" class="form-control" />
                </div>
              </div>
            </form>
            <button id="SignUpBtn" class="btn btn-primary btn-block mb-4">
                注冊
              </button>
              <a class="text-center" href="Login.php">前往登入</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script src="Register.js"></script>
<?php
 include '../../Common/Footer.php';
?>
