<?php require_once __DIR__ . '/funciones.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agenda de Contactos</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php include __DIR__ . '/menu.php'; ?>
<main class="contenedor">
<?php if ($f = flash()): ?>
    <div class="alerta alerta-<?= e($f['tipo']) ?>"><?= e($f['mensaje']) ?></div>
<?php endif; ?>