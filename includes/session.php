<?php 
 session_start(); 
 
 function esta_logado() { 
     return isset($_SESSION['user_id']); 
 } 
 
 function user_role() { 
     return $_SESSION['user_role'] ?? ''; 
 } 
 
 function user_nome() { 
     return $_SESSION['user_nome'] ?? ''; 
 } 
 
 function user_id() { 
     return $_SESSION['user_id'] ?? null; 
 } 
 
 function e_gestor() { 
     return user_role() === 'gestor'; 
 } 
 
 function e_staff() { 
     return in_array(user_role(), ['gestor', 'rececionista']); 
 } 
 
 function exigir_login($redirecionar = '../auth/login.php') { 
     if (!esta_logado()) { 
         header('Location: ' . $redirecionar); 
         exit; 
     } 
 } 
 
 function exigir_role($roles, $redirecionar = '../auth/login.php') { 
     exigir_login($redirecionar); 
     if (!in_array(user_role(), $roles)) { 
         header('Location: ' . $redirecionar); 
         exit; 
     } 
 } 
 ?>