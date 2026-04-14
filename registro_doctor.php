<?php
session_start();
if (empty($_SESSION["id"]) || $_SESSION["rol"] != 'admin') { header("location: index.php"); exit(); }
include("conexion.php");

if (!empty($_POST["btnregistrar"])) {
    $nom = $_POST["nombre"];
    $esp = $_POST["especialidad"];
    $tel = $_POST["telefono"];

    $stmt = $conexion->prepare("INSERT INTO doctores (nombre, especialidad, telefono) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nom, $esp, $tel);

    if ($stmt->execute()) {
        header("location: lista_doctores.php?msg=ok");
        exit();
    } else {
        $error = "Error al registrar el médico.";
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