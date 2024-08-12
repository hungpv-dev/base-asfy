<?php
session_start();

require_once 'vendor/autoload.php';

Dotenv\Dotenv::createImmutable(__DIR__)->load();

require_once 'config/app.php';
require_once 'config/database.php';
require_once 'app/Utils/DB.php';
require_once 'config/routes.php';
