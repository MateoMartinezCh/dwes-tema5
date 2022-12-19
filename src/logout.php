<?php
session_start();
/**********************************************************************************************************************
 * Este script tan solo tiene que destruir la sesión y volver a la página principal.
 * 
 * UN USUARIO NO LOGEADO NO PUEDE ACCEDER A ESTE SCRIPT.
 */
if (!isset($_SESSION['usuario'])) {
    header('location:index.php');
    exit();
}
/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * TODO: tienes que realizar toda la lógica de este script
 */
session_destroy();
echo "<h1>Sesión cerrada correctamente</h1>";
echo "<a href='index.php'>Vuelve a la página principal</a>";
