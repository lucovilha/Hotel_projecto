<?php 
 require_once '../includes/db.php'; 
 require_once '../includes/session.php'; 
 require_once '../includes/helpers.php'; 
 
 exigir_role(['gestor', 'rececionista'], '../auth/login.php'); 
 
 $hoje = date('Y-m-d'); 
 
 $ocupados = mysqli_fetch_assoc(mysqli_query($conn, 
     "SELECT COUNT(*) AS total FROM quartos WHERE estado = 'ocupado'" 
 ))['total']; 
 
 $total_quartos = mysqli_fetch_assoc(mysqli_query($conn, 
     "SELECT COUNT(*) AS total FROM quartos" 
 ))['total']; 
 
 $reservas_ativas = mysqli_fetch_assoc(mysqli_query($conn, 
     "SELECT COUNT(*) AS total FROM reservas WHERE estado = 'ativa'" 
 ))['total']; 
 
 $checkins_hoje = mysqli_fetch_assoc(mysqli_query($conn, 
     "SELECT COUNT(*) AS total FROM reservas 
      WHERE data_inicio = '$hoje' AND checkin_feito = 0 AND estado IN ('pendente','ativa')" 
 ))['total']; 
 ?> 
 <!DOCTYPE html> 
 <html lang="pt"> 
 <head> 
     <meta charset="UTF-8"> 
     <title>Dashboard</title> 
     <link rel="stylesheet" href="../includes/style.css"> 
 </head> 
 <body> 
     <header> 
         <a href="index.php">Dashboard</a> 
         <a href="reservas.php">Reservas</a> 
         <a href="checkin.php">Check-in/out</a> 
         <a href="pagamentos.php">Pagamentos</a> 
         <a href="hospedes.php">Hóspedes</a> 
         <?php  if (e_gestor()): ?> 
             <a href="quartos.php">Quartos</a> 
             <a href="tipos-quarto.php">Tipos de Quarto</a> 
             <a href="relatorios.php">Relatórios</a> 
         <?php  endif; ?> 
         <a href="logs.php">Logs</a> 
         <a href="../auth/logout.php">Sair</a> 
     </header> 
     <main> 
         <h1>Dashboard</h1> 
         <p>Bem-vindo, <?=  h(user_nome()) ?>  ( <?=  h(user_role()) ?> )</p> 
         <table> 
             <tr> 
                 <th>Quartos Ocupados</th> 
                 <th>Reservas Ativas</th> 
                 <th>Check-ins Hoje</th> 
             </tr> 
             <tr> 
                 <td> <?=  $ocupados ?> / <?=  $total_quartos ?> </td> 
                 <td> <?=  $reservas_ativas ?> </td> 
                 <td> <?=  $checkins_hoje ?> </td> 
             </tr> 
         </table> 
     </main> 
     <footer> 
         <p>&copy; 2026 Hotel. Todos os direitos reservados.</p> 
     </footer> 
 </body> 
 </html>