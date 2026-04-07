<?php
session_start();
include("conexion.php");

if (!empty($_POST["btnentrar"])) {
    if (!empty($_POST["email"]) && !empty($_POST["password"])) {
        $email = $_POST["email"];
        $password = $_POST["password"];
        
        // Buscamos en tu tabla 'login'
        $sql = $conexion->query("SELECT * FROM login WHERE email='$email' AND contraseña='$password'");
        
        if ($datos = $sql->fetch_object()) {
            $_SESSION["id"] = $datos->id_usuario;
            $_SESSION["email"] = $datos->email;
            header("location: dashboard.php");
        } else {
            echo "<div class='alert alert-danger'>Acceso denegado</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Login - Clínica San Pablo</title>
</head>
<body class="bg-light d-flex align-items-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4 card p-4 shadow">
                <h3 class="text-center">Iniciar Sesión</h3>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" name="btnentrar" value="ok" class="btn btn-primary w-100">Entrar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>