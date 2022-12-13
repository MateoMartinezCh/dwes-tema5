<?php

/*********************************************************************************************************************
 * Este script realiza el registro del usuario vía el POST del formulario que hay debajo, en la vista.
 * 
 * Cuando llegue POST hay que validarlo y si todo fue bien insertar en la base de datos el usuario.
 * 
 * Requisitos del POST:
 * *- El nombre de usuario no tiene que estar vacío y NO PUEDE EXISTIR UN USUARIO CON ESE NOMBRE EN LA BASE DE DATOS.
 * *- La contraseña tiene que ser, al menos, de 8 caracteres.
 * *- Las contraseñas tiene que coincidir.
 * 
 * *La contraseña la tienes que guardar en la base de datos cifrada mediante el algoritmo BCRYPT.
 * 
 * * UN USUARIO LOGEADO NO PUEDE ACCEDER A ESTE SCRIPT.
 */
session_start();
//si el usuario está logeado, ¿qué hace aquí Lo echamos.
if (isset($_SESSION['usuario'])) {
    header('location: index.php');
    exit();
}/*
function existeUsuario(string $nombre, mysqli $mysqli): bool
{
    $resultado = $mysqli->query("select * from usuario where nombre=$nombre");
    $resultado ? $existe = true : $existe = false;

    return $existe;
}  */
function insertarUsuario(string $nombre, string $claveencriptada, mysqli $mysqli): bool
{
    $sehainsertadousuario = false;
    /*  if (!existeUsuario($nombre, $mysqli)) {
        $mysqli->query("Insert into usuario (nombre,clave)values ('$nombre','$claveencriptada')");
        $sehainsertadousuario = true;
    } */
    $mysqli->query("Insert into usuario (nombre,clave)values ('$nombre','$claveencriptada')");

    if (!$mysqli->error) {
        $sehainsertadousuario = true;
    }
    return $sehainsertadousuario;
}
/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que realizar toda la lógica de este script
 */
$mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
if ($mysqli->connect_errno) {
    echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    exit();
}
if ($mysqli->affected_rows > 0) {
}
$errornombre = '';
$errorclave = '';
$errorclaverepetida = '';
$errorclavesdiferentes = '';
$errorusuario = '';
$nombresano = '';
if ($_POST && isset($_POST['nombre']) && isset($_POST['clave']) && isset($_POST['repite_clave'])) {
    if (empty($_POST['nombre'])) {
        $errornombre = "<div class='alert alert-danger'>El nombre está vacío</div>";
    } else {
        $nombresano = htmlspecialchars(trim($_POST['nombre']));
    }
    if (mb_strlen($_POST['clave']) < 8) {
        $errorclave = "<div class='alert alert-danger'>La clave tiene menos de 8 caracteres</div>";
    }
    if (empty($_POST['clave'])) {
        $errorclave = "<div class='alert alert-danger'>La clave está vacía</div>";
    }
    if (empty($_POST['repite_clave'])) {
        $errorclaverepetida = "<div class='alert alert-danger'>La clave repetida está vacía</div>";
    }
    if (!empty($_POST['nombre']) && (!empty($_POST['clave']) || mb_strlen($_POST['clave']) < 8) && !empty($_POST['repite_clave'])) {
        $clavesana = htmlspecialchars(trim($_POST['clave']));
        $repite_clavesana = htmlspecialchars(trim($_POST['repite_clave']));
        if ($clavesana != $repite_clavesana) {
            $errorclavesdiferentes = "<div class='alert alert-danger'>ERROR: Las claves no son iguales</div>";
        } else {
            $claveencriptada = password_hash($clavesana, PASSWORD_BCRYPT);
            if (!insertarUsuario($nombresano, $claveencriptada, $mysqli)) {
                $errorusuario = "<div class='alert alert-danger'>No se ha podido insertar un usuario, ya hay un usuario con ese nombre en la base de datos</div>";
            }
        }
    }
}

/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar en la vista:
 * *- TODO: los errores que se produzcan tienen que aparecer debajo de los campos.(AHORA MISMO ME SALEN AL LADO)
 * *- TODO: cuando hay errores en el formulario se debe mantener el valor del nombre de usuario en el campo
 *         correspondiente.
 */
?>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <style>
        form,
        h1,
        h2 {
            margin: 0 auto;
        }

        p {
            margin-top: 30px;
        }
    </style>
</head>

<body>

    <?php if (!$_POST || $errornombre != '' || $errorclave != '' || $errorclaverepetida != '' || $errorclavesdiferentes != '' || $errorusuario != '' || $nombresano = '') {
        echo <<<END
        <h1>Galería de imágenes</h1>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="filter.php">Filtrar imágenes</a></li>
            <li><strong>Regístrate</strong></li>
            <li><a href="login.php">Inicia sesión</a></li>
        </ul>
        END; ?>
        <h1>Regístrate</h1>

        <form action="signup.php" method="post">
            <p>
                <label for="nombre">Nombre de usuario</label>
                <input type="text" name="nombre" id="nombre" value="<?= $nombresano ?>">
                <?= $errornombre ?>
            </p>
            <p>
                <label for="clave">Contraseña</label>
                <input type="password" name="clave" id="clave">
                <?= $errorclave ?>
            </p>
            <p>
                <label for="repite_clave">Repite la contraseña</label>
                <input type="password" name="repite_clave" id="repite_clave">
                <?= $errorclaverepetida ?>
            </p>
            <?= $errorclavesdiferentes ?>
            <?= $errorusuario ?>
            <p>
                <input type="submit" value="Regístrate">
            </p>
        </form>
    <?php } else {

        echo "<h3>Te has registrado correctamente.</h3>";
        echo "<h5><a href='index.php'>Ir de vuelta al inicio</a></h5>";
    } ?>
</body>