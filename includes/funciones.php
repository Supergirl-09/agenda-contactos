<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_valido($token) {
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string)$token);
}

function flash($tipo = null, $mensaje = null) {
    if ($tipo !== null) {
        $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
        return null;
    }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function limpiar_datos($post) {
    $campos = ['nombre', 'apellido', 'telefono', 'email', 'direccion', 'notas'];
    $datos = [];
    foreach ($campos as $c) {
        $datos[$c] = trim($post[$c] ?? '');
    }
    return $datos;
}

function validar_contacto($d) {
    $errores = [];
    if ($d['nombre'] === '')   $errores[] = 'El nombre es obligatorio.';
    if ($d['apellido'] === '') $errores[] = 'El apellido es obligatorio.';
    if ($d['telefono'] === '') {
        $errores[] = 'El teléfono es obligatorio.';
    } elseif (!preg_match('/^[0-9+\-\s()]{7,20}$/', $d['telefono'])) {
        $errores[] = 'El teléfono tiene un formato inválido.';
    }
    if ($d['email'] !== '' && !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo electrónico no es válido.';
    }
    return $errores;
}

function guardar_foto($archivo, &$error) {
    if (!$archivo || $archivo['error'] !== UPLOAD_ERR_OK) {
        $error = 'La foto es obligatoria.';
        return null;
    }
    if ($archivo['size'] > 2 * 1024 * 1024) {
        $error = 'La foto no debe pesar más de 2 MB.';
        return null;
    }
    $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($archivo['tmp_name']);
    if (!isset($permitidos[$mime])) {
        $error = 'Formato de foto no permitido (usa JPG, PNG o WEBP).';
        return null;
    }
    $nombre = bin2hex(random_bytes(8)) . '.' . $permitidos[$mime];
    if (!move_uploaded_file($archivo['tmp_name'], __DIR__ . '/../archivos/' . $nombre)) {
        $error = 'No se pudo guardar la foto.';
        return null;
    }
    return $nombre;
}