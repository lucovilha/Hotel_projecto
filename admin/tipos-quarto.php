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
         $nome      = limpar($_POST['nome'] ?? ''); 
         $cap_base  = (int)($_POST['capacidade_base'] ?? 0); 
         $cap_max   = (int)($_POST['capacidade_maxima'] ?? 0); 
         $preco     = (float)($_POST['preco_diaria'] ?? 0); 
         $extra     = (float)($_POST['preco_hospede_extra'] ?? 0); 
         $pa        = (float)($_POST['preco_pequeno_almoco'] ?? 0); 
         $desc      = limpar($_POST['descricao'] ?? ''); 
 
         if (empty($nome) || !$cap_base || !$cap_max || !$preco) { 
             $erro = 'Preenche todos os campos obrigatórios.'; 
         } elseif ($cap_max < $cap_base) { 
             $erro = 'A capacidade máxima não pode ser menor que a capacidade base.'; 
         } else { 
             $stmt = mysqli_prepare($conn, 
                 "INSERT INTO tipos_quarto (nome, capacidade_base, capacidade_maxima, 
                  preco_diaria, preco_hospede_extra, preco_pequeno_almoco, descricao) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)"); 
             mysqli_stmt_bind_param($stmt, 'siiddds', 
                 $nome, $cap_base, $cap_max, $preco, $extra, $pa, $desc); 
             mysqli_stmt_execute($stmt); 
             $sucesso = 'Tipo de quarto criado com sucesso.'; 
         } 
     } elseif ($acao === 'desativar') { 
         $tid = (int)($_POST['tipo_id'] ?? 0); 
         $stmt = mysqli_prepare($conn, 
             "UPDATE tipos_quarto SET ativo = 0 WHERE id = ?"); 
         mysqli_stmt_bind_param($stmt, 'i', $tid); 
         mysqli_stmt_execute($stmt); 
         $sucesso = 'Tipo de quarto desativado.'; 
     } 
 } 
 
 $tipos = mysqli_query($conn, 
     "SELECT * FROM tipos_quarto ORDER BY nome"); 
 ?> 
 <!DOCTYPE html> 
 <html lang="pt"> 
 <head> 
     <meta charset="UTF-8"> 
     <title>Tipos de Quarto</title> 
 </head> 
 <body> 
     <h1>Tipos de Quarto</h1> 
     <a href="index.php">Dashboard</a> | 
     <a href="../auth/logout.php">Sair</a> 
     <hr> 
     <?php if ($erro): ?> 
         <p style="color:red"><?= h($erro) ?></p> 
     <?php endif; ?> 
     <?php if ($sucesso): ?> 
         <p style="color:green"><?= h($sucesso) ?></p> 
     <?php endif; ?> 
 
     <h2>Adicionar Tipo de Quarto</h2> 
     <form method="POST"> 
         <input type="hidden" name="acao" value="criar"> 
         <label>Nome <input type="text" name="nome" required></label><br> 
         <label>Capacidade Base <input type="number" name="capacidade_base" min="1" required></label><br> 
         <label>Capacidade Máxima <input type="number" name="capacidade_maxima" min="1" required></label><br> 
         <label>Preço/noite (€) <input type="number" name="preco_diaria" step="0.01" min="0" required></label><br> 
         <label>Suplemento hóspede extra (€) <input type="number" name="preco_hospede_extra" step="0.01" min="0" value="0"></label><br> 
         <label>Pequeno-almoço por hóspede (€) <input type="number" name="preco_pequeno_almoco" step="0.01" min="0" value="0"></label><br> 
         <label>Descrição <input type="text" name="descricao"></label><br> 
         <button type="submit">Adicionar</button> 
     </form> 
 
     <hr> 
     <h2>Lista de Tipos</h2> 
     <table border="1"> 
         <tr> 
             <th>Nome</th> 
             <th>Cap. Base</th> 
             <th>Cap. Máx</th> 
             <th>Preço/noite</th> 
             <th>Extra/hóspede</th> 
             <th>Pequeno-almoço</th> 
             <th>Estado</th> 
             <th>Ações</th> 
         </tr> 
         <?php while ($t = mysqli_fetch_assoc($tipos)): ?> 
         <tr> 
             <td><?= h($t['nome']) ?></td> 
             <td><?= $t['capacidade_base'] ?></td> 
             <td><?= $t['capacidade_maxima'] ?></td> 
             <td>€<?= number_format($t['preco_diaria'], 2) ?></td> 
             <td>€<?= number_format($t['preco_hospede_extra'], 2) ?></td> 
             <td>€<?= number_format($t['preco_pequeno_almoco'], 2) ?></td> 
             <td><?= $t['ativo'] ? 'Ativo' : 'Inativo' ?></td> 
             <td> 
                 <?php if ($t['ativo']): ?> 
                     <form method="POST" style="display:inline"> 
                         <input type="hidden" name="acao" value="desativar"> 
                         <input type="hidden" name="tipo_id" value="<?= $t['id'] ?>"> 
                         <button type="submit">Desativar</button> 
                     </form> 
                 <?php endif; ?> 
             </td> 
         </tr> 
         <?php endwhile; ?> 
     </table> 
 </body> 
 </html> 
