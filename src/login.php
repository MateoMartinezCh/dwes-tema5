<?php

/**********************************************************************************************************************
 * Este programa, a través del formulario que tienes que hacer debajo, en el área de la vista, realiza el inicio de
 * sesión del usuario verificando que ese usuario con esa contraseña existe en la base de datos.
 * 
 * Para mantener iniciada la sesión dentrás que usar la $_SESSION de PHP.
 * 
 * En el formulario se deben indicar los errores ("Usuario y/o contraseña no válido") cuando corresponda.
 * 
 * Dicho formulario enviará los datos por POST.
 * 
 * Cuando el usuario se haya logeado correctamente y hayas iniciado la sesión, redirige al usuario a la
 * página principal.
 * 
 * UN USUARIO LOGEADO NO PUEDE ACCEDER A ESTE SCRIPT.
 */
session_start();
//si el usuario está logeado, ¿qué hace aquí Lo echamos.
if (isset($_SESSION['usuario'])) {
    header('location: index.php');
    exit();
}
/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * TODO: tienes que realizar toda la lógica de este script
 */


/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar en la vista:
 * * TODO: añadir el menú.
 * * TODO: formulario con nombre de usuario y contraseña.
 */
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">';
?>

<main>
    <?php
    echo <<<END
    <h1>Galería de imágenes</h1>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="filter.php">Filtrar imágenes</a></li>
            <li><a href="signup.php">Regístrate</a></li>
            <li><strong>Inicia sesión</strong></li>
        </ul>
    END;
    ?>
    <h1>Inicia sesión</h1>
    <?php
    if ($_POST) {
        echo "<p>Usuario y/o contraseña no validos</p>";
    }
    ?>
    <form action="login.php" method="post">
        <p>
            <label for="usuario">Nombre de usuario</label><br>
            <input type="text" name="usuario" id="usuario" value=<?php echo $_POST && isset($_POST['usuario']) ? $_POST['usuario'] : "" ?>>
        </p>
        <p>
            <label for="clave">Contraseña</label><br>
            <input type="password" name="clave" id="clave">
        </p>
        <p>
            <input type="submit" value="Inicia sesión">
        </p>
    </form>
</main>