<?php
// Utility to generate and load RSA keypair for JWT lab
// Keys are generated on first use and stored under a writable directory.

function jwt_keys_dir(): string {
    static $selected = null;
    if ($selected !== null) return $selected;

    $candidates = [
        __DIR__ . DIRECTORY_SEPARATOR . 'keys',
        rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'webvulnlab_jwt_keys',
    ];

    foreach ($candidates as $dir) {
        if (is_dir($dir)) {
            if (is_writable($dir)) { $selected = $dir; return $selected; }
        } else {
            // Try to create quietly
            if (@mkdir($dir, 0700, true)) { $selected = $dir; return $selected; }
        }
    }
    // Fallback to temp dir without creating (may limit to in-memory keys)
    $selected = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'webvulnlab_jwt_keys';
    @mkdir($selected, 0700, true);
    return $selected;
}

function jwt_private_key_path(): string {
    return jwt_keys_dir() . DIRECTORY_SEPARATOR . 'private.pem';
}

function jwt_public_key_path(): string {
    return jwt_keys_dir() . DIRECTORY_SEPARATOR . 'public.pem';
}

function jwt_ensure_keys(): bool {
    $dir = jwt_keys_dir();
    if (!is_dir($dir) && !@mkdir($dir, 0700, true)) {
        return false;
    }
    $priv = jwt_private_key_path();
    $pub = jwt_public_key_path();
    if (!file_exists($priv) || !file_exists($pub)) {
        $config = [
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];
        $res = openssl_pkey_new($config);
        if ($res === false) {
            return false;
        }
        $privKey = '';
        if (!openssl_pkey_export($res, $privKey)) {
            return false;
        }
        $details = openssl_pkey_get_details($res);
        if ($details === false || !isset($details['key'])) {
            return false;
        }
        $pubKey = $details['key'];
        if (@file_put_contents($priv, $privKey) === false) { return false; }
        @chmod($priv, 0600);
        if (@file_put_contents($pub, $pubKey) === false) { return false; }
        @chmod($pub, 0644);
    }
    return true;
}

function jwt_get_private_key(): string {
    if (!jwt_ensure_keys()) { return ''; }
    $data = @file_get_contents(jwt_private_key_path());
    return $data !== false ? $data : '';
}

function jwt_get_public_key(): string {
    if (!jwt_ensure_keys()) { return ''; }
    $data = @file_get_contents(jwt_public_key_path());
    return $data !== false ? $data : '';
}
