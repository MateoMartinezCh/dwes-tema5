<?php

/*********************************************************************************************************************
 * ? Este script muestra un formulario a través del cual se pueden buscar imágenes por el nombre y mostrarlas. Utiliza
 * el operador LIKE de SQL para buscar en el nombre de la imagen lo que llegue por $_GET['nombre'].
 * 
 * Evidentemente, tienes que controlar si viene o no por GET el valor a buscar. Si no viene nada, muestra el formulario
 * de búsqueda. Si viene en el GET el valor a buscar (en $_GET['nombre']) entonces hay que preparar y ejecutar una 
 * sentencia SQL.
 * 
 * El valor a buscar se tiene que mantener en el formulario.
 */

function filtra(String $texto): array
{
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        echo "No hay conexión con la base de datos";
        return [];
    }
    // Preparamos la consulta
    $sentencia = $mysqli->prepare("SELECT ruta from imagen where nombre like ?");
    if (!$sentencia) {
        echo "Error: " . $mysqli->error;
        $mysqli->close();
        return [];
    }
    // Bindeamos
    $valor = '%' . $texto . '%';
    $vinculo = $sentencia->bind_param("s", $valor);
    if (!$vinculo) {
        echo "Error al vincular: " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        return [];
    }

    // EJECUTAMOS
    $ejecucion = $sentencia->execute();
    if (!$ejecucion) {
        echo "Error al ejecutar la sentencia: " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        return [];
    }
    // Recuperamos las filas obtenidas como resultado
    $resultado = $sentencia->get_result();
    if (!$resultado) {
        echo "Error al obtener los resultados: " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        return [];
    }
    $vuelta = [];
    while (($fila = $resultado->fetch_assoc()) != null) {
        $vuelta[] = $fila;
    }
    return $vuelta;
}
/**********************************************************************************************************************
 * ? Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que realizar toda la lógica de este script
 */

$recuperonombre = $_GET && isset($_GET['nombre']) ? htmlspecialchars(trim($_GET['nombre'])) : "";
$imagenes = [];
if ($_GET && isset($_GET['nombre'])) {
    //PROGRAMA PRINCIPAL
    if (mb_strlen($recuperonombre) > 0) {
        $imagenes = filtra($recuperonombre);
    }
}

?>

<?php
/*********************************************************************************************************************
 * ? Salida HTML
 * 
 * Tareas a realizar:
 * - TODO: completa el código de la vista añadiendo el menú de navegación.
 * - TODO: en el formulario falta añadir el nombre que se puso cuando se envió el formulario.
 * - TODO: debajo del formulario tienen que aparecer las imágenes que se han encontrado en la base de datos.
 */

echo <<<END
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

        <h1>Galería de imágenes</h1>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><strong>Filtrar imágenes</strong></li>
            <li><a href="signup.php">Regístrate</a></li>
            <li><a href="login.php">Inicia sesión</a></li>
        </ul>
    END;
?>

<h2>Busca imágenes por filtro</h2>

<form method="get">
    <p>
        <label for="nombre">Busca por nombre</label>
        <input type="text" name="nombre" id="nombre" value='<?= $recuperonombre ?>'>
    </p>
    <p>
        <input type="submit" value="Buscar">
    </p>
</form>
<?php
foreach ($imagenes as $img) {
    echo <<<END
            <img src="{$img['ruta']}"/>
        END;
}
?>