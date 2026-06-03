<?php 
 require_once 'includes/session.php'; 
 require_once 'includes/helpers.php'; 
 ?> 
 <!DOCTYPE html> 
 <html lang="pt"> 
 <head> 
     <meta charset="UTF-8"> 
     <title>Sobre Nós</title> 
 </head> 
 <body> 
     <h1>Sobre Nós</h1> 
     <a href="index.php">Início</a> | 
     <?php if (esta_logado()): ?> 
         <a href="auth/logout.php">Sair</a> 
     <?php else: ?> 
         <a href="auth/login.php">Entrar</a> 
     <?php endif; ?> 
     <hr> 
     <p>Somos um hotel familiar de pequeno porte, dedicado a proporcionar 
     uma estadia confortável e personalizada a cada hóspede.</p> 
 
     <h2>A Nossa História</h2> 
     <p>Fundado em 2010, o nosso hotel começou como um projeto familiar 
     com o objetivo de oferecer um serviço de qualidade a preços acessíveis.</p> 
 
     <h2>Localização</h2> 
     <p>Estamos situados no centro da cidade, a poucos minutos das principais 
     atrações turísticas e transportes públicos.</p> 
 
     <h2>Contactos</h2> 
     <p>Email: geral@hotel.pt</p> 
     <p>Telefone: +351 210 000 000</p> 
     <p>Morada: Rua do Hotel, nº 1, Lisboa</p> 
 </body> 
 </html>