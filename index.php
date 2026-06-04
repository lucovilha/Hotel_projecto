<?php 
 require_once 'includes/session.php'; 
 require_once 'includes/helpers.php'; 
 ?> 
 <!DOCTYPE html> 
 <html lang="pt"> 
 <head> 
     <meta charset="UTF-8"> 
     <title>Hotel</title> 
     <link rel="stylesheet" href="includes/style.css"> 
 </head> 
 <body> 
     <header> 
         <a href="index.php">Hotel</a> 
         <a href="sobre.php">Sobre Nós</a> 
         <?php if (esta_logado()): ?> 
             <?php if (e_staff()): ?> 
                 <a href="admin/index.php">Backoffice</a> 
             <?php else: ?> 
                 <a href="cliente/reservas.php">As Minhas Reservas</a> 
             <?php endif; ?> 
             <a href="auth/logout.php">Sair</a> 
         <?php else: ?> 
             <a href="auth/login.php">Entrar</a> 
             <a href="auth/register.php">Registar</a> 
         <?php endif; ?> 
     </header> 
     <main> 
         <h1>Bem-vindo ao Hotel</h1> 
         <p>O melhor hotel para a tua estadia.</p> 
        <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800" 
             alt="Hotel" style="width:100%; border-radius:8px; margin-top:15px;"> 
     </main> 
     <footer> 
         <p>&copy; 2026 Hotel. Todos os direitos reservados.</p> 
     </footer> 
 </body> 
 </html>
