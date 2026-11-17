<?php 
    $host = "localhost";
    $user = "";
    $pass = ""; // Ojo: Las contraseñas suelen ser diferentes en prod
    $db = "";
    $db_timezone = "-04:00"; // Por defecto Venezuela (UTC -4)

    // Detectar el dominio actual
    $domain = $_SERVER['HTTP_HOST'];

    
    // Configuración para la versión Venezuela
    $user = "caronico_admin";
    $pass = "B20s9#Wu-vyJM8";
    $db = "caronico_3d";
    date_default_timezone_set("America/Caracas");
    $db_timezone = "-04:00";
    
    
    $connection = new mysqli($host, $user, $pass, $db);

    // Verificar si la conexión falló
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
    
    // 1. Establecer el juego de caracteres a utf8mb4
    $connection->set_charset("utf8mb4");
    
    // 2. CORRECCIÓN DEFINITIVA: Sincronizar la zona horaria de MySQL con la del país
    $connection->query("SET time_zone = '$db_timezone'");

?>