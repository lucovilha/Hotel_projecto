<?php 
 require_once '../includes/session.php'; 
 require_once '../includes/helpers.php';
 
 session_destroy(); 
 redirecionar('../auth/login.php'); 
 ?>