<?php require_once 'session.php'; require_once 'helpers.php'; ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Bacalhau</title>
    <link rel="stylesheet" href="/hotel/assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Hotel Bacalhau</h1>
            <nav>
                <ul>
                    <li><a href="/hotel/index.php">Home</a></li>
                    <?php if (esta_logado()): ?>
                        <?php if (e_staff()): ?>
                            <li><a href="/hotel/admin/index.php">Painel Admin</a></li>
                        <?php else: ?>
                            <li><a href="/hotel/cliente/reservas.php">Minhas Reservas</a></li>
                        <?php endif; ?>
                        <li class="user-info">Olá, <?= h(user_nome()) ?></li>
                        <li><a href="/hotel/auth/logout.php">Sair</a></li>
                    <?php else: ?>
                        <li><a href="/hotel/auth/login.php">Entrar</a></li>
                        <li><a href="/hotel/auth/register.php">Registar</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">
