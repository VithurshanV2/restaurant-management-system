<?php
require "../../includes/session.php";
session_unset();
session_destroy();
header("Location: ../home/home.php");
exit();
