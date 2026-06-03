<?php 
 require_once 'includes/session.php'; 
 require_once 'includes/helpers.php'; 
 ?> 
 <!DOCTYPE html> 
 <html lang="pt"> 
 <head> 
     <meta charset="UTF-8"> 
     <title>Hotel</title> 
 </head> 
 <body> 
     <h1>Bem-vindo ao Hotel</h1> 
    <a href="sobre.php">Sobre Nós</a> 
    <hr> 
     <?php if (esta_logado()): ?> 
         <p>Olá, <?= h(user_nome()) ?>!</p> 
         <?php if (e_staff()): ?> 
             <a href="admin/index.php">Ir para o Backoffice</a> 
         <?php else: ?> 
             <a href="cliente/reservas.php">As Minhas Reservas</a> 
         <?php endif; ?> 
         <br> 
         <a href="auth/logout.php">Sair</a> 
     <?php else: ?> 
         <a href="auth/login.php">Entrar</a> | 
         <a href="auth/register.php">Registar</a> 
     <?php endif; ?> 
 </body> 
 </html> 
