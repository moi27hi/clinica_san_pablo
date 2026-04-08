<?php
session_start();
include("conexion.php");

// --- 1. LÓGICA PARA INICIAR SESIÓN ---
if (!empty($_POST["btnentrar"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];
    
    $sql = $conexion->query("SELECT * FROM login WHERE email='$email' AND contraseña='$password'");
    
    if ($datos = $sql->fetch_object()) {
        $_SESSION["id"] = $datos->id_usuario;
        $_SESSION["email"] = $datos->email;
        $_SESSION["rol"] = $datos->rol;

        if ($datos->rol == 'admin') {
            header("location: dashboard.php");
        } else {
            header("location: perfil_paciente.php");
        }
        exit();
    } else {
        echo "<div class='alert alert-danger text-center mb-0'>Acceso denegado: Usuario o contraseña incorrectos</div>";
    }
}

// --- 2. LÓGICA PARA REGISTRO COMPLETO DE PACIENTES ---
if (!empty($_POST["btnregistrar_paciente"])) {
    $nom = $_POST["nom"];
    $ape = $_POST["ape"];
    $dir = $_POST["dir"];
    $tel = $_POST["tel"];
    $email = $_POST["email"];
    $pass = $_POST["pass"];

    // Primero insertamos en la tabla de acceso (login)
    $sql_login = $conexion->query("INSERT INTO login (email, contraseña, rol) VALUES ('$email', '$pass', 'paciente')");

    if ($sql_login) {
        // Capturamos el ID que se acaba de generar para este usuario
        $id_generado = $conexion->insert_id;

        // Insertamos en la tabla pacientes usando ese mismo ID
        $sql_paciente = $conexion->query("INSERT INTO pacientes (id_paciente, nombre, apellido, direccion, telefono) 
                                          VALUES ($id_generado, '$nom', '$ape', '$dir', '$tel')");

        if ($sql_paciente) {
            echo "<div class='alert alert-success text-center mb-0'>¡Registro exitoso! Bienvenido/a $nom, ya puedes iniciar sesión.</div>";
        } else {
            echo "<div class='alert alert-danger mb-0'>Error al crear perfil: " . $conexion->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger mb-0'>Error al crear usuario: El correo podría estar ya registrado.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Login - Clínica San Pablo</title>
    <style>
        body { background: #f8f9fa; height: 100vh; display: flex; align-items: center; }
        .login-card { border: none; border-radius: 15px; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow login-card p-4">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Iniciar Sesión</h2>
                    <p class="text-muted">Clínica San Pablo</p>
                </div>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" name="btnentrar" value="ok" class="btn btn-primary w-100 py-2 fw-bold">Entrar</button>
                </form>
                <div class="text-center mt-4">
                    <hr>
                    <button type="button" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#modalRegistro">
                        Crear cuenta nueva (Pacientes)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRegistro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Registro de Nuevo Paciente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Apellido</label>
                            <input type="text" name="ape" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="dir" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="tel" class="form-control" required>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico (Será su Usuario)</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="pass" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" name="btnregistrar_paciente" value="ok" class="btn btn-success px-4">Registrarme</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>