<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

/**
 * Envía una respuesta JSON con código de estado HTTP y termina la ejecución.
 */
function jsonResponse(int $statusCode, array $data): void {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

/**
 * Devuelve el parámetro $key desde POST (form) o desde un JSON en el body.
 */
function getParam(string $key): string {
    // 1) Form / x-www-form-urlencoded / multipart
    if (isset($_POST[$key])) return trim((string)$_POST[$key]);

    // 2) JSON
    $raw = file_get_contents('php://input');
    if ($raw) {
        $data = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($data[$key])) {
            return trim((string)$data[$key]);
        }
    }
    return '';
}

// Solo permitir método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Acceso con método no permitido: " . $_SERVER['REQUEST_METHOD']);
    jsonResponse(405, ['status' => 'error', 'msg' => 'Método no permitido']);
}

$usuario  = getParam('usuario');
$password = getParam('password');

// Validar campos obligatorios
if ($usuario === '' || $password === '') {
    error_log("Datos incompletos: usuario='$usuario'");
    jsonResponse(400, ['status' => 'error', 'msg' => 'Datos incompletos']);
}

// Validaciones simples
if (strlen($usuario) < 5 || strlen($usuario) > 20) {
    error_log("Usuario inválido: '$usuario'");
    jsonResponse(422, ['status' => 'error', 'msg' => 'El usuario debe tener entre 5 y 20 caracteres']);
}

if (strlen($password) < 6) {
    error_log("Contraseña demasiado corta para usuario '$usuario'");
    jsonResponse(422, ['status' => 'error', 'msg' => 'La contraseña debe tener al menos 6 caracteres']);
}

// Si pasa todas las validaciones
jsonResponse(200, ['status' => 'ok']);
