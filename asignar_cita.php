<?php
session_start();
if (empty($_SESSION["id"])) { header("location: index.php"); exit(); }
include("conexion.php");

// Lógica para guardar la relación
if (!empty($_POST["btnasignar"])) {
    $paciente = $_POST["paciente"];
    $doctor = $_POST["doctor"];
    $fecha = $_POST["fecha"];
    $obs = $_POST["observaciones"];

    $sql = $conexion->query("INSERT INTO citas (id_paciente, id_doctor, fecha_cita, observaciones) VALUES ('$paciente', '$doctor', '$fecha', '$obs')");
    
    if ($sql) {
        echo "<div class='alert alert-success mt-2 text-center'>Cita programada con éxito</div>";
    } else {
        echo "<div class='alert alert-danger mt-2'>Error: " . $conexion->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Asignar Cita - Clínica San Pablo</title>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 card shadow p-4">
                <h3 class="text-center mb-4 text-primary">Nueva Cita Médica</h3>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Seleccionar Paciente</label>
                        <select name="paciente" class="form-select" required>
                            <option value="">-- Seleccione un paciente --</option>
                            <?php
                            $query = $conexion->query("SELECT id_paciente, nombre, apellido FROM pacientes");
                            while($p = $query->fetch_object()){
                                echo "<option value='$p->id_paciente'>$p->nombre $p->apellido</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Seleccionar Doctor</label>
                        <select name="doctor" class="form-select" required>
                            <option value="">-- Seleccione un médico --</option>
                            <?php
                            $query = $conexion->query("SELECT id_doctor, nombre, especialidad FROM doctores");
                            while($d = $query->fetch_object()){
                                echo "<option value='$d->id_doctor'>Dr. $d->nombre ($d->especialidad)</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fecha de la Cita</label>
                        <input type="date" name="fecha" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="btnasignar" value="ok" class="btn btn-primary">Agendar Cita</button>
                        <a href="dashboard.php" class="btn btn-outline-secondary">Volver al Inicio</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>