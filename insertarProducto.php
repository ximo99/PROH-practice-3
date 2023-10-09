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
    
    // Verificar si se envió el formulario de inserción
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $descripcion = isset($_POST['nombreProducto']) ? $_POST['nombreProducto'] : "";
        $precio = isset($_POST['precio']) ? $_POST['precio'] : "";
        $existencias = isset($_POST['existencia']) ? $_POST['existencia'] : "";
        
        $imagen = "";
        if (isset($_FILES['imagen'])) {
            $file = $_FILES['imagen'];
            $filename = $file['name'];
            $filetmp = $file['tmp_name'];
            
            // Verificar si se seleccionó una imagen
            if (!empty($filename)) {
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                
                // Insertar nuevo producto
                $sql = "INSERT INTO productos (descripcion, precio, existencias, imagen) VALUES ('$descripcion', $precio, $existencias, '')";
                $result = $bbdd->query($sql);
                
                if ($result) {
                    $codigo = $bbdd->insert_id; // Obtener el código del nuevo producto insertado
                    $imagen = "images/$codigo.$ext";
                    
                    // Actualizar la URL de la imagen en la base de datos
                    $sql = "UPDATE productos SET imagen = '$imagen' WHERE codigo = $codigo";
                    $result = $bbdd->query($sql);
                    
                    if ($result) {
                        move_uploaded_file($filetmp, $imagen);
                        mysqli_close($bbdd);
                        echo '<script>alert("Producto insertado en la base de datos satisfactoriamente.\nHas añadido: '.$descripcion.'\nPrecio: '.$precio.'\nExistencias: '.$existencias.'"); window.location.href = "adminProductos.php";</script>';
                        exit();
                    } else {
                        $error_message = "Error al guardar el producto.";
                    }
                } else {
                    $error_message = "Error al guardar el producto.";
                }
            }
        }
    }
    ?>

<!DOCTYPE html>
<html>
    <head>
        <title>Insertar producto | Futboleros</title>
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="./images/icon.png">
        <link rel="stylesheet" href="css/general.css">
        <link rel="stylesheet" href="css/form.css">
        <link rel="stylesheet" href="css/boton.css">
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
            <h1>Insertar nuevo producto</h1>

            <form method="POST" action="" enctype="multipart/form-data">
                <label for="nombreProducto">Descripción:</label>
                <input type="text" id="nombreProducto" name="nombreProducto" required>

                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" required>

                <label for="existencia">Existencias:</label>
                <input type="number" id="existencia" name="existencia" required>

                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" name="imagen">

                <input class="button2" type="submit" value="Guardar">
            </form>
        </div>
    </body>
</html>

<?php
    mysqli_close($bbdd);
}
?>
