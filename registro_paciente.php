<?php
session_start();
if (empty($_SESSION["id"])) { header("location: index.php"); exit(); }
include("conexion.php");

if (!empty($_POST["btnregistrar"])) {
    $nom = $_POST["nombre"];
    $ape = $_POST["apellido"];
    $dir = $_POST["direccion"];
    $tel = $_POST["telefono"];
    
    $sql = $conexion->query("INSERT INTO pacientes (nombre, apellido, direccion, telefono) VALUES ('$nom', '$ape', '$dir', '$tel')");
    
    if ($sql) {
        header("location: dashboard.php?msg=ok");
    } else {
        echo "<div class='alert alert-danger'>Error al registrar</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Nuevo Paciente - Clínica San Pablo</title>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between">
                        <span>Registrar Nuevo Paciente</span>
                        <a href="dashboard.php" class="btn-close btn-close-white"></a>
                    </div>
                    <form class="card-body" method="POST">
                        <div class="mb-3"><label>Nombre</label><input type="text" name="nombre" class="form-control" required></div>
                        <div class="mb-3"><label>Apellido</label><input type="text" name="apellido" class="form-control" required></div>
                        <div class="mb-3"><label>Dirección</label><input type="text" name="direccion" class="form-control"></div>
                        <div class="mb-3"><label>Teléfono</label><input type="text" name="telefono" class="form-control"></div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" name="btnregistrar" value="ok" class="btn btn-success">Guardar Paciente</button>
                            <a href="dashboard.php" class="btn btn-secondary">Cancelar y Volver</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>