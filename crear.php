<?php
include 'includes/header.php';
$errores = $_SESSION['errores'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errores'], $_SESSION['old']);
?>
<h1>Nuevo contacto</h1>

<?php foreach ($errores as $err): ?>
    <div class="alerta alerta-error"><?= e($err) ?></div>
<?php endforeach; ?>

<form class="formulario" method="post" action="guardar.php" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <label for="nombre">Nombre *</label>
    <input type="text" id="nombre" name="nombre" maxlength="80" required value="<?= e($old['nombre'] ?? '') ?>">

    <label for="apellido">Apellido *</label>
    <input type="text" id="apellido" name="apellido" maxlength="80" required value="<?= e($old['apellido'] ?? '') ?>">

    <label for="telefono">Teléfono *</label>
    <input type="tel" id="telefono" name="telefono" maxlength="20" required value="<?= e($old['telefono'] ?? '') ?>">

    <label for="foto">Foto * (JPG, PNG o WEBP, máx. 2 MB)</label>
    <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp" required>

    <label for="email">Correo electrónico</label>
    <input type="email" id="email" name="email" maxlength="120" value="<?= e($old['email'] ?? '') ?>">

    <label for="direccion">Dirección</label>
    <input type="text" id="direccion" name="direccion" maxlength="255" value="<?= e($old['direccion'] ?? '') ?>">

    <label for="notas">Notas</label>
    <textarea id="notas" name="notas" rows="4"><?= e($old['notas'] ?? '') ?></textarea>

    <p>
        <button class="btn btn-verde" type="submit">Guardar</button>
        <a class="btn btn-gris" href="index.php">Cancelar</a>
    </p>
</form>
<?php include 'includes/footer.php'; ?>