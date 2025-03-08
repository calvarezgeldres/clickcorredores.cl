<?php
require_once("../clases/class.coneccion.php");
$miCon = new coneccion();
$link = $miCon->conectar();
mysqli_set_charset($link, 'utf8mb4');

$id = htmlentities($_POST["idCiudad"], ENT_QUOTES, 'UTF-8'); // Sanitiza la entrada
$sql = "SELECT * FROM mm_comuna WHERE idCiudad='" . $id . "'";

$query = mysqli_query($link, $sql);
$datos = []; // Inicializa el array

while ($row = mysqli_fetch_assoc($query)) {
    // No uses utf8_decode, ya que ya está en UTF-8
    $datos[$row["idComuna"]] = $row["nombre"];
}

// Codifica como JSON
echo json_encode($datos, JSON_UNESCAPED_UNICODE);
?>
