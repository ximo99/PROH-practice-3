<?php
session_start();
if ($_SESSION==null || $_SESSION["usuario"] ==null) {
    echo "No estás autorizado para ver esta página.";
} else {
    // Conexión a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "futboleros";
    
    $bbdd = mysqli_connect($servername, $username, $password, $dbname);

$bbdd = mysqli_connect($servername, $username, $password, $dbname);

if (mysqli_connect_error()) {
    printf("Error conectando a la base de datos: %s\n", mysqli_connect_error());
    exit();
}

// Obtener el código del usuario enviado por POST
if ($_SERVER["REQUEST_METHOD"] == "GET" || $_SERVER["REQUEST_METHOD"] == "POST") {
    
    $codigoUsuario = $_GET['codigo'];
    
    // Obtener los datos completos del usuario
    $sql = "SELECT * FROM usuarios WHERE codigo = $codigoUsuario";
    $result = $bbdd->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
    } else {
        echo "No se encontró el usuario.";
        exit();
    }
}

// Verificar si se envió el formulario de modificación
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevoEmail = $_POST['email'];
    $nuevoUsuario = $_POST['usuario'];
    $nuevaClave = $_POST['clave'];
    $nuevoNombre = $_POST['nombre'];
    $nuevosApellidos = $_POST['apellidos'];
    $nuevoDomicilio = $_POST['domicilio'];
    $nuevaPoblacion = $_POST['poblacion'];
    $nuevaProvincia = $_POST['provincia'];
    $nuevoCP = $_POST['cp'];
    $nuevoTelefono = $_POST['telefono'];
    
    // Actualizar los datos del usuario
    $stmt = $bbdd->prepare("UPDATE usuarios SET email = ?, usuario = ?, clave = ?, nombre = ?, apellidos = ?, domicilio = ?, poblacion = ?, provincia = ?, cp = ?, telefono = ? WHERE codigo = ?");
    $stmt->bind_param("ssssssssssi", $nuevoEmail, $nuevoUsuario, $nuevaClave, $nuevoNombre, $nuevosApellidos, $nuevoDomicilio, $nuevaPoblacion, $nuevaProvincia, $nuevoCP, $nuevoTelefono, $codigoUsuario);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        header("Location: adminUsuarios.php");
        exit();
    } else {
        $error_message = "Error al actualizar el usuario.";
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
    	<title> Administración | Futboleros </title>
    	<meta charset="UTF-8">
    	<link rel="shortcut icon" href="./images/icon.png">
        <link rel="stylesheet" href="css/general.css">
        <link rel="stylesheet" href="css/form.css">
        <link rel="stylesheet" href="css/boton.css">
    	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    	<script src="js/libCapas2122.js"> </script>
    </head>
    <body>
		<header> FUTBOLER@S</header>
		
		<nav class="topnav" id="myTopnav">
			<a href="adminUsuarios.php"> Usuarios</a>
			<a href="adminProductos.php"> Productos</a>
			<a href="adminPedidos.php"> Pedidos</a>
			<a href="logout.php"> Cerrar sesión</a>
			
			<a href="javascript:void(0);" class="icon" onclick="myFunction()">
		    	<i class="fa fa-bars"></i>
		  	</a>
		</nav>
		<div  class="content">
            <h1>Modificar Usuario</h1>
            
            <form method="POST" action="">
                <input type="hidden" name="codigo" value="<?php echo $usuario['codigo']; ?>">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo $usuario['email']; ?>" required>
                <br>
                <label>Usuario:</label>
                <input type="text" name="usuario" value="<?php echo $usuario['usuario']; ?>" required>
                <br>
                <label>Clave:</label>
                <input type="password" name="clave" value="<?php echo $usuario['clave']; ?>" required>
                <br>
                <label>Nombre:</label>
                <input type="text" name="nombre" value="<?php echo $usuario['nombre']; ?>" required>
                <br>
                <label>Apellidos:</label>
                <input type="text" name="apellidos" value="<?php echo $usuario['apellidos']; ?>" required>
                <br>
                <label>Domicilio:</label>
                <input type="text" name="domicilio" value="<?php echo $usuario['domicilio']; ?>" required>
                <br>
                <label>Población:</label>
                <input type="text" name="poblacion" value="<?php echo $usuario['poblacion']; ?>" required>
                <br>
                <label>Provincia:</label>
                <input type="text" name="provincia" value="<?php echo $usuario['provincia']; ?>" required>
                <br>
                <label>Código Postal:</label>
                <input type="text" name="cp" value="<?php echo $usuario['cp']; ?>" required>
                <br>
                <label>Teléfono:</label>
                <input type="text" name="telefono" value="<?php echo $usuario['telefono']; ?>" required>
                <br>
                <input class="button2" type="submit" value="Guardar">
            </form>
            
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