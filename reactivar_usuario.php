<?php
session_start();
require "conexion.php";

if (!isset($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado.');
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $conexion->prepare("UPDATE usuarios SET archivado_en = NULL WHERE id = ? AND archivado_en IS NOT NULL");
        $stmt->execute([$id]);
    }
}
header("Location: usuarios_archivados.php");
exit;