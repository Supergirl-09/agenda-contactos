<?php
require_once 'config/db.php';
require_once 'includes/funciones.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT)
   ?: filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$contacto = false;

if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM contactos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $contacto = $stmt->fetch();
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}

if (!$contacto) {
    flash('error', 'El contacto no existe.');
    header('Location: index.php');
    exit;
}

// Paso 2: confirmación recibida por POST -> se elimina
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valido($_POST['csrf'] ?? '')) {
        http_response_code(403);
        die('Token de seguridad inválido.');
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM contactos WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $ruta = __DIR__ . '/archivos/' . basename($contacto['foto']);
        if (is_file($ruta)) unlink($ruta);

        flash('ok', 'Contacto eliminado correctamente.');
    } catch (PDOException $e) {
        error_log($e->getMessage());
        flash('error', 'No se pudo eliminar el contacto.');
    }

    header('Location: index.php');
    exit;
}

// Paso 1: pantalla de confirmación
include 'includes/header.php';
?>
<div class="tarjeta">
    <h1>¿Eliminar contacto?</h1>
    <p>Vas a eliminar a <strong><?= e($contacto['nombre'] . ' ' . $contacto['apellido']) ?></strong>.
       Esta acción no se puede deshacer.</p>
    <form method="post" action="eliminar.php">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= (int)$contacto['id'] ?>">
        <button class="btn btn-rojo" type="submit">Sí, eliminar</button>
        <a class="btn btn-gris" href="index.php">Cancelar</a>
    </form>
</div>
<?php include 'includes/footer.php'; ?>