<?php 
 require_once '../includes/db.php'; 
 require_once '../includes/session.php'; 
 require_once '../includes/helpers.php'; 
 
 exigir_role(['gestor', 'rececionista'], '../auth/login.php'); 
 
 $sucesso = ''; 
 
 if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
     $acao = $_POST['acao'] ?? ''; 
 
     if ($acao === 'inativar') { 
         $hid = (int)($_POST['hospede_id'] ?? 0); 
         $stmt = mysqli_prepare($conn, 
             "UPDATE hospedes SET estado = 'inativo' WHERE id = ?"); 
         mysqli_stmt_bind_param($stmt, 'i', $hid); 
         mysqli_stmt_execute($stmt); 
         $sucesso = 'Hóspede inativado.'; 
     } 
 } 
 
 $hospedes = mysqli_query($conn, 
     "SELECT h.id, h.nome_completo, h.doc_tipo, h.doc_numero, 
             h.nif, h.telefone, h.estado, u.email 
      FROM hospedes h 
      JOIN utilizadores u ON h.utilizador_id = u.id 
      ORDER BY h.nome_completo"); 
 ?> 
 <!DOCTYPE html> 
 <html lang="pt"> 
 <head> 
     <meta charset="UTF-8"> 
     <title>Hóspedes</title> 
     <link rel="stylesheet" href="../includes/style.css"> 
 </head> 
 <body> 
     <header> 
         <a href="index.php">Dashboard</a> 
         <a href="reservas.php">Reservas</a> 
         <a href="hospedes.php">Hóspedes</a> 
         <?php if (e_gestor()): ?> 
             <a href="quartos.php">Quartos</a> 
             <a href="relatorios.php">Relatórios</a> 
         <?php endif; ?> 
         <a href="logs.php">Logs</a> 
         <a href="../auth/logout.php">Sair</a> 
     </header> 
     <main> 
         <h1>Gestão de Hóspedes</h1> 
         <?php if ($sucesso): ?> 
             <p class="sucesso"><?= h($sucesso) ?></p> 
         <?php endif; ?> 
         <table> 
             <tr> 
                 <th>Nome</th> 
                 <th>Email</th> 
                 <th>Documento</th> 
                 <th>Nº Doc</th> 
                 <th>NIF</th> 
                 <th>Telefone</th> 
                 <th>Estado</th> 
                 <th>Ações</th> 
             </tr> 
             <?php while ($h = mysqli_fetch_assoc($hospedes)): ?> 
             <tr> 
                 <td><?= h($h['nome_completo']) ?></td> 
                 <td><?= h($h['email']) ?></td> 
                 <td><?= h($h['doc_tipo']) ?></td> 
                 <td><?= h($h['doc_numero']) ?></td> 
                 <td><?= h($h['nif'] ?? '-') ?></td> 
                 <td><?= h($h['telefone'] ?? '-') ?></td> 
                 <td><?= h($h['estado']) ?></td> 
                 <td> 
                     <?php if ($h['estado'] === 'ativo'): ?> 
                         <form method="POST" style="display:inline"> 
                             <input type="hidden" name="acao" value="inativar"> 
                             <input type="hidden" name="hospede_id" value="<?= $h['id'] ?>"> 
                             <button type="submit">Inativar</button> 
                         </form> 
                     <?php endif; ?> 
                 </td> 
             </tr> 
             <?php endwhile; ?> 
         </table> 
     </main> 
     <footer> 
         <p>&copy; 2026 Hotel. Todos os direitos reservados.</p> 
     </footer> 
 </body> 
 </html>