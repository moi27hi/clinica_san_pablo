<?php
session_start();
include("conexion.php");
if (empty($_SESSION["id"]) || $_SESSION["rol"] != 'admin') {
    header("location: index.php");
    exit();
}

// --- ELIMINAR CITA ---
if (!empty($_GET["eliminar"])) {
    $id = (int) $_GET["eliminar"];
    $stmt = $conexion->prepare("DELETE FROM citas WHERE id_cita = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("location: dashboard.php?res=eliminada");
    exit();
}

// --- EDITAR CITA ---
if (!empty($_POST["btn_editar"])) {
    $id_cita   = (int) $_POST["id_cita"];
    $paciente  = (int) $_POST["paciente"];
    $doctor    = (int) $_POST["doctor"];
    $fecha     = $_POST["fecha"];
    $hora      = $_POST["hora"];
    $obs       = $_POST["observaciones"];

    $stmt = $conexion->prepare("UPDATE citas SET id_paciente=?, id_doctor=?, fecha_cita=?, hora_cita=?, observaciones=? WHERE id_cita=?");
    $stmt->bind_param("iisssi", $paciente, $doctor, $fecha, $hora, $obs, $id_cita);
    $stmt->execute();
    header("location: dashboard.php?res=editada");
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

        <?php if (isset($_GET['res'])): ?>
            <?php if ($_GET['res'] == 'eliminada'): ?>
                <div class="alert alert-warning">Cita eliminada correctamente.</div>
            <?php elseif ($_GET['res'] == 'editada'): ?>
                <div class="alert alert-success">Cita actualizada correctamente.</div>
            <?php endif; ?>
        <?php endif; ?>

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
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT
                                    c.id_cita,
                                    c.id_paciente,
                                    c.id_doctor,
                                    c.fecha_cita,
                                    c.hora_cita,
                                    c.observaciones,
                                    p.nombre AS nom_paciente,
                                    p.apellido AS ape_paciente,
                                    d.nombre AS nom_doctor,
                                    d.especialidad
                                FROM citas c
                                INNER JOIN pacientes p ON c.id_paciente = p.id_paciente
                                INNER JOIN doctores d ON c.id_doctor = d.id_doctor
                                ORDER BY c.fecha_cita ASC, c.hora_cita ASC";

                        $query = $conexion->query($sql);

                        if ($query && $query->num_rows > 0) {
                            while($cita = $query->fetch_object()) { ?>
                                <tr>
                                    <td>
                                        <strong><?= date("d/m/Y", strtotime($cita->fecha_cita)) ?></strong>
                                        <?php if ($cita->hora_cita): ?>
                                            <br><small class="text-muted"><?= date("h:i A", strtotime($cita->hora_cita)) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($cita->nom_paciente . " " . $cita->ape_paciente) ?></td>
                                    <td>
                                        <span class="badge bg-info text-dark">Dr. <?= htmlspecialchars($cita->nom_doctor) ?></span>
                                        <br><small class="text-muted"><?= htmlspecialchars($cita->especialidad) ?></small>
                                    </td>
                                    <td><small><?= htmlspecialchars($cita->observaciones) ?></small></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary mb-1"
                                            data-id="<?= $cita->id_cita ?>"
                                            data-paciente="<?= $cita->id_paciente ?>"
                                            data-doctor="<?= $cita->id_doctor ?>"
                                            data-fecha="<?= $cita->fecha_cita ?>"
                                            data-hora="<?= $cita->hora_cita ?>"
                                            data-obs="<?= htmlspecialchars($cita->observaciones) ?>"
                                            onclick="abrirEditar(this)">Editar</button>
                                        <a href="dashboard.php?eliminar=<?= $cita->id_cita ?>"
                                           onclick="return confirm('¿Eliminar esta cita permanentemente?')"
                                           class="btn btn-sm btn-outline-danger">Eliminar</a>
                                    </td>
                                </tr>
                            <?php }
                        } else {
                            echo "<tr><td colspan='5' class='p-4 text-muted'>No hay citas programadas aún.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Editar Cita -->
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Editar Cita</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="id_cita" id="edit_id_cita">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Paciente</label>
                            <select name="paciente" id="edit_paciente" class="form-select" required>
                                <?php
                                $pacientes = $conexion->query("SELECT id_paciente, nombre, apellido FROM pacientes ORDER BY nombre");
                                while ($p = $pacientes->fetch_object()) {
                                    echo "<option value='$p->id_paciente'>" . htmlspecialchars($p->nombre . " " . $p->apellido) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Doctor</label>
                            <select name="doctor" id="edit_doctor" class="form-select" required>
                                <?php
                                $doctores = $conexion->query("SELECT id_doctor, nombre, especialidad FROM doctores ORDER BY nombre");
                                while ($d = $doctores->fetch_object()) {
                                    echo "<option value='$d->id_doctor'>Dr. " . htmlspecialchars($d->nombre) . " (" . htmlspecialchars($d->especialidad) . ")</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Fecha</label>
                                <input type="date" name="fecha" id="edit_fecha" class="form-control" required>
                            </div>
                            <div class="col">
                                <label class="form-label">Hora</label>
                                <input type="time" name="hora" id="edit_hora" class="form-control" required min="07:00" max="18:00">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" id="edit_obs" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="btn_editar" value="ok" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function abrirEditar(btn) {
            document.getElementById('edit_id_cita').value  = btn.dataset.id;
            document.getElementById('edit_paciente').value = btn.dataset.paciente;
            document.getElementById('edit_doctor').value   = btn.dataset.doctor;
            document.getElementById('edit_fecha').value    = btn.dataset.fecha;
            document.getElementById('edit_hora').value     = btn.dataset.hora ? btn.dataset.hora.substring(0, 5) : '';
            document.getElementById('edit_obs').value      = btn.dataset.obs;
            new bootstrap.Modal(document.getElementById('modalEditar')).show();
        }
    </script>
</body>
</html>
