<?php
require_once __DIR__ . '/config.php';

/**
 * Kirim request ke Flask API menggunakan cURL.
 *
 * @param  string  $endpoint  path setelah API_BASE_URL, contoh: '/pengguna'
 * @param  string  $method    GET | POST | PUT | DELETE
 * @param  array   $body      data yang dikirim sebagai JSON (POST/PUT)
 * @return array   ['status'=>string, 'message'=>string, 'data'=>mixed, 'http_code'=>int]
 */
function api_call(string $endpoint, string $method = 'GET', array $body = []): array
{
    $url = API_BASE_URL . $endpoint;
    $ch  = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
    ]);

    if (in_array($method, ['POST', 'PUT']) && !empty($body)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    $raw  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = $raw ? (json_decode($raw, true) ?? []) : [];
    $result['http_code'] = $code;
    return $result;
}

/** Shorthand helpers */
function api_get(string $ep): array                        { return api_call($ep, 'GET'); }
function api_post(string $ep, array $d): array             { return api_call($ep, 'POST', $d); }
function api_put(string $ep, array $d): array              { return api_call($ep, 'PUT', $d); }
function api_delete(string $ep): array                     { return api_call($ep, 'DELETE'); }

/** Flash message via session */
function set_flash(string $type, string $msg): void
{
    session_start();
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function get_flash(): ?array
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}
