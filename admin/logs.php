<?php 
 require_once '../includes/db.php'; 
 require_once '../includes/session.php'; 
 require_once '../includes/helpers.php'; 
 
 exigir_role(['gestor', 'rececionista'], '../auth/login.php'); 
 
 $logs = mysqli_query($conn, 
     "SELECT l.id, l.acao, l.descricao, l.criado_em, 
             u.nome AS operador 
      FROM logs l 
      LEFT JOIN utilizadores u ON l.utilizador_id = u.id 
      ORDER BY l.criado_em DESC"); 
 ?> 
 <!DOCTYPE html> 
 <html lang="pt"> 
 <head> 
     <meta charset="UTF-8"> 
     <title>Logs de Auditoria</title> 
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
         <h1>Logs de Auditoria</h1> 
         <table> 
             <tr> 
                 <th>ID</th> 
                 <th>Ação</th> 
                 <th>Descrição</th> 
                 <th>Operador</th> 
                 <th>Data</th> 
             </tr> 
             <?php while ($l = mysqli_fetch_assoc($logs)): ?> 
             <tr> 
                 <td><?= $l['id'] ?></td> 
                 <td><?= h($l['acao']) ?></td> 
                 <td><?= h($l['descricao']) ?></td> 
                 <td><?= h($l['operador'] ?? 'Sistema') ?></td> 
                 <td><?= h($l['criado_em']) ?></td> 
             </tr> 
             <?php endwhile; ?> 
         </table> 
     </main> 
     <footer> 
         <p>&copy; 2026 Hotel. Todos os direitos reservados.</p> 
     </footer> 
 </body> 
 </html>