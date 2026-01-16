<?php
session_start();
session_destroy(); // Hancurkan sesi login
header("Location: index.php"); // Balik ke halaman utama
exit();
?>