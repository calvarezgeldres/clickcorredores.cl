<?php
require_once("../clases/class.coneccion.php");
$miCon=new coneccion();
$conn=$miCon->conectar();
$idCorredora = $_POST['idCorredora'];
$newPassword = $_POST['newPassword'];






// Proteger contra inyecciones SQL
$idCorredora = $idCorredora;
$newPassword =$newPassword;

 
// Actualizar la contraseña en la base de datos
$sql = "UPDATE registro SET pass = '$newPassword' WHERE idReg = '$idCorredora'";
 

if (mysqli_query($conn, $sql)) {
  echo "true";
} else {
    echo "false";
}

// Cerrar conexión
mysqli_close($conn);
 
?>
 
 