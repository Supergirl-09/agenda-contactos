<?php
require_once 'config/db.php';
require_once 'includes/funciones.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
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
include 'includes/header.php';
?>
<div class="tarjeta">
    <img class="foto-grande" src="archivos/<?= e($contacto['foto']) ?>" alt="Foto de <?= e($contacto['nombre']) ?>">
    <h1><?= e($contacto['nombre'] . ' ' . $contacto['apellido']) ?></h1>
    <p><strong>Teléfono:</strong> <?= e($contacto['telefono']) ?></p>
    <p><strong>Correo:</strong> <?= e($contacto['email'] ?: 'No registrado') ?></p>
    <p><strong>Dirección:</strong> <?= e($contacto['direccion'] ?: 'No registrada') ?></p>
    <p><strong>Notas:</strong><br><?= nl2br(e($contacto['notas'] ?: 'Sin notas')) ?></p>
    <a class="btn btn-verde" href="editar.php?id=<?= (int)$contacto['id'] ?>">Editar</a>
    <a class="btn btn-gris" href="index.php">Volver</a>
</div>
<?php include 'includes/footer.php'; ?>