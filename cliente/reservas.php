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
 </head> 
 <body> 
     <h1>As Minhas Reservas</h1> 
     <p>Olá, <?= h(user_nome()) ?></p> 
     <a href="nova-reserva.php">Fazer Nova Reserva</a> | 
     <a href="../auth/logout.php">Sair</a> 
 
     <hr> 
 
     <?php if (mysqli_num_rows($reservas) === 0): ?> 
         <p>Ainda não tens reservas.</p> 
     <?php else: ?> 
         <table border="1"> 
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
                     $diff = $agora->diff($inicio)->h + ($agora->diff($inicio)->days * 24); 
                     $pode_editar = $r['estado'] !== 'cancelada' && $diff > 24; 
                     ?> 
                     <?php if ($pode_editar): ?> 
                         <a href="cancelar-reserva.php?id=<?= $r['id'] ?>">Cancelar</a> 
                     <?php endif; ?> 
                 </td> 
             </tr> 
             <?php endwhile; ?> 
         </table> 
     <?php endif; ?> 
 </body> 
 </html>