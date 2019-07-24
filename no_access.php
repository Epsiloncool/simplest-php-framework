<?php

// Redirect to login
$uri = $_SERVER['REQUEST_URI'];

$path = parse_url($uri, PHP_URL_PATH);
$qr = parse_url($uri, PHP_URL_QUERY);

header('Location: /login/?return_to='.urlencode($path.((strlen($qr) > 0) ? '?'.$qr : '')));
