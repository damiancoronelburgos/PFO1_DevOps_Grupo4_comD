<?php
header('Content-Type: application/json; charset=utf-8');

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

$usuario  = getParam('usuario');
$password = getParam('password');

if ($usuario === '' || $password === '') {
  http_response_code(400);
  echo json_encode(['status' => 'error', 'msg' => 'Datos incompletos']);
  exit;
}

/* Validaciones simples */
if (strlen($usuario) < 3 || strlen($usuario) > 20) {
  http_response_code(422);
  echo json_encode(['status' => 'error', 'msg' => 'El usuario debe tener entre 3 y 20 caracteres']);
  exit;
}
if (strlen($password) < 6) {
  http_response_code(422);
  echo json_encode(['status' => 'error', 'msg' => 'La contraseña debe tener al menos 6 caracteres']);
  exit;
}

echo json_encode(['status' => 'ok']);