<?php
$db_file = __DIR__ . '/database.sqlite';

try {
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Create tables if they don't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS jewellery (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        token_id TEXT NOT NULL UNIQUE,
        name TEXT NOT NULL,
        category TEXT NOT NULL,
        weight REAL NOT NULL,
        purity TEXT NOT NULL,
        hallmark TEXT NOT NULL,
        manufacturer_id INTEGER NOT NULL,
        current_owner_id INTEGER NOT NULL,
        ipfs_hash TEXT NOT NULL,
        is_stolen INTEGER DEFAULT 0,
        FOREIGN KEY(manufacturer_id) REFERENCES users(id),
        FOREIGN KEY(current_owner_id) REFERENCES users(id)
    )");

} catch (PDOException $e) {
    die(json_encode(["message" => "Database connection failed: " . $e->getMessage()]));
}

function generate_jwt($payload) {
    $secret = 'super_secret_key_123';
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload = json_encode($payload);

    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

function verify_jwt($jwt) {
    $secret = 'super_secret_key_123';
    $tokenParts = explode('.', $jwt);
    if(count($tokenParts) != 3) return false;
    
    $header = base64_decode($tokenParts[0]);
    $payload = base64_decode($tokenParts[1]);
    $signature_provided = $tokenParts[2];

    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    if($base64UrlSignature === $signature_provided) {
        return json_decode($payload, true);
    }
    return false;
}
?>
