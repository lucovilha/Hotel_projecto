<?php 
 require_once '../includes/db.php'; 
 require_once '../includes/session.php'; 
 require_once '../includes/helpers.php'; 
 
 exigir_login('../auth/login.php'); 
 
 $erro = ''; 
 $tipos = mysqli_query($conn, "SELECT * FROM tipos_quarto WHERE ativo = 1"); 
 
 if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
     $tipo_id      = (int)($_POST['tipo_quarto_id'] ?? 0); 
     $data_inicio  = limpar($_POST['data_inicio'] ?? ''); 
     $data_fim     = limpar($_POST['data_fim'] ?? ''); 
     $num_hospedes = (int)($_POST['num_hospedes'] ?? 1); 
     $pa           = isset($_POST['pequeno_almoco']) ? 1 : 0; 
     $nif          = limpar($_POST['nif'] ?? ''); 
 
     if (!$tipo_id || !$data_inicio || !$data_fim) { 
         $erro = 'Preenche todos os campos obrigatórios.'; 
     } elseif ($data_inicio >= $data_fim) { 
         $erro = 'A data de fim deve ser posterior à data de início.'; 
     } elseif ($data_inicio < date('Y-m-d')) { 
         $erro = 'Não podes criar reservas com datas no passado.'; 
     } else { 
         $stmt = mysqli_prepare($conn, "SELECT * FROM tipos_quarto WHERE id = ? AND ativo = 1"); 
         mysqli_stmt_bind_param($stmt, 'i', $tipo_id); 
         mysqli_stmt_execute($stmt); 
         $tipo = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); 
 
         if (!$tipo) { 
             $erro = 'Tipo de quarto inválido.'; 
         } elseif ($num_hospedes > $tipo['capacidade_maxima']) { 
             $erro = 'Número de hóspedes excede a capacidade máxima deste tipo de quarto.'; 
         } else { 
             $stmt2 = mysqli_prepare($conn, 
                 "SELECT COUNT(*) AS ocupados FROM reservas 
                  WHERE tipo_quarto_id = ? 
                  AND estado IN ('pendente','ativa') 
                  AND data_inicio < ? AND data_fim > ?"); 
             mysqli_stmt_bind_param($stmt2, 'iss', $tipo_id, $data_fim, $data_inicio); 
             mysqli_stmt_execute($stmt2); 
             $ocupados = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2))['ocupados']; 
 
             $total_tipo = mysqli_fetch_assoc(mysqli_query($conn, 
                 "SELECT COUNT(*) AS total FROM quartos WHERE tipo_quarto_id = $tipo_id" 
             ))['total']; 
 
             if ($ocupados >= $total_tipo) { 
                 $erro = 'Não existem quartos disponíveis deste tipo para as datas escolhidas.'; 
             } else { 
                 $uid = user_id(); 
                 $stmt3 = mysqli_prepare($conn, "SELECT id FROM hospedes WHERE utilizador_id = ? LIMIT 1"); 
                 mysqli_stmt_bind_param($stmt3, 'i', $uid); 
                 mysqli_stmt_execute($stmt3); 
                 $hospede = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt3)); 
 
                 if (!$hospede) { 
                     $erro = 'Precisas de completar o teu perfil antes de reservar.'; 
                 } else { 
                     $noites = calcular_noites($data_inicio, $data_fim); 
                     $total  = calcular_total($noites, $num_hospedes, 
                                 $tipo['capacidade_base'], $tipo['preco_diaria'], 
                                 $tipo['preco_hospede_extra'], $tipo['preco_pequeno_almoco'], $pa); 
 
                     $stmt4 = mysqli_prepare($conn, 
                         "INSERT INTO reservas (hospede_id, tipo_quarto_id, data_inicio, data_fim, 
                          num_hospedes, pequeno_almoco, nif_faturacao, total_estimado) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)"); 
                     mysqli_stmt_bind_param($stmt4, 'iissiisd', 
                         $hospede['id'], $tipo_id, $data_inicio, $data_fim, 
                         $num_hospedes, $pa, $nif, $total); 
                     mysqli_stmt_execute($stmt4); 
 
                     redirecionar('reservas.php'); 
                 } 
             } 
         } 
     } 
 } 
 ?> 
 <!DOCTYPE html> 
 <html lang="pt"> 
 <head> 
     <meta charset="UTF-8"> 
     <title>Nova Reserva</title> 
     <link rel="stylesheet" href="../includes/style.css"> 
 </head> 
 <body> 
     <header> 
         <a href="../index.php">Hotel</a> 
         <a href="reservas.php">As Minhas Reservas</a> 
         <a href="../auth/logout.php">Sair</a> 
     </header> 
     <main> 
         <h1>Nova Reserva</h1> 
         <?php if ($erro): ?> 
             <p class="erro"><?= h($erro) ?></p> 
         <?php endif; ?> 
         <form method="POST"> 
             <label>Tipo de Quarto 
                 <select name="tipo_quarto_id" required> 
                     <option value="">-- Escolhe --</option> 
                     <?php while ($t = mysqli_fetch_assoc($tipos)): ?> 
                         <option value="<?= $t['id'] ?>"> 
                             <?= h($t['nome']) ?> — €<?= $t['preco_diaria'] ?>/noite 
                             (max <?= $t['capacidade_maxima'] ?> hóspedes) 
                         </option> 
                     <?php endwhile; ?> 
                 </select> 
             </label> 
             <label>Data de Check-in <input type="date" name="data_inicio" required></label> 
             <label>Data de Check-out <input type="date" name="data_fim" required></label> 
             <label>Número de Hóspedes <input type="number" name="num_hospedes" min="1" value="1" required></label> 
             <label>Pequeno-almoço <input type="checkbox" name="pequeno_almoco" style="width:auto"></label> 
             <label>NIF (opcional) <input type="text" name="nif" maxlength="9"></label> 
             <button type="submit">Confirmar Reserva</button> 
         </form> 
     </main> 
     <footer> 
         <p>&copy; 2026 Hotel. Todos os direitos reservados.</p> 
     </footer> 
 </body> 
 </html>