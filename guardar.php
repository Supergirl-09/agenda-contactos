<?php
require_once 'config/db.php';
require_once 'includes/funciones.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: crear.php');
    exit;
}
if (!csrf_valido($_POST['csrf'] ?? '')) {
    http_response_code(403);
    die('Token de seguridad inválido.');
}

$datos = limpiar_datos($_POST);
$errores = validar_contacto($datos);
$foto = null;

if (!$errores) {
    $errorFoto = '';
    $foto = guardar_foto($_FILES['foto'] ?? null, $errorFoto);
    if (!$foto) $errores[] = $errorFoto;
}

if ($errores) {
    $_SESSION['errores'] = $errores;
    $_SESSION['old'] = $datos;
    header('Location: crear.php');
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO contactos (nombre, apellido, telefono, foto, email, direccion, notas)
         VALUES (:nombre, :apellido, :telefono, :foto, :email, :direccion, :notas)"
    );
    $stmt->execute([
        ':nombre'    => $datos['nombre'],
        ':apellido'  => $datos['apellido'],
        ':telefono'  => $datos['telefono'],
        ':foto'      => $foto,
        ':email'     => $datos['email'] ?: null,
        ':direccion' => $datos['direccion'] ?: null,
        ':notas'     => $datos['notas'] ?: null,
    ]);
    flash('ok', 'Contacto creado correctamente.');
    header('Location: index.php');
} catch (PDOException $e) {
    error_log($e->getMessage());
    flash('error', 'No se pudo guardar el contacto. Intenta de nuevo.');
    header('Location: crear.php');
}
exit;