<?php
session_start();
session_unset();
session_destroy();

header("Location: /Gestion_Stage/index.php");
exit();
?>