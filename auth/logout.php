<?php

session_start();
session_unset();
session_destroy();
header("Location: ../pages/home_page.php");
exit();

?>