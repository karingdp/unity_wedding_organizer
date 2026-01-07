<?php
session_start();
session_unset();
session_destroy();
// Bersihkan juga cookie session jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
echo "<script>alert('Sesi telah direset. Silakan login ulang.'); location='login.php';</script>";
?>