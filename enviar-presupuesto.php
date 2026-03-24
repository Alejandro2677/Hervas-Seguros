<?php

$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    hs_redirect('error-envio.html');
}

if (!hs_validate_email($config['mail_to'] ?? '') || !hs_validate_email($config['mail_from'] ?? '')) {
    hs_redirect('error-envio.html');
}

$nombre = hs_sanitize_text($_POST['nombre'] ?? '');
$apellidos = hs_sanitize_text($_POST['apellidos'] ?? '');
$email = hs_sanitize_email($_POST['email'] ?? '');
$telefono = hs_sanitize_text($_POST['telefono'] ?? '');
$tipoVehiculo = hs_sanitize_text($_POST['tipo_vehiculo'] ?? '');
$marcaVehiculo = hs_sanitize_text($_POST['marca_vehiculo'] ?? '');
$anioMatriculacion = hs_sanitize_text($_POST['anio_matriculacion'] ?? '');
$modalidadSeguro = hs_sanitize_text($_POST['modalidad_seguro'] ?? '');
$comentarios = hs_sanitize_textarea($_POST['comentarios'] ?? '');
$privacidad = hs_post_checked($_POST['privacidad'] ?? '');

$errores = [];

if ($nombre === '') {
    $errores[] = 'nombre';
}

if ($apellidos === '') {
    $errores[] = 'apellidos';
}

if (!hs_validate_email($email)) {
    $errores[] = 'email';
}

if (!hs_validate_phone($telefono)) {
    $errores[] = 'telefono';
}

if ($tipoVehiculo === '') {
    $errores[] = 'tipo_vehiculo';
}

if ($modalidadSeguro === '') {
    $errores[] = 'modalidad_seguro';
}

if (!hs_validate_year($anioMatriculacion)) {
    $errores[] = 'anio_matriculacion';
}

if (!$privacidad) {
    $errores[] = 'privacidad';
}

if (!empty($errores)) {
    hs_redirect('error-envio.html');
}

$subject = 'Nuevo presupuesto desde la web - Hervás Seguros';

$messageLines = [
    'Nuevo presupuesto recibido desde la web de ' . $config['site_name'],
    '',
    'DATOS DE CONTACTO',
    hs_mail_value('Nombre', $nombre),
    hs_mail_value('Apellidos', $apellidos),
    hs_mail_value('Email', $email),
    hs_mail_value('Teléfono', $telefono),
    '',
    'DATOS DEL SEGURO',
    hs_mail_value('Tipo de vehículo', $tipoVehiculo),
    hs_mail_value('Marca del vehículo', $marcaVehiculo),
    hs_mail_value('Año de matriculación', $anioMatriculacion),
    hs_mail_value('Modalidad de seguro', $modalidadSeguro),
    '',
    'COMENTARIOS',
    $comentarios !== '' ? $comentarios : 'No indicado',
    '',
    'PRIVACIDAD',
    'Aceptada: ' . ($privacidad ? 'Sí' : 'No'),
];

$message = implode("\r\n", $messageLines);

$headers = [
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'From: ' . $config['mail_from'],
    'Reply-To: ' . $email,
    'X-Mailer: PHP/' . phpversion(),
];

$sent = mail(
    $config['mail_to'],
    hs_mail_subject($subject),
    $message,
    implode("\r\n", $headers)
);

if ($sent) {
    hs_redirect('gracias.html');
}

hs_redirect('error-envio.html');
