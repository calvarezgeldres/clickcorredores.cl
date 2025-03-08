<?php
require_once("./clases/class.coneccion.php");
$miCon=new coneccion();
$id=htmlentities($_POST["idReg"]);
$tabla=htmlentities($_POST["tabla"]);
$campoIndice=htmlentities($_POST["campo"]);
$sql="delete from ".$tabla." where ".$campoIndice."='".$id."'";
$q=mysql_query($sql);


?>