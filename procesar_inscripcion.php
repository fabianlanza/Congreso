<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 
require 'config.php'; // base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Correo inválido.");
    }

    try {
        // Conectar a BD
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Insertar en la base de datos
        $stmt = $pdo->prepare("INSERT INTO inscritos (nombre, email) VALUES (:nombre, :email)");
        $stmt->execute(['nombre' => $nombre, 'email' => $email]);

        // Configurar PHPMailer
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'pruebacorreos22897@gmail.com'; 
        $mail->Password = 'gsfq iren czqy cyzx'; // contrasena generada
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Configurar el correo
        $mail->setFrom('pruebacorreos22897@gmail.com', 'Congreso Tech 2025');
        $mail->addAddress($email, $nombre);
        $mail->isHTML(true);
        $mail->Subject = 'Confirmacion de inscripcion - Congreso Tech 2025';
        $mail->Body = "<h3>Hola $nombre,</h3>
                      <p>Gracias por inscribirte en nuestro congreso.</p>
                      <p><strong>Detalles:</strong></p>
                      <ul>
                         <li>Fecha: 15 de marzo de 2025</li>
                         <li>Ubicación: CREDIA</li>
                         <li>Hora: 9:00 AM - 2:00 PM</li>
                      </ul>
                      <p>Nos vemos pronto!</p>";

        $mail->send();
        header("Location: index.html?success=1");
        // echo "Inscripción exitosa. Revisa tu correo.";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
