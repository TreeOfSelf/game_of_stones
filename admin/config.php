<?php
error_reporting(E_ERROR | E_PARSE);
// User stuff stored in cookies
$id = $_COOKIE['id'];
$name = $_COOKIE['name'];
$lastname = $_COOKIE['lastname'];
$password = $_COOKIE['password'];
$mode = $_COOKIE['mode'];

// URL
$subfile = "";
$server_name = "http://".$_SERVER['SERVER_NAME'];

// MySQL Database5 config info
$database_name = "";
$database_username = "";
$database_password = "";
$database_server = "";

// Admin password for updating donors
$admin_username = "";
$admin_password = "";
?>