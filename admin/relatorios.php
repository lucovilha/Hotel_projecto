<?php 
 require_once '../includes/db.php'; 
 require_once '../includes/session.php'; 
 require_once '../includes/helpers.php'; 
 
 exigir_role(['gestor'], '../auth/login.php'); 
 
 $hoje = date('Y-m-d'); 
 $mes_inicio = date('Y-m-01'); 
 $mes_fim = date('Y-m-t'); 
 
 $ocupados = mysqli_fetch_assoc(mysqli_query($conn, 
     "SELECT COUNT(*) AS total FROM quartos WHERE estado = 'ocupado'" 
 ))['total']; 
 
 $total_quartos = mysqli_fetch_assoc(mysqli_query($conn, 
     "SELECT COUNT(*) AS total FROM quartos" 
 ))['total']; 
 
 $pct = $total_quartos > 0 ? round(($ocupados / $total_quartos) * 100) : 0; 
 
 $receita_mes = mysqli_fetch_assoc(mysqli_query($conn, 
     "SELECT IFNULL(SUM(montante), 0) AS total FROM pagamentos 
      WHERE data BETWEEN '$mes_inicio' AND '$mes_fim 23:59:59'" 
 ))['total']; 
 
 $receita_tipo = mysqli_query($conn, 
     "SELECT tq.nome, IFNULL(SUM(p.montante), 0) AS total 
      FROM tipos_quarto tq 
      LEFT JOIN reservas r ON r.tipo_quarto_id = tq.id 
      LEFT JOIN pagamentos p ON p.reserva_id = r.id 
      GROUP BY tq.id, tq.nome 
      ORDER BY total DESC"); 
 
 $reservas_mes = mysqli_query($conn, 
     "SELECT r.id, h.nome_completo, tq.nome AS tipo, 
             r.data_inicio, r.data_fim, r.estado, r.total_estimado 
      FROM reservas r 
      JOIN hospedes h ON r.hospede_id = h.id 
      JOIN tipos_quarto tq ON r.tipo_quarto_id = tq.id 
      WHERE r.data_inicio BETWEEN '$mes_inicio' AND '$mes_fim' 
      ORDER BY r.data_inicio"); 
 ?> 
 <!DOCTYPE html> 
 <html lang="pt"> 
 <head> 
     <meta charset="UTF-8"> 
     <title>Relatórios</title> 
     <link rel="stylesheet" href="../includes/style.css"> 
 </head> 
 <body> 
     <header> 
         <a href="index.php">Dashboard</a> 
         <a href="reservas.php">Reservas</a> 
         <a href="relatorios.php">Relatórios</a> 
         <a href="logs.php">Logs</a> 
         <a href="../auth/logout.php">Sair</a> 
     </header> 
     <main> 
         <h1>Relatórios</h1> 
 
         <h2>Ocupação Hoje</h2> 
         <p><?= $ocupados ?> de <?= $total_quartos ?> quartos ocupados (<?= $pct ?>%)</p> 
 
         <h2>Receita Este Mês</h2> 
         <p>€<?= number_format($receita_mes, 2) ?></p> 
 
         <h2>Receita por Tipo de Quarto</h2> 
         <table> 
             <tr> 
                 <th>Tipo</th> 
                 <th>Total Recebido</th> 
             </tr> 
             <?php while ($rt = mysqli_fetch_assoc($receita_tipo)): ?> 
             <tr> 
                 <td><?= h($rt['nome']) ?></td> 
                 <td>€<?= number_format($rt['total'], 2) ?></td> 
             </tr> 
             <?php endwhile; ?> 
         </table> 
 
         <h2>Reservas deste Mês</h2> 
         <table> 
             <tr> 
                 <th>ID</th> 
                 <th>Hóspede</th> 
                 <th>Tipo</th> 
                 <th>Check-in</th> 
                 <th>Check-out</th> 
                 <th>Estado</th> 
                 <th>Total</th> 
             </tr> 
             <?php while ($r = mysqli_fetch_assoc($reservas_mes)): ?> 
             <tr> 
                 <td><?= $r['id'] ?></td> 
                 <td><?= h($r['nome_completo']) ?></td> 
                 <td><?= h($r['tipo']) ?></td> 
                 <td><?= h($r['data_inicio']) ?></td> 
                 <td><?= h($r['data_fim']) ?></td> 
                 <td><?= h($r['estado']) ?></td> 
                 <td>€<?= number_format($r['total_estimado'], 2) ?></td> 
             </tr> 
             <?php endwhile; ?> 
         </table> 
     </main> 
     <footer> 
         <p>&copy; 2026 Hotel. Todos os direitos reservados.</p> 
     </footer> 
 </body> 
 </html>