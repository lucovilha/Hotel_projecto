<?php 
 require_once '../includes/db.php'; 
 require_once '../includes/session.php'; 
 require_once '../includes/helpers.php'; 
 
 exigir_role(['gestor'], '../auth/login.php'); 
 
 $erro = ''; 
 $sucesso = ''; 
 
 if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
     $acao = $_POST['acao'] ?? ''; 
 
     if ($acao === 'criar') { 
         $numero  = limpar($_POST['numero'] ?? ''); 
         $tipo_id = (int)($_POST['tipo_quarto_id'] ?? 0); 
         $desc    = limpar($_POST['descricao'] ?? ''); 
 
         if (empty($numero) || !$tipo_id) { 
             $erro = 'Preenche todos os campos obrigatórios.'; 
         } else { 
             $stmt = mysqli_prepare($conn, 
                 "INSERT INTO quartos (numero, tipo_quarto_id, descricao) 
                  VALUES (?, ?, ?)"); 
             mysqli_stmt_bind_param($stmt, 'sis', $numero, $tipo_id, $desc); 
             if (mysqli_stmt_execute($stmt)) { 
                 $sucesso = 'Quarto criado com sucesso.'; 
             } else { 
                 $erro = 'Número de quarto já existe.'; 
             } 
         } 
     } 
 } 
 
 $quartos = mysqli_query($conn, 
     "SELECT q.id, q.numero, tq.nome AS tipo, q.estado, q.descricao 
      FROM quartos q 
      JOIN tipos_quarto tq ON q.tipo_quarto_id = tq.id 
      ORDER BY q.numero"); 
 
 $tipos = mysqli_query($conn, "SELECT id, nome FROM tipos_quarto WHERE ativo = 1"); 
 ?> 
 <!DOCTYPE html> 
 <html lang="pt"> 
 <head> 
     <meta charset="UTF-8"> 
     <title>Quartos</title> 
     <link rel="stylesheet" href="../includes/style.css"> 
 </head> 
 <body> 
     <header> 
         <a href="index.php">Dashboard</a> 
         <a href="reservas.php">Reservas</a> 
         <a href="quartos.php">Quartos</a> 
         <a href="tipos-quarto.php">Tipos de Quarto</a> 
         <a href="relatorios.php">Relatórios</a> 
         <a href="../auth/logout.php">Sair</a> 
     </header> 
     <main> 
         <h1>Gestão de Quartos</h1> 
         <?php if ($erro): ?> 
             <p class="erro"><?= h($erro) ?></p> 
         <?php endif; ?> 
         <?php if ($sucesso): ?> 
             <p class="sucesso"><?= h($sucesso) ?></p> 
         <?php endif; ?> 
         <h2>Adicionar Quarto</h2> 
         <form method="POST"> 
             <input type="hidden" name="acao" value="criar"> 
             <label>Número <input type="text" name="numero" required></label> 
             <label>Tipo 
                 <select name="tipo_quarto_id" required> 
                     <option value="">-- Escolhe --</option> 
                     <?php while ($t = mysqli_fetch_assoc($tipos)): ?> 
                         <option value="<?= $t['id'] ?>"><?= h($t['nome']) ?></option> 
                     <?php endwhile; ?> 
                 </select> 
             </label> 
             <label>Descrição <input type="text" name="descricao"></label> 
             <button type="submit">Adicionar</button> 
         </form> 
         <h2>Lista de Quartos</h2> 
         <table> 
             <tr> 
                 <th>Número</th> 
                 <th>Tipo</th> 
                 <th>Estado</th> 
                 <th>Descrição</th> 
             </tr> 
             <?php while ($q = mysqli_fetch_assoc($quartos)): ?> 
             <tr> 
                 <td><?= h($q['numero']) ?></td> 
                 <td><?= h($q['tipo']) ?></td> 
                 <td><?= h($q['estado']) ?></td> 
                 <td><?= h($q['descricao'] ?? '-') ?></td> 
             </tr> 
             <?php endwhile; ?> 
         </table> 
     </main> 
     <footer> 
         <p>&copy; 2026 Hotel. Todos os direitos reservados.</p> 
     </footer> 
 </body> 
 </html>