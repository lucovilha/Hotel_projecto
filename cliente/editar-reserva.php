<?php 
 require_once '../includes/db.php'; 
 require_once '../includes/session.php'; 
 require_once '../includes/helpers.php'; 
 
 exigir_login('../auth/login.php'); 
 
 $id  = (int)($_GET['id'] ?? 0); 
 $uid = user_id(); 
 $erro = ''; 
 
 // Verificar que a reserva pertence ao utilizador 
 $stmt = mysqli_prepare($conn, 
     "SELECT r.id, r.data_inicio, r.data_fim, r.num_hospedes, 
             r.pequeno_almoco, r.tipo_quarto_id, r.estado 
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
 
 if ($horas <= 24 || $reserva['estado'] === 'cancelada') { 
     redirecionar('reservas.php'); 
 } 
 
 $tipos = mysqli_query($conn, "SELECT * FROM tipos_quarto WHERE ativo = 1"); 
 
 if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
     $data_inicio  = limpar($_POST['data_inicio'] ?? ''); 
     $data_fim     = limpar($_POST['data_fim'] ?? ''); 
     $num_hospedes = (int)($_POST['num_hospedes'] ?? 1); 
     $pa           = isset($_POST['pequeno_almoco']) ? 1 : 0; 
     $tipo_id      = (int)($_POST['tipo_quarto_id'] ?? 0); 
 
     if (!$data_inicio || !$data_fim || !$tipo_id) { 
         $erro = 'Preenche todos os campos.'; 
     } elseif ($data_inicio >= $data_fim) { 
         $erro = 'A data de fim deve ser posterior à data de início.'; 
     } elseif ($data_inicio < date('Y-m-d')) { 
         $erro = 'Não podes usar datas no passado.'; 
     } else { 
         $stmt2 = mysqli_prepare($conn, "SELECT * FROM tipos_quarto WHERE id = ? AND ativo = 1"); 
         mysqli_stmt_bind_param($stmt2, 'i', $tipo_id); 
         mysqli_stmt_execute($stmt2); 
         $tipo = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2)); 
 
         if ($num_hospedes > $tipo['capacidade_maxima']) { 
             $erro = 'Número de hóspedes excede a capacidade máxima.'; 
         } else { 
             $noites = calcular_noites($data_inicio, $data_fim); 
             $total  = calcular_total($noites, $num_hospedes, 
                         $tipo['capacidade_base'], $tipo['preco_diaria'], 
                         $tipo['preco_hospede_extra'], $tipo['preco_pequeno_almoco'], $pa); 
 
             $stmt3 = mysqli_prepare($conn, 
                 "UPDATE reservas SET data_inicio = ?, data_fim = ?, 
                  num_hospedes = ?, pequeno_almoco = ?, 
                  tipo_quarto_id = ?, total_estimado = ? 
                  WHERE id = ?"); 
             mysqli_stmt_bind_param($stmt3, 'ssiiddi', 
                 $data_inicio, $data_fim, $num_hospedes, $pa, $tipo_id, $total, $id); 
             mysqli_stmt_execute($stmt3); 
 
             redirecionar('reservas.php'); 
         } 
     } 
 } 
 ?> 
 <!DOCTYPE html> 
 <html lang="pt"> 
 <head> 
     <meta charset="UTF-8"> 
     <title>Editar Reserva</title> 
 </head> 
 <body> 
     <h1>Editar Reserva</h1> 
     <a href="reservas.php">Voltar</a> 
     <p style="color:orange">Só podes editar até 24 horas antes do check-in.</p> 
     <hr> 
     <?php if ($erro): ?> 
         <p style="color:red"><?= h($erro) ?></p> 
     <?php endif; ?> 
     <form method="POST"> 
         <label>Tipo de Quarto 
             <select name="tipo_quarto_id" required> 
                 <?php while ($t = mysqli_fetch_assoc($tipos)): ?> 
                     <option value="<?= $t['id'] ?>" 
                         <?= $t['id'] == $reserva['tipo_quarto_id'] ? 'selected' : '' ?>> 
                         <?= h($t['nome']) ?> — €<?= $t['preco_diaria'] ?>/noite 
                     </option> 
                 <?php endwhile; ?> 
             </select> 
         </label><br> 
         <label>Data de Check-in 
             <input type="date" name="data_inicio" 
                    value="<?= h($reserva['data_inicio']) ?>" required> 
         </label><br> 
         <label>Data de Check-out 
             <input type="date" name="data_fim" 
                    value="<?= h($reserva['data_fim']) ?>" required> 
         </label><br> 
         <label>Número de Hóspedes 
             <input type="number" name="num_hospedes" min="1" 
                    value="<?= $reserva['num_hospedes'] ?>" required> 
         </label><br> 
         <label>Pequeno-almoço 
             <input type="checkbox" name="pequeno_almoco" 
                    <?= $reserva['pequeno_almoco'] ? 'checked' : '' ?>> 
         </label><br> 
         <button type="submit">Guardar Alterações</button> 
     </form> 
 </body> 
 </html>