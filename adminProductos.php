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
    
    if (mysqli_connect_error()) {
        printf("Error conectando a la base de datos: %s\n", mysqli_connect_error());
        exit();
    }
    
    // Obtener la lista de productos
    $sql = "SELECT * FROM productos";
    $result = $bbdd->query($sql);
    
    $productos = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
    }
    
    // Verificar si se envió el formulario de modificación o alta
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $idProducto = isset($_POST['idProducto']) ? $_POST['idProducto'] : "";
        $descripcion = isset($_POST['nombreProducto']) ? $_POST['nombreProducto'] : "";
        $precio = isset($_POST['precio']) ? $_POST['precio'] : "";
        $existencias = isset($_POST['existencia']) ? $_POST['existencia'] : "";
        
        if ($idProducto != "") {
            // Actualizar producto existente
            $sql = "UPDATE productos SET descripcion = '$descripcion', precio = $precio, existencias = $existencias";
            
            if ($_FILES['imagen']['size'] > 0) {
                $imagen = $_FILES['imagen']['name'];
                $tmp_name = $_FILES['imagen']['tmp_name'];
                $folder = "images/";
                
                move_uploaded_file($tmp_name, $folder . $imagen);
                
                $sql .= ", imagen = '$folder$imagen'";
            }
            
            $sql .= " WHERE codigo = $idProducto";
        } else {
            // Insertar nuevo producto
            $sql = "INSERT INTO productos (descripcion, precio, existencias, imagen) VALUES ('$descripcion', $precio, $existencias, '$folder$imagen')";
        }
        
        $result = $bbdd->query($sql);
        
        if ($result) {
            header("Location: adminProductos.php");
            exit();
        } else {
            $error_message = "Error al guardar el producto.";
        }
    }
    
    mysqli_close($bbdd);
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
        <link rel="stylesheet" href="css/table.css">
        <link rel="stylesheet" href="css/boton.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <script src="js/libCapas2122.js"> </script>
        <script>
            function mostrarAlert() {
                alert("El producto se ha modificado correctamente.");
            }
        </script>
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
            <h1>Administración de productos</h1>
            <a class="button2" href="insertarProducto.php">Insertar producto</a>
            
            <?php if (!empty($productos)) { ?>
                <table>
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Existencias</th>
                        <th>Imagen</th>
                        <th>Acciones</th>
                    </tr>
                    <?php foreach ($productos as $producto) { ?>
                        <tr>
                            <td><?php echo $producto['codigo']; ?></td>
                            <td><?php echo $producto['descripcion']; ?></td>
                            <td><?php echo $producto['precio']; ?></td>
                            <td><?php echo $producto['existencias']; ?></td>
                            <td>
                                <img src="<?php echo $producto['imagen']; ?>" alt="Imagen del producto" width="100" height="100">
                            </td>
                            <td>
                                <form method="POST" action="" onsubmit="mostrarAlert()" enctype="multipart/form-data">
                                    <input type="hidden" name="idProducto" value="<?php echo $producto['codigo']; ?>">
                                    <input type="text" name="nombreProducto" value="<?php echo $producto['descripcion']; ?>" required>
                                    <input type="number" name="precio" value="<?php echo $producto['precio']; ?>" required>
                                    <input type="number" name="existencia" value="<?php echo $producto['existencias']; ?>" required>
                                    <input type="file" name="imagen">
                                    <input class="button2" type="submit" value="Guardar">
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <p>No hay productos registrados.</p>
            <?php } ?>
        </div>
    </body>
</html>
