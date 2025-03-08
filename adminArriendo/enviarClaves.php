<?php
require './mailer/autoload.php'; // Ajusta la ruta según la ubicación de PHPMailer en tu proyecto
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;




require_once("./clases/class.coneccion.php");
$miCon=new coneccion();
$link=$miCon->conectar();


$idPropi=$_POST["idPropi"];

$sql="select email,nombre from mm_propietarios where idPropietario='".$idPropi."'";
$q=mysqli_query($link,$sql);
$r=mysqli_fetch_array($q);
$email=$r["email"];
$nombre=$r["nombre"];


 
// Generar una clave aleatoria de 8 caracteres
$clave = generarClaveAleatoria(8);
 


// Configurar PHPMailer
$mail = new PHPMailer(true);
 

try {
    
    // Configuraciones del servidor de correo
    $mail->isSMTP();
	$mail->SMTPAuth   = true;
	$mail->Host = 'clickcorredores.cl';
	$mail->Port = 465;
	$mail->Username = 'sistema@clickcorredores.cl';
	$mail->Password = 'Prueba1234_1234';
	$mail->SMTPSecure = 'ssl';
    $mail->SMTPDebug=0;	 

    // Configuraciones del correo electrónico
    $mail->setFrom('contacto@clickcorredores.cl', 'Click Corredores');

   
    $mail->addAddress($email, $nombre);
    
    $mail->isHTML(true);
    $mail->Subject = 'Clave generada aleatoriamente';
    $mail->Body    = 'Estimado/a '.$nombre.', <br><br>
                  Le informamos que se ha generado una nueva clave para su cuenta en nuestro sistema. A continuación, encontrará la nueva clave generada aleatoriamente: <br><br>
                  <b>Nueva Clave:</b> ' . $clave . '<br><br>
                  Enlace para acceder a su cuenta <br><br>
                  <a href="https://clickcorredores.cl/adminArriendo/login.php">Enlace de acceso a propietarios</a><br><br>
                  Con esta nueva clave, ahora puede acceder a su cuenta en nuestro portal y gestionar sus propiedades de manera fácil y segura. <br><br>
                  Por favor, tenga en cuenta que este mensaje ha sido enviado desde Click Corredores. <br><br>
                  Atentamente, <br>
                  Equipo de Click Corredores';

    // Enviar el correo electrónico
    if($mail->send()){
     
        $sql1="update mm_propietarios set contra='".$clave."' where idPropietario='".$idPropi."'";
        $q=mysqli_query($link,$sql1);
    } 
    
	echo "0";
} catch (Exception $e) {
    echo "Error al enviar el correo: {$mail->ErrorInfo}";
}

function generarClaveAleatoria($longitud) {
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $clave = '';

    for ($i = 0; $i < $longitud; $i++) {
        $clave .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }

    return $clave;
}

?>