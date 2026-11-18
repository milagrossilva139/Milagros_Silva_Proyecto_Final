<?php
session_start();
if (!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit;
}
function escapar(string $v): string { 
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); 
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Personal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="container-dashboard">
        <header class="dashboard-header">
            <h1>Panel Personal</h1>
            <div class="user-actions">
                <a href="cerrar_sesion.php" class="btn-danger">Cerrar Sesión</a>
            </div>
        </header>

        <div class="card" style="padding: 20px;">
            <h2>Bienvenido/a, <?= escapar($_SESSION['nombre'] . " " . $_SESSION['apellido']) ?> 👋</h2>
            <p><strong>DNI:</strong> <?= escapar($_SESSION['dni']) ?></p>
            <p><strong>Correo electrónico:</strong> <?= escapar($_SESSION['email']) ?></p>
            <p><strong>Rol en el sistema:</strong> <span class="badge <?= escapar($_SESSION['rol']) ?>"><?= escapar($_SESSION['rol']) ?></span></p>

            <?php if ($_SESSION['rol'] === 'admin'): ?>
                <div class="admin-access" style="margin-top: 20px;">
                    <hr style="margin-bottom: 15px; border: 0; border-top: 1px solid #ddd;">
                    <h3>Panel de Administración</h3>
                    <p style="margin-bottom: 15px;">Tenés permisos de administrador para gestionar los usuarios del sistema.</p>
                    <a href="admin_usuarios.php" class="btn">Ir a Administración de Usuarios</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>