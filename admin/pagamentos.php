<?php 
 require_once '../includes/db.php'; 
 require_once '../includes/session.php'; 
 require_once '../includes/helpers.php'; 
 
 exigir_role(['gestor', 'rececionista'], '../auth/login.php'); 
 
 $reserva_id = (int)($_GET['reserva_id'] ?? 0); 
 $erro = ''; 
 
 // Buscar reserva 
 $stmt = mysqli_prepare($conn, 
     "SELECT r.id, r.total_estimado, h.nome_completo 
      FROM reservas r 
      JOIN hospedes h ON r.hospede_id = h.id 
      WHERE r.id = ? LIMIT 1"); 
 mysqli_stmt_bind_param($stmt, 'i', $reserva_id); 
 mysqli_stmt_execute($stmt); 
 $reserva = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); 
 
 if (!$reserva) { 
     redirecionar('reservas.php'); 
 } 
 
 // Total já pago 
 $pago_res = mysqli_query($conn, "SELECT IFNULL(SUM(montante), 0) AS total FROM pagamentos WHERE reserva_id = $reserva_id");
 $pago = mysqli_fetch_assoc($pago_res)['total']; 
 
 $em_falta = $reserva['total_estimado'] - $pago; 
 
 if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
     $montante = (float)($_POST['montante'] ?? 0); 
     $tipo     = limpar($_POST['tipo'] ?? ''); 
     $metodo   = limpar($_POST['metodo'] ?? ''); 
     $uid      = user_id(); 
 
     if ($montante <= 0) { 
         $erro = 'O montante deve ser maior que zero.'; 
     } elseif (!in_array($tipo, ['parcial', 'total'])) { 
         $erro = 'Tipo de pagamento inválido.'; 
     } elseif (!in_array($metodo, ['numerario', 'cartao', 'transferencia'])) { 
         $erro = 'Método de pagamento inválido.'; 
     } else { 
         $stmt2 = mysqli_prepare($conn, 
             "INSERT INTO pagamentos (reserva_id, montante, tipo, metodo, operador_id) 
              VALUES (?, ?, ?, ?, ?)"); 
         mysqli_stmt_bind_param($stmt2, 'idssi', $reserva_id, $montante, $tipo, $metodo, $uid); 
         mysqli_stmt_execute($stmt2); 
 
         $desc = "Pagamento de €$montante registado para reserva #$reserva_id por " . user_nome(); 
         $stmt3 = mysqli_prepare($conn, 
             "INSERT INTO logs (acao, descricao, utilizador_id, referencia_id, referencia_tipo) 
              VALUES ('pagamento', ?, ?, ?, 'pagamento')"); 
         mysqli_stmt_bind_param($stmt3, 'sii', $desc, $uid, $reserva_id); 
         mysqli_stmt_execute($stmt3); 
 
         redirecionar('pagamentos.php?reserva_id=' . $reserva_id); 
     } 
 } 
 
 // Listar pagamentos 
 $pagamentos = mysqli_query($conn, 
     "SELECT p.montante, p.tipo, p.metodo, p.data, u.nome AS operador 
      FROM pagamentos p 
      JOIN utilizadores u ON p.operador_id = u.id 
      WHERE p.reserva_id = $reserva_id 
      ORDER BY p.data DESC"); 
 ?> 
 <!DOCTYPE html> 
 <html lang="pt"> 
 <head> 
     <meta charset="UTF-8"> 
     <title>Pagamentos</title> 
 </head> 
 <body> 
     <h1>Pagamentos — Reserva #<?= $reserva_id ?></h1> 
     <a href="reservas.php">Voltar</a> 
     <hr> 
     <p><strong>Hóspede:</strong> <?= h($reserva['nome_completo']) ?></p> 
     <p><strong>Total:</strong> €<?= number_format($reserva['total_estimado'], 2) ?></p> 
     <p><strong>Pago:</strong> €<?= number_format($pago, 2) ?></p> 
     <p><strong>Em falta:</strong> €<?= number_format($em_falta, 2) ?></p> 
     <hr> 
     <h2>Registar Pagamento</h2> 
     <?php if ($erro): ?> 
         <p style="color:red"><?= h($erro) ?></p> 
     <?php endif; ?> 
     <form method="POST"> 
         <label>Montante (€) 
             <input type="number" name="montante" step="0.01" min="0.01" required> 
         </label><br> 
         <label>Tipo 
             <select name="tipo"> 
                 <option value="parcial">Parcial</option> 
                 <option value="total">Total</option> 
             </select> 
         </label><br> 
         <label>Método 
             <select name="metodo"> 
                 <option value="numerario">Numerário</option> 
                 <option value="cartao">Cartão</option> 
                 <option value="transferencia">Transferência</option> 
             </select> 
         </label><br> 
         <button type="submit">Registar</button> 
     </form> 
     <hr> 
     <h2>Histórico</h2> 
     <table border="1"> 
         <tr> 
             <th>Montante</th> 
             <th>Tipo</th> 
             <th>Método</th> 
             <th>Data</th> 
             <th>Operador</th> 
         </tr> 
         <?php while ($p = mysqli_fetch_assoc($pagamentos)): ?> 
         <tr> 
             <td>€<?= number_format($p['montante'], 2) ?></td> 
             <td><?= h($p['tipo']) ?></td> 
             <td><?= h($p['metodo']) ?></td> 
             <td><?= h($p['data']) ?></td> 
             <td><?= h($p['operador']) ?></td> 
         </tr> 
         <?php endwhile; ?> 
     </table> 
 </body> 
 </html> 
