<?php 
 require_once '../includes/db.php'; 
 require_once '../includes/session.php'; 
 require_once '../includes/helpers.php'; 
 
 exigir_login('../auth/login.php'); 
 
 $uid = user_id(); 
 
 $stmt = mysqli_prepare($conn, 
     "SELECT r.id, tq.nome AS tipo, r.data_inicio, r.data_fim, 
             r.num_hospedes, r.estado, r.total_estimado, r.checkin_feito 
      FROM reservas r 
      JOIN hospedes h ON r.hospede_id = h.id 
      JOIN tipos_quarto tq ON r.tipo_quarto_id = tq.id 
      WHERE h.utilizador_id = ? 
      ORDER BY r.data_inicio DESC"); 
 mysqli_stmt_bind_param($stmt, 'i', $uid); 
 mysqli_stmt_execute($stmt); 
 $reservas = mysqli_stmt_get_result($stmt); 
 ?> 
 <!DOCTYPE html> 
 <html lang="pt"> 
 <head> 
     <meta charset="UTF-8"> 
     <title>As Minhas Reservas</title> 
     <link rel="stylesheet" href="../includes/style.css"> 
 </head> 
 <body> 
     <header> 
         <a href="../index.php">Hotel</a> 
         <a href="reservas.php">As Minhas Reservas</a> 
         <a href="nova-reserva.php">Nova Reserva</a> 
         <a href="../auth/logout.php">Sair (<?= h(user_nome()) ?>)</a> 
     </header> 
     <main> 
         <h1>As Minhas Reservas</h1> 
         <p style="color:orange">Pode editar/cancelar até 24 horas antes do check-in.</p> 
         <?php if (mysqli_num_rows($reservas) === 0): ?> 
             <p>Ainda não tens reservas. <a href="nova-reserva.php">Faz uma agora</a>.</p> 
         <?php else: ?> 
             <table> 
                 <tr> 
                     <th>Tipo</th> 
                     <th>Check-in</th> 
                     <th>Check-out</th> 
                     <th>Hóspedes</th> 
                     <th>Estado</th> 
                     <th>Total</th> 
                     <th>Ações</th> 
                 </tr> 
                 <?php while ($r = mysqli_fetch_assoc($reservas)): ?> 
                 <tr> 
                     <td><?= h($r['tipo']) ?></td> 
                     <td><?= h($r['data_inicio']) ?></td> 
                     <td><?= h($r['data_fim']) ?></td> 
                     <td><?= h($r['num_hospedes']) ?></td> 
                     <td><?= h($r['estado']) ?></td> 
                     <td>€<?= number_format($r['total_estimado'], 2) ?></td> 
                     <td> 
                         <?php 
                         $inicio = new DateTime($r['data_inicio']); 
                         $agora = new DateTime(); 
                         $horas = ($inicio->getTimestamp() - $agora->getTimestamp()) / 3600; 
                         $pode_editar = $r['estado'] !== 'cancelada' && $horas > 24; 
                         ?> 
                         <?php if ($pode_editar): ?> 
                             <a href="editar-reserva.php?id=<?= $r['id'] ?>">Editar</a> | 
                             <a href="cancelar-reserva.php?id=<?= $r['id'] ?>">Cancelar</a> 
                         <?php else: ?> 
                             — 
                         <?php endif; ?> 
                     </td> 
                 </tr> 
                 <?php endwhile; ?> 
             </table> 
         <?php endif; ?> 
     </main> 
     <footer> 
         <p>&copy; 2026 Hotel. Todos os direitos reservados.</p> 
     </footer> 
 </body> 
 </html>