<?php 
 require_once '../includes/db.php'; 
 require_once '../includes/session.php'; 
 require_once '../includes/helpers.php'; 
 
 if (esta_logado()) { 
     if (e_staff()) { 
         redirecionar('../admin/index.php'); 
     } else { 
         redirecionar('../cliente/reservas.php'); 
     } 
 } 
 
 $erro = ''; 
 
 if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
     $email = limpar($_POST['email'] ?? ''); 
     $pass  = $_POST['password'] ?? ''; 
 
     if (empty($email) || empty($pass)) { 
         $erro = 'Preenche todos os campos.'; 
     } else { 
         $stmt = mysqli_prepare($conn, "SELECT id, nome, password_hash, role, ativo FROM utilizadores WHERE email = ? LIMIT 1"); 
         mysqli_stmt_bind_param($stmt, 's', $email); 
         mysqli_stmt_execute($stmt); 
         $resultado = mysqli_stmt_get_result($stmt); 
         $user = mysqli_fetch_assoc($resultado); 
         mysqli_stmt_close($stmt); 
 
         if (!$user || !password_verify($pass, $user['password_hash'])) { 
             $erro = 'Email ou password incorretos.'; 
         } elseif (!$user['ativo']) { 
             $erro = 'Conta desativada. Contacta a receção.'; 
         } else { 
             $_SESSION['user_id']   = $user['id']; 
             $_SESSION['user_nome'] = $user['nome']; 
             $_SESSION['user_role'] = $user['role']; 
 
             if (e_staff()) { 
                 redirecionar('../admin/index.php'); 
             } else { 
                 redirecionar('../cliente/reservas.php'); 
             } 
         } 
     } 
 } 
 ?> 
 <!DOCTYPE html> 
 <html lang="pt"> 
 <head> 
     <meta charset="UTF-8"> 
     <title>Login</title> 
 </head> 
 <body> 
     <h1>Entrar</h1> 
     <?php if ($erro): ?> 
         <p style="color:red"><?= h($erro) ?></p> 
     <?php endif; ?> 
     <form method="POST"> 
         <label>Email <input type="email" name="email" required></label><br> 
         <label>Password <input type="password" name="password" required></label><br> 
         <button type="submit">Entrar</button> 
     </form> 
     <p>Não tens conta? <a href="register.php">Regista-te</a></p> 
 </body> 
 </html>