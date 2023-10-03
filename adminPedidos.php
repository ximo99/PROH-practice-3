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

// Obtener la lista de usuarios registrados
$queryUsuarios = "SELECT * FROM usuarios";
$resultadoUsuarios = mysqli_query($bbdd, $queryUsuarios);

// Obtener la lista de productos
$queryProductos = "SELECT * FROM productos";
$resultadoProductos = mysqli_query($bbdd, $queryProductos);

// Construir la consulta base de pedidos
$queryPedidos = "SELECT p.codigo, u.usuario, p.fecha, p.estado
                 FROM pedidos p
                 JOIN usuarios u ON p.persona = u.codigo";

// Aplicar filtros si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $usuario = $_GET["usuario"] ?? "";
    $producto = $_GET["producto"] ?? "";
    $fecha_opcion = $_GET["fecha_opcion"] ?? "";
    $fecha = $_GET["fecha"] ?? "";
    
    // Construir la consulta SQL para filtrar los pedidos
    $queryPedidos = "SELECT p.codigo, u.usuario, p.fecha, p.estado
                     FROM pedidos p
                     JOIN usuarios u ON p.persona = u.codigo
                     WHERE 1 = 1"; // Filtro base
    
    // Agregar filtros según los valores seleccionados
    if (!empty($usuario)) {
        $queryPedidos .= " AND u.codigo = $usuario";
    }
    
    if (!empty($producto)) {
        $queryPedidos .= " AND EXISTS (SELECT 1 FROM detalle d WHERE d.codigo_pedido = p.codigo AND d.codigo_producto = $producto)";
    }
    
    if (!empty($fecha)) {
        switch ($fecha_opcion) {
            case "<=":
                $queryPedidos .= " AND p.fecha <= '$fecha'";
                break;
            case "=":
                $queryPedidos .= " AND p.fecha = '$fecha'";
                break;
            case ">=":
                $queryPedidos .= " AND p.fecha >= '$fecha'";
                break;
        }
    }
    
    // Ejecutar la consulta de pedidos
    $resultadoPedidos = mysqli_query($bbdd, $queryPedidos);
}

// Manejar la actualización del estado del pedido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pedidoId = $_POST["pedidoId"];
    $nuevoEstado = $_POST["estado"];
    
    // Actualizar el estado del pedido en la base de datos
    $queryActualizarEstado = "UPDATE pedidos SET estado = $nuevoEstado WHERE codigo = $pedidoId";
    mysqli_query($bbdd, $queryActualizarEstado);
    
    // Obtener la lista actualizada de pedidos
    $resultadoPedidos = mysqli_query($bbdd, $queryPedidos);
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
        <h1>Administrar de pedidos</h1>
        <form method="GET" action="">
            <label for="usuario">Filtrar por Usuario:</label>
            <select name="usuario">
                <option value="">Todos</option>
                <?php while ($filaUsuario = mysqli_fetch_assoc($resultadoUsuarios)) { ?>
                    <option value="<?php echo $filaUsuario['codigo']; ?>"><?php echo $filaUsuario['email']; ?></option>
                <?php } ?>
            </select>
            <label for="producto">Filtrar por Producto:</label>
            <select name="producto">
                <option value="">Todos</option>
                <?php while ($filaProducto = mysqli_fetch_assoc($resultadoProductos)) { ?>
                    <option value="<?php echo $filaProducto['codigo']; ?>"><?php echo $filaProducto['descripcion']; ?></option>
                <?php } ?>
            </select>
            <label for="fecha_opcion">Filtrar por Fecha:</label>
            <select name="fecha_opcion">
                <option value="<=">Menor o igual</option>
                <option value="=">Igual</option>
                <option value=">=">Mayor o igual</option>
            </select>
            <input type="date" name="fecha">
            <br><br>
            <input class="button2" type="submit" value="Filtrar">
        </form>
    
        <h2>Lista de Pedidos</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Productos</th>
                <th>Fecha</th>
                <th>Estado</th>
            </tr>
            <?php if (isset($resultadoPedidos) && mysqli_num_rows($resultadoPedidos) > 0) {
                while ($filaPedido = mysqli_fetch_assoc($resultadoPedidos)) { ?>
                <tr>
                    <td><?php echo $filaPedido['codigo']; ?></td>
                    <td><?php echo $filaPedido['usuario']; ?></td>
                    <td>
                        <?php
                        // Obtener los productos del pedido
                        $pedidoId = $filaPedido['codigo'];
                        $queryDetalle = "SELECT d.codigo_pedido, d.codigo_producto, p.descripcion, d.unidades, d.precio_unitario "
                                        . "FROM detalle d "
                                        . "JOIN productos p ON d.codigo_producto = p.codigo "
                                        . "WHERE d.codigo_pedido = ?";
            
                        $stmt = mysqli_prepare($bbdd, $queryDetalle);
                        mysqli_stmt_bind_param($stmt, "i", $pedidoId);
                        mysqli_stmt_execute($stmt);
                        $resultadoDetalle = mysqli_stmt_get_result($stmt);
                                        
    
                        if (mysqli_num_rows($resultadoDetalle) > 0) {
                            while ($filaDetalle = mysqli_fetch_assoc($resultadoDetalle)) {
                                echo $filaDetalle['descripcion'] . "<br>";
                            }
                        }
                        ?>
                    </td>
                    <td><?php echo $filaPedido['fecha']; ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="pedidoId" value="<?php echo $filaPedido['codigo']; ?>">
                            <select name="estado">
                                <option value="1" <?php if ($filaPedido['estado'] == 1) echo "selected"; ?>>Pendiente</option>
                                <option value="2" <?php if ($filaPedido['estado'] == 2) echo "selected"; ?>>Enviado</option>
                                <option value="3" <?php if ($filaPedido['estado'] == 3) echo "selected"; ?>>Entregado</option>
                                <option value="4" <?php if ($filaPedido['estado'] == 4) echo "selected"; ?>>Cancelado</option>
                            </select>
                            <input class="button2" type="submit" value="Actualizar">
                        </form>
                    </td>
                </tr>
            <?php }} ?>
    	</table>
    </div>
</body>
</html>

<?php
mysqli_close($bbdd);
}
?>
