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

$errores = $_SESSION['errores'] ?? [];
$c = array_merge($contacto, $_SESSION['old'] ?? []);
unset($_SESSION['errores'], $_SESSION['old']);

include 'includes/header.php';
?>
<h1>Editar contacto</h1>

<?php foreach ($errores as $err): ?>
    <div class="alerta alerta-error"><?= e($err) ?></div>
<?php endforeach; ?>

<form class="formulario" method="post" action="actualizar.php" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= (int)$contacto['id'] ?>">

    <label for="nombre">Nombre *</label>
    <input type="text" id="nombre" name="nombre" maxlength="80" required value="<?= e($c['nombre']) ?>">

    <label for="apellido">Apellido *</label>
    <input type="text" id="apellido" name="apellido" maxlength="80" required value="<?= e($c['apellido']) ?>">

    <label for="telefono">Teléfono *</label>
    <input type="tel" id="telefono" name="telefono" maxlength="20" required value="<?= e($c['telefono']) ?>">

    <label>Foto actual</label>
    <img class="foto-mini" src="archivos/<?= e($contacto['foto']) ?>" alt="Foto actual">

    <label for="foto">Cambiar foto (opcional)</label>
    <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp">

    <label for="email">Correo electrónico</label>
    <input type="email" id="email" name="email" maxlength="120" value="<?= e($c['email']) ?>">

    <label for="direccion">Dirección</label>
    <input type="text" id="direccion" name="direccion" maxlength="255" value="<?= e($c['direccion']) ?>">

    <label for="notas">Notas</label>
    <textarea id="notas" name="notas" rows="4"><?= e($c['notas']) ?></textarea>

    <p>
        <button class="btn btn-verde" type="submit">Actualizar</button>
        <a class="btn btn-gris" href="index.php">Cancelar</a>
    </p>
</form>
<?php include 'includes/footer.php'; ?>