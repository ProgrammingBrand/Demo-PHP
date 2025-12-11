<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sure - Contáctanos</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <div class="container">
        <h1>Contáctanos</h1>
        <p>¡Estamos listos para escucharte! Déjanos un mensaje y te responderemos a la brevedad.</p>

        <?php
        // --- 1. CONFIGURACIÓN DE LA BASE DE DATOS (POSTGRESQL) ---
        $host = 'computo-nube.postgres.database.azure.com'; // O la IP de tu servidor de BD
        $dbname = 'postgres'; // **CAMBIA ESTO** por el nombre real de tu BD
        $user = 'azure_root'; // **CAMBIA ESTO**
        $password = 'Az-Db#Postg47_25'; // **CAMBIA ESTO**
        $port = '5432'; // Puerto por defecto de PostgreSQL

        $pdo = null;
        $mensaje_exito = "";
        $mensaje_error = "";

        // Intentar la conexión
        try {
            $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
            $pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        } catch (PDOException $e) {
            // Si la conexión falla, solo lo guardamos en el log (no mostramos error al usuario por seguridad)
            error_log("Error de conexión a PostgreSQL: " . $e->getMessage());
            $mensaje_error_bd = "No se pudo conectar a la base de datos.";
        }
        // --- FIN CONFIGURACIÓN BD ---

        // 2. Manejo del formulario PHP
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Recoger y limpiar los datos del formulario
            $nombre = htmlspecialchars(trim($_POST['nombre']));
            $email = htmlspecialchars(trim($_POST['email']));
            $asunto = htmlspecialchars(trim($_POST['asunto']));
            $mensaje = htmlspecialchars(trim($_POST['mensaje']));

            // Validación simple
            if (!empty($nombre) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($asunto) && !empty($mensaje)) {
                
                $todo_ok = true;

                // A) GUARDAR EN LA BASE DE DATOS
                if ($pdo) { // Solo intentamos si la conexión fue exitosa
                    try {
                        // Usamos consultas preparadas para prevenir ataques de inyección SQL
                        $sql = "INSERT INTO contactos (nombre, email, asunto, mensaje, fecha_envio) VALUES (:nombre, :email, :asunto, :mensaje, NOW())";
                        $stmt = $pdo->prepare($sql);
                        
                        $stmt->execute([
                            ':nombre' => $nombre,
                            ':email' => $email,
                            ':asunto' => $asunto,
                            ':mensaje' => $mensaje
                        ]);
                    } catch (PDOException $e) {
                        $todo_ok = false;
                        error_log("Error al guardar en BD: " . $e->getMessage());
                        $mensaje_error = "❌ Lo sentimos, hubo un problema al guardar tu mensaje en el registro.";
                    }
                } else {
                    $todo_ok = false;
                    $mensaje_error = "❌ No se pudo conectar a la base de datos para guardar el registro.";
                }


                // B) ENVIAR CORREO (Solo si la inserción en BD fue exitosa o si no se intentó la BD)
                if ($todo_ok) { 
                    // --- CONFIGURACIÓN DE ENVÍO DE CORREO ---
                    $destinatario = "tucorreo@ejemplo.com"; // **CAMBIA ESTO**
                    $cabeceras = 'From: ' . $nombre . ' <' . $email . '>' . "\r\n" .
                                 'Reply-To: ' . $email . "\r\n" .
                                 'X-Mailer: PHP/' . phpversion();
                    $contenido_correo = "Nombre: " . $nombre . "\n"
                                      . "Email: " . $email . "\n"
                                      . "Mensaje:\n" . $mensaje;

                    if (mail($destinatario, $asunto, $contenido_correo, $cabeceras)) {
                        $mensaje_exito = "✅ ¡Gracias! Tu mensaje ha sido enviado correctamente y registrado.";
                    } else {
                        // Si falla el mail, pero la BD fue OK, sigue siendo un éxito parcial
                        $mensaje_exito = "⚠️ Tu mensaje ha sido registrado, pero falló el envío del correo de notificación. Revisa la base de datos.";
                    }
                }
            } else {
                $mensaje_error = "❌ Por favor, completa todos los campos correctamente, especialmente el email.";
            }
        }
        ?>

        <?php if (!empty($mensaje_exito)): ?>
            <div class="alerta exito"><?php echo $mensaje_exito; ?></div>
        <?php elseif (!empty($mensaje_error)): ?>
            <div class="alerta error"><?php echo $mensaje_error; ?></div>
        <?php endif; ?>

        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="contactForm">
            <div class="form-group">
                <label for="nombre">Nombre Completo:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="asunto">Asunto:</label>
                <input type="text" id="asunto" name="asunto" required>
            </div>

            <div class="form-group">
                <label for="mensaje">Mensaje:</label>
                <textarea id="mensaje" name="mensaje" rows="6" required></textarea>
            </div>

            <button type="submit">Enviar Mensaje</button>
        </form>
    </div>
    
    <script src="validacion.js"></script>
</body>
</html>