<?php

require_once __DIR__ . '/../vendor/autoload.php';

// redirect(), getUrl(), etc. no son clases, así que Composer no las autocarga.
// Los controladores las usan (ej: AccesoController::login() llama redirect()),
// así que hay que cargarlas a mano antes de correr los tests.
require_once __DIR__ . '/../lib/helpers.php';
