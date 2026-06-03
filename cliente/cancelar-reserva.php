<?php 
 require_once '../includes/db.php'; 
 require_once '../includes/session.php'; 
 require_once '../includes/helpers.php'; 
 
 exigir_login('../auth/login.php'); 
 
 $id  = (int)($_GET['id'] ?? 0); 
 $uid = user_id(); 
 
 // Verificar que a reserva pertence ao utilizador 
 $stmt = mysqli_prepare($conn, 
     "SELECT r.id, r.data_inicio, r.estado 
      FROM reservas r 
      JOIN hospedes h ON r.hospede_id = h.id 
      WHERE r.id = ? AND h.utilizador_id = ? LIMIT 1"); 
 mysqli_stmt_bind_param($stmt, 'ii', $id, $uid); 
 mysqli_stmt_execute($stmt); 
 $reserva = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); 
 
 if (!$reserva) { 
     redirecionar('reservas.php'); 
 } 
 
 // Verificar regra das 24 horas 
 $inicio = new DateTime($reserva['data_inicio']); 
 $agora  = new DateTime(); 
 $horas  = ($inicio->getTimestamp() - $agora->getTimestamp()) / 3600; 
 
 if ($horas <= 24) { 
     redirecionar('reservas.php'); 
 } 
 
 if ($reserva['estado'] === 'cancelada') { 
     redirecionar('reservas.php'); 
 } 
 
 if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
     $stmt2 = mysqli_prepare($conn, 
         "UPDATE reservas SET estado = 'cancelada' WHERE id = ?"); 
     mysqli_stmt_bind_param($stmt2, 'i', $id); 
     mysqli_stmt_execute($stmt2); 
     redirecionar('reservas.php'); 
 } 
 ?> 
 <!DOCTYPE html> 
 <html lang="pt"> 
 <head> 
     <meta charset="UTF-8"> 
     <title>Cancelar Reserva</title> 
 </head> 
 <body> 
     <h1>Cancelar Reserva</h1> 
     <p>Tens a certeza que queres cancelar a reserva de 
        <strong><?= h($reserva['data_inicio']) ?></strong>?</p> 
     <p style="color:orange">Atenção: só podes cancelar até 24 horas antes do check-in.</p> 
     <form method="POST"> 
         <button type="submit">Sim, cancelar</button> 
         <a href="reservas.php">Não, voltar</a> 
     </form> 
 </body> 
 </html> 
