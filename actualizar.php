<?php
require_once 'config/db.php';
require_once 'includes/funciones.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
if (!csrf_valido($_POST['csrf'] ?? '')) {
    http_response_code(403);
    die('Token de seguridad inválido.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php');
    exit;
}

$datos = limpiar_datos($_POST);
$errores = validar_contacto($datos);

try {
    $stmt = $pdo->prepare("SELECT foto FROM contactos WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $actual = $stmt->fetch();
    if (!$actual) {
        flash('error', 'El contacto no existe.');
        header('Location: index.php');
        exit;
    }

    $fotoNueva = null;
    $subioFoto = isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE;
    if (!$errores && $subioFoto) {
        $errorFoto = '';
        $fotoNueva = guardar_foto($_FILES['foto'], $errorFoto);
        if (!$fotoNueva) $errores[] = $errorFoto;
    }

    if ($errores) {
        $_SESSION['errores'] = $errores;
        $_SESSION['old'] = $datos;
        header('Location: editar.php?id=' . $id);
        exit;
    }

    $stmt = $pdo->prepare(
        "UPDATE contactos
         SET nombre = :nombre, apellido = :apellido, telefono = :telefono,
             foto = :foto, email = :email, direccion = :direccion, notas = :notas
         WHERE id = :id"
    );
    $stmt->execute([
        ':nombre'    => $datos['nombre'],
        ':apellido'  => $datos['apellido'],
        ':telefono'  => $datos['telefono'],
        ':foto'      => $fotoNueva ?: $actual['foto'],
        ':email'     => $datos['email'] ?: null,
        ':direccion' => $datos['direccion'] ?: null,
        ':notas'     => $datos['notas'] ?: null,
        ':id'        => $id,
    ]);

    if ($fotoNueva) {
        $ruta = __DIR__ . '/archivos/' . basename($actual['foto']);
        if (is_file($ruta)) unlink($ruta);
    }

    flash('ok', 'Contacto actualizado correctamente.');
    header('Location: index.php');
} catch (PDOException $e) {
    error_log($e->getMessage());
    flash('error', 'No se pudo actualizar el contacto.');
    header('Location: editar.php?id=' . $id);
}
exit;