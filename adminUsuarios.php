<?php
session_start();
if ($_SESSION == null || $_SESSION["usuario"] == null) {
    echo "No estás autorizado para ver esta página.";
} else {
    // Conexión a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "futboleros";
    
    $bbdd = mysqli_connect($servername, $username, $password, $dbname);
    
    if (mysqli_connect_error()) {
        printf("Error conectando a la base de datos: %s\n", mysqli_connect_error());
        exit();
    }
    
    // Obtener la lista de usuarios activos
    $sql = "SELECT * FROM usuarios";
    $result = $bbdd->query($sql);
    
    $usuarios = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
    }
    
    // Verificar si se envió el formulario de modificación del estado
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['activo'])) {
        $idUsuario = $_POST['codigo'];
        $activo = $_POST['activo'];
        
        // Actualizar el estado del usuario
        $sql = "UPDATE usuarios SET activo = $activo WHERE codigo = $idUsuario";
        $result = $bbdd->query($sql);
        
        if ($result) {
            header("Location: adminUsuarios.php");
            exit();
        } else {
            $error_message = "Error al actualizar el usuario.";
        }
    }
    
    // Verificar si se envió el formulario de modificación del rol
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['rol'])) {
        $idUsuario = $_POST['codigo'];
        $admin = $_POST['rol'];
        
        // Actualizar el rol del usuario
        $sql = "UPDATE usuarios SET admin = '$admin' WHERE codigo = $idUsuario";
        $result = $bbdd->query($sql);
        
        if ($result) {
            header("Location: adminUsuarios.php");
            exit();
        } else {
            $error_message = "Error al actualizar el rol del usuario.";
        }
    }
    ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Administración | Futboleros</title>
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="./images/icon.png">
        <link rel="stylesheet" href="css/general.css">
        <link rel="stylesheet" href="css/table.css">
        <link rel="stylesheet" href="css/boton.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <script src="js/libCapas2122.js"></script>
    </head>
    <body>
        <header>FUTBOLER@S</header>

        <nav class="topnav" id="myTopnav">
            <a href="adminUsuarios.php">Usuarios</a>
            <a href="adminProductos.php">Productos</a>
            <a href="adminPedidos.php">Pedidos</a>
            <a href="logout.php">Cerrar sesión</a>

            <a href="javascript:void(0);" class="icon" onclick="myFunction()">
                <i class="fa fa-bars"></i>
            </a>
        </nav>

        <div class="content">
            <h1>Administración de usuarios</h1>

            <?php if (!empty($usuarios)) { ?>
                <table>
                    <tr>
                        <th>Código</th>
                        <th>Usuario</th>
                        <th>Nombre y apellidos</th>
                        <th>Email</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Rol</th>
                        <th>Modificar estado</th>
                        <th>Modificar rol</th>
                        <th>Modificar datos</th>
                    </tr>
                    <?php foreach ($usuarios as $usuario) { ?>
                        <tr>
                            <td><?php echo $usuario['codigo']; ?></td>
                            <td><?php echo $usuario['usuario']; ?></td>
                            <td><?php echo $usuario['nombre']; ?> <?php echo $usuario['apellidos']; ?></td>
                            <td><?php echo $usuario['email']; ?></td>
                            <td><?php echo $usuario['domicilio']; ?> <?php echo $usuario['poblacion']; ?> (<?php echo $usuario['cp']; ?>), <?php echo $usuario['provincia']; ?></td>
                            <td><?php echo $usuario['telefono']; ?></td>
                            <td><?php echo $usuario['activo'] ? 'Activo' : 'Suspendido'; ?></td>
                            <td><?php echo $usuario['admin'] == 1 ? 'Administrador' : 'Usuario común'; ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="codigo" value="<?php echo $usuario['codigo']; ?>">
                                    <input type="hidden" name="email" value="<?php echo $usuario['email']; ?>">
                                    <?php if ($usuario['activo'] == 1) { ?>
                                        <button class="button1" name="activo" value="0">Suspender</button>
                                    <?php } else { ?>
                                        <button class="button1" name="activo" value="1">Activar</button>
                                    <?php } ?>
                                </form>
                            </td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="codigo" value="<?php echo $usuario['codigo']; ?>">
                                    <input type="hidden" name="email" value="<?php echo $usuario['email']; ?>">
                                    <?php if ($usuario['admin'] == 1) { ?>
                                        <button class="button1" name="rol" value="0">Establecer como usuario común</button>
                                    <?php } else { ?>
                                        <button class="button1" name="rol" value="1">Establecer como administrador</button>
                                    <?php } ?>
                                </form>
                            </td>
                            <td>
                                <form method="GET" action="modificarUsuario.php">
                                    <input type="hidden" name="codigo" value="<?php echo $usuario['codigo']; ?>">
                                    <input type="hidden" name="email" value="<?php echo $usuario['email']; ?>">
                                    <input class="button2" type="submit" value="Modificar">
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <p>No hay usuarios registrados.</p>
            <?php } ?>

            <?php if (isset($error_message)) { ?>
                <p><?php echo $error_message; ?></p>
            <?php } ?>
        </div>
    </body>
</html>
<?php
    mysqli_close($bbdd);
}
?>
