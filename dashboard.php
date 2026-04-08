<?php
session_start();
include("conexion.php"); 
if (empty($_SESSION["id"]) || $_SESSION["rol"] != 'admin') { 
    header("location: index.php"); 
    exit(); 
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Panel Principal - Clínica San Pablo</title>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark p-3 shadow">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">🏥 Clínica San Pablo</span>
            <div class="d-flex">
                <a href="lista_pacientes.php" class="btn btn-primary me-2 shadow-sm">Pacientes</a>
                <a href="lista_doctores.php" class="btn btn-info text-white me-2 shadow-sm">Médicos</a>
                <a href="salir.php" class="btn btn-outline-danger">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-12 text-center">
                <h2>Bienvenido al Sistema de Gestión</h2>
                <p class="text-muted">Control de pacientes y citas médicas</p>
            </div>
        </div>

        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Próximas Citas Programadas</h5>
                <a href="asignar_cita.php" class="btn btn-warning btn-sm fw-bold">📅 Agendar Nueva Cita</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0 text-center">
                    <thead class="table-secondary">
                        <tr>
                            <th>Fecha</th>
                            <th>Paciente</th>
                            <th>Doctor / Especialidad</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT 
                                    c.fecha_cita, 
                                    p.nombre AS nom_paciente, 
                                    p.apellido AS ape_paciente, 
                                    d.nombre AS nom_doctor, 
                                    d.especialidad, 
                                    c.observaciones 
                                FROM citas c
                                INNER JOIN pacientes p ON c.id_paciente = p.id_paciente
                                INNER JOIN doctores d ON c.id_doctor = d.id_doctor
                                ORDER BY c.fecha_cita ASC";
                        
                        $query = $conexion->query($sql);

                        if ($query && $query->num_rows > 0) {
                            while($cita = $query->fetch_object()) { ?>
                                <tr>
                                    <td><strong><?= date("d/m/Y", strtotime($cita->fecha_cita)) ?></strong></td>
                                    <td><?= $cita->nom_paciente . " " . $cita->ape_paciente ?></td>
                                    <td>
                                        <span class="badge bg-info text-dark">Dr. <?= $cita->nom_doctor ?></span>
                                        <br><small class="text-muted"><?= $cita->especialidad ?></small>
                                    </td>
                                    <td><small><?= $cita->observaciones ?></small></td>
                                </tr>
                            <?php } 
                        } else {
                            echo "<tr><td colspan='4' class='p-4 text-muted'>No hay citas programadas aún.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>