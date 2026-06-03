<?php 
 require_once '../includes/db.php'; 
 require_once '../includes/session.php'; 
 require_once '../includes/helpers.php'; 
 
 exigir_role(['gestor', 'rececionista'], '../auth/login.php'); 
 
 $reservas = mysqli_query($conn, 
     "SELECT r.id, h.nome_completo, tq.nome AS tipo, 
             r.data_inicio, r.data_fim, r.num_hospedes, 
             r.estado, r.total_estimado, r.checkin_feito, r.checkout_feito 
      FROM reservas r 
      JOIN hospedes h ON r.hospede_id = h.id 
      JOIN tipos_quarto tq ON r.tipo_quarto_id = tq.id 
      ORDER BY r.data_inicio DESC"); 
 ?> 
 <!DOCTYPE html> 
 <html lang="pt"> 
 <head> 
     <meta charset="UTF-8"> 
     <title>Gestão de Reservas</title> 
 </head> 
 <body> 
     <h1>Gestão de Reservas</h1> 
     <a href="index.php">Dashboard</a> | 
    <a href="nova-reserva.php">Nova Reserva</a> | 
    <a href="../auth/logout.php">Sair</a> 
     <hr> 
     <table border="1"> 
         <tr> 
             <th>ID</th> 
             <th>Hóspede</th> 
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
             <td><?= $r['id'] ?></td> 
             <td><?= h($r['nome_completo']) ?></td> 
             <td><?= h($r['tipo']) ?></td> 
             <td><?= h($r['data_inicio']) ?></td> 
             <td><?= h($r['data_fim']) ?></td> 
             <td><?= $r['num_hospedes'] ?></td> 
             <td><?= h($r['estado']) ?></td> 
             <td>€<?= number_format($r['total_estimado'], 2) ?></td> 
             <td> 
                 <a href="checkin.php?id=<?= $r['id'] ?>">Check-in/out</a> | 
                 <a href="pagamentos.php?reserva_id=<?= $r['id'] ?>">Pagamentos</a> 
             </td> 
         </tr> 
         <?php endwhile; ?> 
     </table> 
 </body> 
 </html> 
