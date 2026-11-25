<?php
require_once 'config/db.php';
include 'includes/header.php';

$q = trim($_GET['q'] ?? '');

try {
    if ($q !== '') {
        $like = "%$q%";
        $stmt = $pdo->prepare(
            "SELECT * FROM contactos
             WHERE nombre LIKE ? OR apellido LIKE ? OR telefono LIKE ? OR email LIKE ?
             ORDER BY nombre, apellido"
        );
        $stmt->execute([$like, $like, $like, $like]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM contactos ORDER BY nombre, apellido");
        $stmt->execute();
    }
    $contactos = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    $contactos = [];
    echo '<div class="alerta alerta-error">Ocurrió un error al consultar los contactos.</div>';
}
?>
<h1>Mis contactos</h1>

<form class="buscador" method="get" action="index.php">
    <input type="search" name="q" placeholder="Buscar por nombre, teléfono o correo..."
           value="<?= e($q) ?>">
    <button class="btn" type="submit">Buscar</button>
    <?php if ($q !== ''): ?><a class="btn btn-gris" href="index.php">Limpiar</a><?php endif; ?>
</form>

<?php if (!$contactos): ?>
    <p>No se encontraron contactos.</p>
<?php else: ?>
<table>
    <thead>
        <tr><th>Foto</th><th>Nombre</th><th>Teléfono</th><th>Acciones</th></tr>
    </thead>
    <tbody>
    <?php foreach ($contactos as $c): ?>
        <tr>
            <td><img class="foto-mini" src="archivos/<?= e($c['foto']) ?>" alt="Foto de <?= e($c['nombre']) ?>"></td>
            <td><?= e($c['nombre'] . ' ' . $c['apellido']) ?></td>
            <td><?= e($c['telefono']) ?></td>
            <td>
                <a class="btn" href="ver.php?id=<?= (int)$c['id'] ?>">Ver</a>
                <a class="btn btn-verde" href="editar.php?id=<?= (int)$c['id'] ?>">Editar</a>
                <a class="btn btn-rojo" href="eliminar.php?id=<?= (int)$c['id'] ?>">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>