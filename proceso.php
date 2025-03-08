<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Obtener los valores de los campos
$recaptchaSecret = '6LdUViwqAAAAAOMuUzyxD_2f9bVDBqE6g-sBLsJq'; // Reemplaza con tu clave secreta de reCAPTCHA
$recaptchaResponse = $_POST['g-recaptcha-response']; // Captura el token enviado por el reCAPTCHA

// Verificación del reCAPTCHA con Google
$verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
$response = file_get_contents($verifyUrl . '?secret=' . $recaptchaSecret . '&response=' . $recaptchaResponse);
$responseKeys = json_decode($response, true);

if (intval($responseKeys['success']) !== 1) {
    // El reCAPTCHA no ha sido validado correctamente
    echo '<script>alert("Debe ingresar el captcha");
    window.location="https://www.clickcorredores.cl/evaluacion-comercial-arrendatario.php?msg=5";
    </script>';
    exit;
}else{



$primerNombre = htmlentities($_POST['primer-nombre1']);
$segundoNombre = htmlentities($_POST['segundo-nombre1']);
$apellidoPaterno = htmlentities($_POST['apellido-paterno1']);
$apellidoMaterno = htmlentities($_POST['apellido-materno1']);
$rut = htmlentities($_POST['rut1']);
$nacionalidad = htmlentities($_POST['nacionalidad1']);
$estadoCivil = htmlentities($_POST['estado-civil1']);
$profesionOficio = htmlentities($_POST['profesion-oficio1']);
$empresa = htmlentities($_POST['empresa1']);
$antiguedadLaboral = htmlentities($_POST['antiguedad-laboral1']);
$banco = htmlentities($_POST['banco1']);
$cuentaCorriente = htmlentities($_POST['cuenta-corriente1']);
$rentaLiquida = htmlentities($_POST['renta-liquida1']);
$direccionParticular = htmlentities($_POST['direccion-particular1']);
$direccionLaboral = htmlentities($_POST['direccion-laboral1']);
$fonoParticular = htmlentities($_POST['fono-particular1']);
$fonoCelular = htmlentities($_POST['fono-celular1']);
$email = htmlentities($_POST['email1']);
$propiedadPostulacion = htmlentities($_POST['propiedad-postulacion1']);

// Crear instancia de PHPMailer
$mail = new PHPMailer;

// Configurar el servidor SMTP y las credenciales de correo
$mail->isSMTP();
$mail->Host = 'mail.clickcorredores.cl';
$mail->SMTPAuth = true;
$mail->SMTPSecure = 'ssl';
 
$mail->Port = 465;
$mail->Username = 'contacto@clickcorredores.cl';
$mail->Password = 'Prueba1234_$';

$mail->SMTPDebug=0;	 
 

// Establecer remitente y destinatario
$mail->setFrom('contacto@clickcorredores.cl', $primerNombre);
 $mail->addAddress('mabenite@gmail.com');
 //$mail->addAddress('luisalbchile21@gmail.com');
 
// Configurar el contenido del correo
$mail->isHTML(true);
$mail->Subject = 'Formulario Arrendatario';
$mail->Body = "
    <h2>Formulario Arrendatario :</h2>
    <ul>
        <li>Primer nombre: $primerNombre</li>
        <li>Segundo nombre: $segundoNombre</li>
        <li>Apellido paterno: $apellidoPaterno</li>
        <li>Apellido materno: $apellidoMaterno</li>
        <li>RUT: $rut</li>
        <li>Nacionalidad: $nacionalidad</li>
        <li>Estado civil: $estadoCivil</li>
        <li>Profesión / Oficio: $profesionOficio</li>
        <li>Empresa: $empresa</li>
        <li>Antigüedad laboral: $antiguedadLaboral</li>
        <li>Banco: $banco</li>
        <li>Cuenta corriente: $cuentaCorriente</li>
        <li>Renta líquida: $rentaLiquida</li>
        <li>Dirección particular: $direccionParticular</li>
        <li>Dirección laboral: $direccionLaboral</li>
        <li>Fono particular: $fonoParticular</li>
        <li>Fono celular: $fonoCelular</li>
        <li>Email: $email</li>
        <li>Propiedad de postulación: $propiedadPostulacion</li>
    </ul>
    <div style='padding-top:30px;'>Enviado desde el formulario de arrendatario en clickcorredores.cl</div>
";

// Enviar el correo
if ($mail->send()) {
   
   header("location:evaluacion-comercial-arrendatario.php?msg=1");
    exit;
} else {
 
    echo 'Error al enviar el correo: ' . $mail->ErrorInfo;
}
}
?>


