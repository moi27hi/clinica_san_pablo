<?php
session_start();
if (empty($_SESSION["id"]) || $_SESSION["rol"] != 'admin') { header("location: index.php"); exit(); }
include("conexion.php");

// Eliminar doctor
if (!empty($_GET["id_eliminar"])) {
    $id = (int) $_GET["id_eliminar"];
    $stmt = $conexion->prepare("DELETE FROM doctores WHERE id_doctor = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("location: lista_doctores.php?res=eliminado");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Personal Médico - Clínica San Pablo</title>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary p-3 shadow">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">🏥 Clínica San Pablo - Doctores</span>
        <div class="d-flex">
            <a href="dashboard.php" class="btn btn-dark btn-sm me-2 shadow-sm">Inicio</a>
            <a href="lista_pacientes.php" class="btn btn-light btn-sm me-2 shadow-sm">Pacientes</a>
            <a href="salir.php" class="btn btn-danger btn-sm shadow-sm">Cerrar Sesión</a>
        </div>
    </div>
</nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Cuerpo Médico Actual</h3>
            <a href="registro_doctor.php" class="btn btn-success"> + Registrar Doctor</a>
        </div>

        <?php if (isset($_GET['res']) && $_GET['res'] == 'eliminado'): ?>
            <div class="alert alert-warning">Médico eliminado del sistema.</div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Especialidad</th>
                            <th>Teléfono</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = $conexion->query("SELECT * FROM doctores");
                        while($datos = $query->fetch_object()) { ?>
                            <tr>
                                <td><?= $datos->id_doctor ?></td>
                                <td><?= htmlspecialchars($datos->nombre) ?></td>
                                <td><?= htmlspecialchars($datos->especialidad) ?></td>
                                <td><?= htmlspecialchars($datos->telefono) ?></td>
                                <td>
                                    <a href="lista_doctores.php?id_eliminar=<?= $datos->id_doctor ?>"
                                       onclick="return confirm('¿Quitar a este médico del sistema?')"
                                       class="btn btn-sm btn-outline-danger">Quitar</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>