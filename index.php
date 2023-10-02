<?php
session_start();
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


// Verificar el envío del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];
    
    // Consulta para verificar las credenciales del usuario
    $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND clave = '$contrasena' AND admin = 1";
    $result = $bbdd->query($sql);
    
    if ($result->num_rows == 1) {
        // Inicio de sesión exitoso, redireccionar a la página de administrador
        $_SESSION["usuario"] = $usuario;
        header("Location: adminUsuarios.php");
        exit();
    } else {
        // Credenciales incorrectas, mostrar mensaje de error
        $error_message = "Credenciales incorrectas. Por favor, intenta nuevamente.";
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
		<title> Futboleros </title>
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
    	<div  class="content">
        	<h1>Zona de administración</h1>
        	<form method="POST" action="">
                <label for="usuario">Usuario:</label>
                <input type="text" name="usuario" required>
                <br><br>
                <label for="contrasena">Contraseña:</label>
                <input type="password" name="contrasena" required>
                <br><br>
                <input class="button2" type="submit" value="Iniciar sesión">
            </form>
        
            <?php if (isset($error_message)) { ?>
                <h3><?php echo $error_message; ?></h3>
            <?php } ?>
    	</div>
        
        <footer>
			<a href="https://www.facebook.com/">
				<i class="fa fa-facebook"></i>
			</a>
			
			<a href="https://twitter.com/">
				<i class="fa fa-twitter"></i>
			</a>
			
			<a href="https://www.instagram.com/">
				<i class="fa fa-instagram"></i>
			</a>
		</footer>
    </body>
</html>

<?php
mysqli_close($bbdd);
?>