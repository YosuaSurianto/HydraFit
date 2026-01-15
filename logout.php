<?php
session_start();
session_destroy(); // Hancurkan sesi login
header("Location: index.html"); // Balik ke halaman utama
exit();
?>