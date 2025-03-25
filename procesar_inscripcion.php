<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 
require 'config.php'; // base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);

    // validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Correo inválido.");
    }
    //validar telefonos
    if (!preg_match("/^\d{4}-?\d{4}$/", $telefono)) {
        die("Número de teléfono inválido.");
    }

    try {
        // Conectar a BD
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Insertar en la base de datos
        $stmt = $pdo->prepare("INSERT INTO inscritos (nombre, apellido, email, telefono) VALUES (:nombre, :apellido, :email, :telefono)");
        $stmt->execute(['nombre' => $nombre, 'apellido' => $apellido, 'email' => $email, 'telefono' => $telefono]);

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
        $mail->setFrom('pruebacorreos22897@gmail.com', 'Congreso ICC 2025');
        $mail->addAddress($email, $nombre);
        $mail->isHTML(true);
        $mail->Subject = 'Confirmacion de inscripcion - Congreso ICC 2025';
        $mail->Body = "    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; background-color: #f9f9f9; text-align: center;'>
        <h2 style='color: #004aad;'>✅ Inscripción Confirmada</h2>
        <p>Hola <strong>$nombre</strong>,</p>
        <p>Gracias por inscribirte en nuestro <strong>Congreso ICC 2025</strong>.</p>
        <p>Nos pondremos en contacto contigo pronto con más información.</p>
        <p style='margin-top: 20px; font-weight: bold;'>¡Gracias por tu interés!</p>
    </div>
";

        $mail->send();

        // Redirigir a la página con un mensaje de éxito para SweetAlert
        header("Location: index.html?success=1");
        // echo "Inscripción exitosa. Revisa tu correo.";
    } catch (Exception $e) {
        // echo "Error: " . $e->getMessage();
        error_log("Error en inscripcion: " . $e->getMessage());
        header("Location: index.html?error=1");
    }
}
?>
