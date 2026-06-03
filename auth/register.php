<?php 
 require_once '../includes/db.php'; 
 require_once '../includes/session.php'; 
 require_once '../includes/helpers.php'; 
 
 if (esta_logado()) { 
     redirecionar('../cliente/reservas.php'); 
 } 
 
 $erro = ''; 
 
 if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
     $nome  = limpar($_POST['nome'] ?? ''); 
     $email = limpar($_POST['email'] ?? ''); 
     $pass  = $_POST['password'] ?? ''; 
     $pass2 = $_POST['password2'] ?? ''; 
 
     if (empty($nome) || empty($email) || empty($pass) || empty($pass2)) { 
         $erro = 'Preenche todos os campos.'; 
     } elseif ($pass !== $pass2) { 
         $erro = 'As passwords não coincidem.'; 
     } elseif (strlen($pass) < 6) { 
         $erro = 'A password deve ter pelo menos 6 caracteres.'; 
     } else { 
         $stmt = mysqli_prepare($conn, "SELECT id FROM utilizadores WHERE email = ? LIMIT 1"); 
         mysqli_stmt_bind_param($stmt, 's', $email); 
         mysqli_stmt_execute($stmt); 
         mysqli_stmt_store_result($stmt); 
 
         if (mysqli_stmt_num_rows($stmt) > 0) { 
             $erro = 'Este email já está registado.'; 
             mysqli_stmt_close($stmt); 
         } else { 
             mysqli_stmt_close($stmt); 
             $hash = password_hash($pass, PASSWORD_DEFAULT); 
             $stmt2 = mysqli_prepare($conn, "INSERT INTO utilizadores (nome, email, password_hash, role) VALUES (?, ?, ?, 'cliente')"); 
             mysqli_stmt_bind_param($stmt2, 'sss', $nome, $email, $hash); 
             mysqli_stmt_execute($stmt2); 
             $novo_id = mysqli_insert_id($conn); 
             mysqli_stmt_close($stmt2); 
 
             $_SESSION['user_id']   = $novo_id; 
             $_SESSION['user_nome'] = $nome; 
             $_SESSION['user_role'] = 'cliente'; 
 
             $stmt3 = mysqli_prepare($conn, 
                 "INSERT INTO hospedes (utilizador_id, nome_completo, doc_tipo, doc_numero) 
                  VALUES (?, ?, 'Outro', 'por_preencher')"); 
             mysqli_stmt_bind_param($stmt3, 'is', $novo_id, $nome); 
             mysqli_stmt_execute($stmt3); 
 
             redirecionar('../cliente/reservas.php'); 
         } 
     } 
 } 
 ?> 
 <!DOCTYPE html> 
 <html lang="pt"> 
 <head> 
     <meta charset="UTF-8"> 
     <title>Registo</title> 
     <link rel="stylesheet" href="../includes/style.css"> 
 </head> 
 <body> 
     <header> 
         <a href="../index.php">Hotel</a> 
         <a href="../sobre.php">Sobre Nós</a> 
     </header> 
     <main> 
         <h1>Criar Conta</h1> 
         <?php if ($erro): ?> 
             <p class="erro"><?= h($erro) ?></p> 
         <?php endif; ?> 
         <form method="POST"> 
             <label>Nome <input type="text" name="nome" required></label> 
             <label>Email <input type="email" name="email" required></label> 
             <label>Password <input type="password" name="password" required></label> 
             <label>Confirmar Password <input type="password" name="password2" required></label> 
             <button type="submit">Registar</button> 
         </form> 
         <p>Já tens conta? <a href="login.php">Entra aqui</a></p> 
     </main> 
     <footer> 
         <p>&copy; 2026 Hotel. Todos os direitos reservados.</p> 
     </footer> 
 </body> 
 </html>