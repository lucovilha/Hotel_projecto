<?php 
 require_once '../includes/db.php'; 
 require_once '../includes/session.php'; 
 require_once '../includes/helpers.php'; 
 
 exigir_role(['gestor', 'rececionista'], '../auth/login.php'); 
 
 $id = (int)($_GET['id'] ?? 0); 
 $erro = ''; 
 
 $stmt = mysqli_prepare($conn, 
     "SELECT r.id, r.estado, r.checkin_feito, r.checkout_feito, 
             r.data_inicio, r.data_fim, r.tipo_quarto_id, 
             h.nome_completo, tq.nome AS tipo 
      FROM reservas r 
      JOIN hospedes h ON r.hospede_id = h.id 
      JOIN tipos_quarto tq ON r.tipo_quarto_id = tq.id 
      WHERE r.id = ? LIMIT 1"); 
 mysqli_stmt_bind_param($stmt, 'i', $id); 
 mysqli_stmt_execute($stmt); 
 $reserva = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); 
 
 if (!$reserva) { 
     redirecionar('reservas.php'); 
 } 
 
 if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
     $acao = $_POST['acao'] ?? ''; 
 
     if ($acao === 'checkin' && !$reserva['checkin_feito']) { 
         $stmt2 = mysqli_prepare($conn, 
             "SELECT id FROM quartos 
              WHERE tipo_quarto_id = ? AND estado = 'livre' LIMIT 1"); 
         mysqli_stmt_bind_param($stmt2, 'i', $reserva['tipo_quarto_id']); 
         mysqli_stmt_execute($stmt2); 
         $quarto = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2)); 
 
         if (!$quarto) { 
             $erro = 'Não há quartos livres disponíveis para este tipo.'; 
         } else { 
             $qid = $quarto['id']; 
             $uid = user_id(); 
 
             $stmt3 = mysqli_prepare($conn, 
                 "UPDATE reservas SET checkin_feito = 1, estado = 'ativa', quarto_id = ? 
                  WHERE id = ?"); 
             mysqli_stmt_bind_param($stmt3, 'ii', $qid, $id); 
             mysqli_stmt_execute($stmt3); 
 
             $stmt4 = mysqli_prepare($conn, 
                 "UPDATE quartos SET estado = 'ocupado' WHERE id = ?"); 
             mysqli_stmt_bind_param($stmt4, 'i', $qid); 
             mysqli_stmt_execute($stmt4); 
 
             $desc = "Check-in efetuado para reserva #$id por " . user_nome(); 
             $stmt5 = mysqli_prepare($conn, 
                 "INSERT INTO logs (acao, descricao, utilizador_id, referencia_id, referencia_tipo) 
                  VALUES ('checkin', ?, ?, ?, 'reserva')"); 
             mysqli_stmt_bind_param($stmt5, 'sii', $desc, $uid, $id); 
             mysqli_stmt_execute($stmt5); 
 
             redirecionar('reservas.php'); 
         } 
 
     } elseif ($acao === 'checkout' && $reserva['checkin_feito'] && !$reserva['checkout_feito']) { 
         $uid = user_id(); 
 
         $stmt6 = mysqli_prepare($conn, 
             "UPDATE reservas SET checkout_feito = 1, estado = 'concluida' WHERE id = ?"); 
         mysqli_stmt_bind_param($stmt6, 'i', $id); 
         mysqli_stmt_execute($stmt6); 
 
         $stmt7 = mysqli_prepare($conn, 
             "UPDATE quartos SET estado = 'livre' WHERE id = 
              (SELECT quarto_id FROM reservas WHERE id = ?)"); 
         mysqli_stmt_bind_param($stmt7, 'i', $id); 
         mysqli_stmt_execute($stmt7); 
 
         $desc = "Check-out efetuado para reserva #$id por " . user_nome(); 
         $stmt8 = mysqli_prepare($conn, 
             "INSERT INTO logs (acao, descricao, utilizador_id, referencia_id, referencia_tipo) 
              VALUES ('checkout', ?, ?, ?, 'reserva')"); 
         mysqli_stmt_bind_param($stmt8, 'sii', $desc, $uid, $id); 
         mysqli_stmt_execute($stmt8); 
 
         redirecionar('reservas.php'); 
     } 
 } 
 ?> 
 <!DOCTYPE html> 
 <html lang="pt"> 
 <head> 
     <meta charset="UTF-8"> 
     <title>Check-in / Check-out</title> 
     <link rel="stylesheet" href="../includes/style.css"> 
 </head> 
 <body> 
     <header> 
         <a href="index.php">Dashboard</a> 
         <a href="reservas.php">Reservas</a> 
         <a href="../auth/logout.php">Sair</a> 
     </header> 
     <main> 
         <h1>Check-in / Check-out</h1> 
         <?php if ($erro): ?> 
             <p class="erro"><?= h($erro) ?></p> 
         <?php endif; ?> 
         <p><strong>Hóspede:</strong> <?= h($reserva['nome_completo']) ?></p> 
         <p><strong>Tipo:</strong> <?= h($reserva['tipo']) ?></p> 
         <p><strong>Check-in:</strong> <?= h($reserva['data_inicio']) ?></p> 
         <p><strong>Check-out:</strong> <?= h($reserva['data_fim']) ?></p> 
         <p><strong>Estado:</strong> <?= h($reserva['estado']) ?></p> 
         <br> 
         <?php if (!$reserva['checkin_feito']): ?> 
             <form method="POST"> 
                 <input type="hidden" name="acao" value="checkin"> 
                 <button type="submit">Efetuar Check-in</button> 
             </form> 
         <?php elseif (!$reserva['checkout_feito']): ?> 
             <form method="POST"> 
                 <input type="hidden" name="acao" value="checkout"> 
                 <button type="submit">Efetuar Check-out</button> 
             </form> 
         <?php else: ?> 
             <p>Esta reserva já foi concluída.</p> 
         <?php endif; ?> 
         <br> 
         <a href="reservas.php">Voltar</a> 
     </main> 
     <footer> 
         <p>&copy; 2026 Hotel. Todos os direitos reservados.</p> 
     </footer> 
 </body> 
 </html>