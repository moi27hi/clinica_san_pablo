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

    // Actualizamos nombre, apellido y lo demás
    $sql_upd = $conexion->query("UPDATE pacientes SET 
        nombre='$nom', 
        apellido='$ape', 
        direccion='$dir', 
        telefono='$tel' 
        WHERE id_paciente=$id_logueado");

    if ($sql_upd) {
        $mensaje = "<div class='alert alert-success'>¡Datos actualizados correctamente!</div>";
        // Refrescamos los datos para que el cambio se vea en el saludo de la página
        $res_pac = $conexion->query("SELECT * FROM pacientes WHERE id_paciente=$id_logueado");
        $p = $res_pac->fetch_object();
    }
}

// --- LÓGICA: AGENDAR CITA ---
if (!empty($_POST["btn_cita"])) {
    $doc_id = $_POST["doctor"];
    $fec = $_POST["fecha"];
    $obs = $_POST["observaciones"];

    // 1. Insertamos la cita
    $sql_cita = $conexion->query("INSERT INTO citas (id_paciente, id_doctor, fecha_cita, observaciones) VALUES ($id_logueado, $doc_id, '$fec', '$obs')");

    if ($sql_cita) {
        // 2. Consultamos el nombre del doctor para la confirmación
        $consulta_doc = $conexion->query("SELECT nombre, especialidad FROM doctores WHERE id_doctor = $doc_id");
        $datos_doc = $consulta_doc->fetch_object();
        
        // 3. Formateamos la fecha (de AAAA-MM-DD a DD/MM/AAAA)
        $fecha_formateada = date("d/m/Y", strtotime($fec));

        // 4. Creamos el mensaje personalizado
        $mensaje = "
        <div class='alert alert-success shadow-sm'>
            <h5 class='alert-heading'>¡Cita Agendada con Éxito! ✅</h5>
            <hr>
            <p class='mb-0'>Tu cita ha sido programada para el día <strong>$fecha_formateada</strong>.</p>
            <p>Serás atendido por el <strong>Dr. $datos_doc->nombre</strong> ($datos_doc->especialidad).</p>
            <small class='text-muted'>Por favor, preséntate 15 minutos antes de tu hora.</small>
        </div>";
    } else {
        $mensaje = "<div class='alert alert-danger'>Hubo un error al procesar tu cita. Intenta de nuevo.</div>";
    }
}

// Obtener datos actuales del paciente
$res_pac = $conexion->query("SELECT * FROM pacientes WHERE id_paciente=$id_logueado");
$p = $res_pac->fetch_object();



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
            <span class="navbar-brand">Portal del Paciente: <?= $p->nombre ?></span>
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
        <input type="text" name="nuevo_nombre" class="form-control" value="<?= $p->nombre ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Apellido</label>
        <input type="text" name="nuevo_apellido" class="form-control" value="<?= $p->apellido ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Dirección de Residencia</label>
        <input type="text" name="direccion" class="form-control" value="<?= $p->direccion ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Teléfono de Contacto</label>
        <input type="text" name="telefono" class="form-control" value="<?= $p->telefono ?>">
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
                                    echo "<option value='$d->id_doctor'>Dr. $d->nombre - $d->especialidad</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha Preferida</label>
                            <input type="date" name="fecha" class="form-control" required min="<?= date('Y-m-d') ?>">
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