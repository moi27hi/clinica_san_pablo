<?php
session_start();
if (empty($_SESSION["id"]) || $_SESSION["rol"] != 'admin') { header("location: index.php"); exit(); }
include("conexion.php");

// --- LÓGICA PARA REGISTRAR PACIENTE ---
if (!empty($_POST["btnregistrar"])) {
    $nom = $_POST["nombre"];
    $ape = $_POST["apellido"];
    $dir = $_POST["direccion"];
    $tel = $_POST["telefono"];

    $stmt = $conexion->prepare("INSERT INTO pacientes (nombre, apellido, direccion, telefono) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nom, $ape, $dir, $tel);

    if ($stmt->execute()) {
        header("location: lista_pacientes.php?res=registrado");
        exit();
    } else {
        $error = "Error al registrar paciente.";
    }
}

// --- LÓGICA PARA ELIMINAR PACIENTE ---
if (!empty($_GET["id_eliminar"])) {
    $id = (int) $_GET["id_eliminar"];
    $stmt = $conexion->prepare("DELETE FROM pacientes WHERE id_paciente = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("location: lista_pacientes.php?res=eliminado");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Pacientes - Clínica San Pablo</title>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark p-3 shadow">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">🏥 Clínica San Pablo</span>
            <div class="d-flex">
                <a href="dashboard.php" class="btn btn-outline-light me-2">Inicio</a>
                <a href="lista_doctores.php" class="btn btn-info text-white me-2">Médicos</a>
                <a href="salir.php" class="btn btn-danger">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Listado General de Pacientes</h2>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRegistroPaciente">
                + Registrar Nuevo Paciente
            </button>
        </div>

        <?php 
        if(isset($_GET['res'])){
            if($_GET['res'] == 'registrado') echo '<div class="alert alert-success">Paciente registrado con éxito.</div>';
            if($_GET['res'] == 'eliminado') echo '<div class="alert alert-warning">Paciente eliminado del sistema.</div>';
        }
        ?>

        <div class="card shadow border-0">
            <div class="card-body p-0 text-center">
                <table class="table table-hover mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = $conexion->query("SELECT * FROM pacientes");
                        while($datos = $query->fetch_object()) { ?>
                            <tr>
                                <td><?= $datos->id_paciente ?></td>
                                <td><?= htmlspecialchars($datos->nombre) ?></td>
                                <td><?= htmlspecialchars($datos->apellido) ?></td>
                                <td><?= htmlspecialchars($datos->direccion) ?></td>
                                <td><?= htmlspecialchars($datos->telefono) ?></td>
                                <td>
                                    <a href="lista_pacientes.php?id_eliminar=<?= $datos->id_paciente ?>"
                                       onclick="return confirm('¿Eliminar permanentemente a este paciente?')"
                                       class="btn btn-sm btn-outline-danger">Quitar</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalRegistroPaciente" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Nuevo Registro de Paciente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control" required placeholder="Ej. Juan">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Apellido</label>
                            <input type="text" name="apellido" class="form-control" required placeholder="Ej. Pérez">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="direccion" class="form-control" placeholder="Dirección completa">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" placeholder="Ej. 7766-5544">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="btnregistrar" value="ok" class="btn btn-success">Guardar Paciente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>