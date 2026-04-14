<?php
session_start();
include("conexion.php"); 
if (empty($_SESSION["id"]) || $_SESSION["rol"] != 'paciente') { 
    header("location: index.php"); 
    exit(); 
}
// Capturamos el ID del paciente logueado para usarlo en las consultas
$id_logueado = $_SESSION['id']; 

// --- LÓGICA: ACTUALIZAR DATOS PERSONALES ---
if (!empty($_POST["btn_actualizar"])) {
    $nom = $_POST["nuevo_nombre"];
    $ape = $_POST["nuevo_apellido"];
    $dir = $_POST["direccion"];
    $tel = $_POST["telefono"];

    $stmt = $conexion->prepare("UPDATE pacientes SET nombre=?, apellido=?, direccion=?, telefono=? WHERE id_paciente=?");
    $stmt->bind_param("ssssi", $nom, $ape, $dir, $tel, $id_logueado);

    if ($stmt->execute()) {
        $mensaje = "<div class='alert alert-success'>¡Datos actualizados correctamente!</div>";
    }
}

// --- LÓGICA: AGENDAR CITA ---
if (!empty($_POST["btn_cita"])) {
    $doc_id = (int) $_POST["doctor"];
    $fec    = $_POST["fecha"];
    $hora   = $_POST["hora"];
    $obs    = $_POST["observaciones"];

    $stmt = $conexion->prepare("INSERT INTO citas (id_paciente, id_doctor, fecha_cita, hora_cita, observaciones) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $id_logueado, $doc_id, $fec, $hora, $obs);

    if ($stmt->execute()) {
        $stmt_doc = $conexion->prepare("SELECT nombre, especialidad FROM doctores WHERE id_doctor = ?");
        $stmt_doc->bind_param("i", $doc_id);
        $stmt_doc->execute();
        $datos_doc = $stmt_doc->get_result()->fetch_object();

        $fecha_formateada = date("d/m/Y", strtotime($fec));
        $hora_formateada  = date("h:i A", strtotime($hora));
        $nom_doctor  = htmlspecialchars($datos_doc->nombre);
        $esp_doctor  = htmlspecialchars($datos_doc->especialidad);

        $mensaje = "
        <div class='alert alert-success shadow-sm'>
            <h5 class='alert-heading'>¡Cita Agendada con Éxito!</h5>
            <hr>
            <p class='mb-0'>Tu cita ha sido programada para el día <strong>$fecha_formateada</strong> a las <strong>$hora_formateada</strong>.</p>
            <p>Serás atendido por el <strong>Dr. $nom_doctor</strong> ($esp_doctor).</p>
            <small class='text-muted'>Por favor, preséntate 15 minutos antes de tu hora.</small>
        </div>";
    } else {
        $mensaje = "<div class='alert alert-danger'>Hubo un error al procesar tu cita. Intenta de nuevo.</div>";
    }
}

// Obtener datos actuales del paciente
$stmt_pac = $conexion->prepare("SELECT * FROM pacientes WHERE id_paciente = ?");
$stmt_pac->bind_param("i", $id_logueado);
$stmt_pac->execute();
$p = $stmt_pac->get_result()->fetch_object();



?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Mi Perfil - Clínica San Pablo</title>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary p-3 shadow">
        <div class="container">
            <span class="navbar-brand">Portal del Paciente: <?= htmlspecialchars($p->nombre) ?></span>
            <a href="salir.php" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="container mt-4">
        <?= isset($mensaje) ? $mensaje : '' ?>
        
        <div class="row">
            <div class="col-md-5 mb-4">
                <div class="card shadow border-0">
                    <div class="card-header bg-dark text-white">Mis Datos Personales</div>
<form class="card-body" method="POST">
    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nuevo_nombre" class="form-control" value="<?= htmlspecialchars($p->nombre) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Apellido</label>
        <input type="text" name="nuevo_apellido" class="form-control" value="<?= htmlspecialchars($p->apellido) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Dirección de Residencia</label>
        <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($p->direccion) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Teléfono de Contacto</label>
        <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($p->telefono) ?>">
    </div>
    <button type="submit" name="btn_actualizar" value="ok" class="btn btn-dark w-100">Actualizar mis datos</button>
</form>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white">Solicitar Nueva Cita Médica</div>
                    <form class="card-body" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Seleccione al Especialista</label>
                            <select name="doctor" class="form-select" required>
                                <?php
                                $docs = $conexion->query("SELECT id_doctor, nombre, especialidad FROM doctores");
                                while($d = $docs->fetch_object()){
                                    $n = htmlspecialchars($d->nombre);
                                    $e = htmlspecialchars($d->especialidad);
                                    echo "<option value='$d->id_doctor'>Dr. $n - $e</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Fecha Preferida</label>
                                <input type="date" name="fecha" class="form-control" required min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col">
                                <label class="form-label">Hora</label>
                                <input type="time" name="hora" class="form-control" required min="07:00" max="18:00">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Motivo de la consulta / Síntomas</label>
                            <textarea name="observaciones" class="form-control" rows="3" placeholder="Breve descripción..."></textarea>
                        </div>
                        <button type="submit" name="btn_cita" value="ok" class="btn btn-primary w-100">Confirmar Cita</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>