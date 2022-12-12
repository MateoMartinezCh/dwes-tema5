<?php

/**********************************************************************************************************************
 *
 * * Este script simplemente elimina la imagen de la base de datos y de la carpeta <imagen>
 *
 * *La información de la imagen a eliminar viene vía GET. Por GET se tiene que indicar el id de la imagen a eliminar
 * *de la base de datos.
 * 
 * *Busca en la documentación de PHP cómo borrar un fichero.
 * 
 * *Si no existe ninguna imagen con el id indicado en el GET o no se ha inicado GET, este script redirigirá al usuario
 * *a la página principal.
 * 
 * *En otro caso seguirá la ejecución del script y mostrará la vista de debajo en la que se indica al usuario que
 * *la imagen ha sido eliminada.
 */

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * *- TODO: tienes que desarrollar toda la lógica de este script.
 */

session_start();

$mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
if ($mysqli->connect_errno) {
    echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    exit();
}

if ($_GET && isset($_GET['id'])) {

    $idsana = htmlspecialchars(trim($_GET['id']));
    $resultado = $mysqli->query("select ruta from imagen where id=$idsana");
    if (($fila = $resultado->fetch_assoc()) != null) {
        $rutaimagen = htmlspecialchars(trim($fila['ruta']));
        $mysqli->query("delete from imagen where id=$idsana");
        if ($mysqli->affected_rows > 0) {
            $mysqli->close();
            //Borramos el fichero
            unlink(".$rutaimagen");
        } else {
            //Error porque no hay ninguna imagen con ese ID en la base
            header("location:index.php");
            $mysqli->close();
        }
    } else {
        header("location:index.php");
        $mysqli->close();
        //error por no obtener resultado en la consulta para obtener la ruta
        //$errornoencontrado = "<div class='alert alert-danger'>ERROR: No se han obtenido entradas en la base de datos</div>";
    }
    $resultado->free();
} else {
    //Error porque no hay ID o no hay nada en el get
    header("location:index.php");
    $mysqli->close();
}

/*********************************************************************************************************************
 * Salida HTML
 */
?>
<h1>Galería de imágenes</h1>

<p>Imagen eliminada correctamente</p>

<p>Vuelve a la <a href="index.php">página de inicio</a></p>