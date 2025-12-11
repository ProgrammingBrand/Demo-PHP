<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sure - Contáctanos</title>
    <link rel="stylesheet" href="estilos.css"> </head>
<body>

    <div class="container">
        <h1>Contáctanos</h1>
        <p>¡Estamos listos para escucharte! Déjanos un mensaje y te responderemos a la brevedad.</p>

        <?php
        // 1. Manejo del formulario PHP
        $mensaje_exito = "";
        $mensaje_error = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Recoger y limpiar los datos del formulario
            $nombre = htmlspecialchars(trim($_POST['nombre']));
            $email = htmlspecialchars(trim($_POST['email']));
            $asunto = htmlspecialchars(trim($_POST['asunto']));
            $mensaje = htmlspecialchars(trim($_POST['mensaje']));

            // Validación simple
            if (!empty($nombre) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($asunto) && !empty($mensaje)) {

                // --- CONFIGURACIÓN DE ENVÍO DE CORREO ---
                $destinatario = "tucorreo@ejemplo.com"; // **CAMBIA ESTO** por tu email real
                $cabeceras = 'From: ' . $nombre . ' <' . $email . '>' . "\r\n" .
                             'Reply-To: ' . $email . "\r\n" .
                             'X-Mailer: PHP/' . phpversion();
                $contenido_correo = "Nombre: " . $nombre . "\n"
                                  . "Email: " . $email . "\n"
                                  . "Mensaje:\n" . $mensaje;
                // --- FIN CONFIGURACIÓN DE ENVÍO ---

                if (mail($destinatario, $asunto, $contenido_correo, $cabeceras)) {
                    $mensaje_exito = "✅ ¡Gracias! Tu mensaje ha sido enviado correctamente.";
                } else {
                    $mensaje_error = "❌ Lo sentimos, hubo un problema al enviar el mensaje. Inténtalo más tarde.";
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
    
    <script src="validacion.js"></script> </body>
</html>