<?php
session_start();

session_unset();
session_destroy();

if (isset($_COOKIE['auth_token_test'])) {
    unset($_COOKIE['auth_token_test']);
    setcookie('auth_token_test', '', time() - 3600, '/'); 
}

echo "<script>
    sessionStorage.removeItem('auth_token_test');
    console.log('登出成功');
    location.href = 'Login.php';
</script>";

exit();
?>
