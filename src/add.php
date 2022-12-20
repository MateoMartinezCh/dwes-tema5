<?php

/**********************************************************************************************************************
 * Este es el script que añade imágenes en la base de datos. En la tabla "imagen" de la base de datos hay que guardar
 * el nombre que viene vía POST, la ruta de la imagen como se indica más abajo, la fecha de la inserción (función
 * UNIX_TIMESTAMP()) y el identificador del usuario que inserta la imagen (el usuario que está logeado en estos
 * momentos).
 * 
 * ¿Cuál es la ruta de la imagen? ¿De dónde sacamos esta ruta? Te lo explico a continuación:
 * - Busca una forma de asignar un nombre que sea único.
 * - La extensión será la de la imagen original, que viene en $_FILES['imagne']['name'].
 * - Las imágenes se subirán a la carpeta llamada "imagenes/" que ves en el proyecto.
 * - En la base de datos guardaremos la ruta relativa en el campo "ruta" de la tabla "imagen".
 * 
 * Así, si llega por POST una imagen PNG y le asignamos el nombre "imagen1", entonces en el campo "ruta" de la tabla
 * "imagen" de la base de datos se guardará el valor "imagenes/imagen1.png".
 * 
 * Como siempre:
 * 
 * - Si no hay POST, entonces tan solo se muestra el formulario.
 * - Si hay POST con errores se muestra el formulario con los errores y manteniendo el nombre en el campo nombre.
 * - Si hay POST y todo es correcto entonces se guarda la imagen en la base de datos para el usuario logeado.
 * 
 * Esta son las validaciones que hay que hacer sobre los datos POST y FILES que llega por el formulario:
 * - En el nombre debe tener algo (mb_strlen > 0).
 * - La imagen tiene que ser o PNG o JPEG (JPG). Usa FileInfo para verificarlo.
 * 
 * NO VAMOS A CONTROLAR SI YA EXISTE UNA IMAGEN CON ESE NOMBRE. SI EXISTE, SE SOBREESCRIBIRÁ Y YA ESTÁ.
 * 
 * *A ESTE SCRIPT SOLO SE PUEDE ACCEDER SI HAY UN USARIO LOGEADO.
 */
session_start();
//si el usuario no está logeado, ¿qué hace aquí? Lo echamos.
if (!isset($_SESSION['usuario'])) {
    header('location: index.php');
    exit();
}
/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * TODO: tienes que desarrollar toda la lógica de este script.
 */
function imprimirFormulario($errornombre, $errorarchivo, $nombre): void
{
    echo <<<END
    <form method="post" enctype="multipart/form-data">
        <p>
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="$nombre">
        </p>
        $errornombre
        <p>
            <label for="imagen">Imagen</label>
            <input type="file" name="imagen" id="imagen">
        </p>
        $errorarchivo
        <p>
            <input type="submit" value="Añadir">
        </p>
    </form>
    END;
}
$errornombre = "";
$errorarchivo = "";
if ($_POST) {
    //PREPARAMOS TODO PARA SUBIR EL ARCHIVO A EL SERVIDOR
    $validarfichero = $_FILES && isset($_FILES['imagen']) &&
        $_FILES['imagen']['error'] === UPLOAD_ERR_OK &&
        $_FILES['imagen']['size'] > 0;

    if (isset($_POST['nombre']) && mb_strlen($_POST['nombre'] > 0) && $_POST['nombre'] != " ") {
        $nombresano = htmlspecialchars(trim($_POST['nombre']));
        if ($validarfichero) {
            $permitidos = array("png", "jpg");
            $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $mimesPermitidos = array("image/png", "image/jpeg", "image/jpg");
            $fichero = $_FILES['imagen']['tmp_name'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_fichero = finfo_file($finfo, $fichero);
            if (in_array($extension, $permitidos) && in_array($mime_fichero, $mimesPermitidos)) {
                $_FILES['imagen']['name'] = $nombresano . "." . $extension;
                $rutaFicheroDestino = './imagenes/' . basename($_FILES['imagen']['name']);
            } else {
                $errorarchivo = "<div class='alert alert-danger'>Extensión de archivo incorrecta</div>";
            }
        } else {
            $errorarchivo = "<div class='alert alert-danger'>No hay archivo</div>";
        }
    } else {
        $errornombre = "<div class='alert alert-danger'>El nombre está vacío</div>";
        $errorarchivo = $validarfichero ? "" : "<div class='alert alert-danger'>No hay archivo</div>";
    }
    //SI TODO HA IDO BIEN AÑADIMOS REGISTRO A LA BASE DE DATOS Y SUBIREMOS EL FICHERO
    if ($errorarchivo == "" && $errornombre == "") {

        //conexión con mysqli
        $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
        if ($mysqli->connect_errno) {
            echo "No hay conexión con la base de datos";
            exit();
        }
        // Preparamos la consulta
        $sentencia = $mysqli->prepare("INSERT INTO imagen (nombre,ruta,subido,usuario) values (?,?,?,?)");
        if (!$sentencia) {
            echo "Error: " . $mysqli->error;
            $mysqli->close();
            exit();
        }
        // Bindeamos
        $id = $_SESSION['id'];
        $nombre = $nombresano;
        $tiempo = "UNIX_TIMESTAMP()";
        $ruta = $rutaFicheroDestino;
        $vinculo = $sentencia->bind_param("ssis", $nombre, $ruta, $tiempo, $id);
        if (!$vinculo) {
            echo "Error al vincular: " . $mysqli->error;
            $sentencia->close();
            $mysqli->close();
            exit();
        }

        // EJECUTAMOS
        $ejecucion = $sentencia->execute();
        if (!$ejecucion) {
            echo "Error al ejecutar la sentencia: " . $mysqli->error;
            $sentencia->close();
            $mysqli->close();
            exit();
        }
        //Cerramos la sentencia y liberamos recurso
        $sentencia->close();
        // También se cierra la conexión con la base de datos a través del objeto mysqli
        $mysqli->close();

        //SI TODO HA IDO BIEN LLEGAREMOS HASTA AQUÍ DONDE SUBIREMOS FINALMENTE EL FICHERO AL SERVIDOR
        move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaFicheroDestino);
        echo "<h1>El fichero se ha subido correctamente</h1>";
        echo "<a href='index.php'>¡Vuelve al índice para verlo!</a></br>";
        echo "<a href='filter.php'>¡O búscalo por nombre en nuestro buscador!</a>";
        exit();
    }
}



/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar:
 * TODO: añadir el menú de navegación.
 * TODO: añadir en el campo del nombre el valor del mismo cuando haya errores en el envío para mantener el nombre
 *         que el usuario introdujo.
 * TODO: añadir los errores que se produzcan cuando se envíe el formulario debajo de los campos.
 */
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
<h1>Galería de imágenes</h1>
<?php {
    echo <<<END
    <ul>
        <li><a href=index.php>Home</a></li>
        <li><strong>Añadir imagen</strong></li>
        <li><a href="filter.php">Filtrar imágenes</a></li>
        <li><a href="logout.php">Cerrar sesión ({$_SESSION['usuario']})</a></li>
    </ul>
    <h2>Añade tu imágen!</h2>
    END;
    imprimirFormulario($errornombre, $errorarchivo, isset($_POST['nombre']) ? htmlspecialchars(trim($_POST['nombre'])) : "");
}
?>