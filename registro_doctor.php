<?php
session_start();
if (empty($_SESSION["id"])) { header("location: index.php"); exit(); }
include("conexion.php");

if (!empty($_POST["btnregistrar"])) {
    $nom = $_POST["nombre"];
    $esp = $_POST["especialidad"];
    $tel = $_POST["telefono"];
    
    // Eliminamos 'id_paciente' de la consulta INSERT
    $sql = $conexion->query("INSERT INTO doctores (nombre, especialidad, telefono) VALUES ('$nom', '$esp', '$tel')");
    
    if ($sql) {
        header("location: lista_doctores.php?msg=ok");
    } else {
        echo "<div class='alert alert-danger'>Error al registrar: " . $conexion->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Nuevo Doctor - Clínica San Pablo</title>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0">
                    <div class="card-header bg-dark text-white text-center">
                        <h4 class="mb-0">Registrar Nuevo Médico</h4>
                    </div>
                    <form class="card-body" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Especialidad</label>
                            <input type="text" name="especialidad" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control">
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" name="btnregistrar" value="ok" class="btn btn-primary">Guardar Doctor</button>
                            <a href="lista_doctores.php" class="btn btn-outline-secondary">Volver al Listado</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>