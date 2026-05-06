<?php
require_once "auth.php";
requireLogin();

header("Location: index.php");
exit;