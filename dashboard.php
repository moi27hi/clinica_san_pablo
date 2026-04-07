<?php
session_start();
if (empty($_SESSION["id"])) { header("location: index.php"); } // Protección de ruta
include("conexion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Dashboard - Clínica San Pablo</title>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark p-3">
        <span class="navbar-brand">Clínica San Pablo</span>
        <a href="salir.php" class="btn btn-outline-danger">Cerrar Sesión</a>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Pacientes Registrados</h5>
                        <p class="display-4">
                            <?php
                            $res = $conexion->query("SELECT COUNT(*) as total FROM pacientes");
                            echo $res->fetch_object()->total;
                            ?>
                        </p>
                        <a href="#" class="btn btn-info text-white">Ver listado</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Cuerpo Médico</h5>
                        <p class="display-4">
                            <?php
                            $res = $conexion->query("SELECT COUNT(*) as total FROM doctores");
                            echo $res->fetch_object()->total;
                            ?>
                        </p>
                        <a href="#" class="btn btn-success">Gestionar Doctores</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>