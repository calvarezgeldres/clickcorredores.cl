<?php
session_start();

setcookie("id","x",time()-86400*7);
				setcookie("autentifica","x",time()-86400*7);
				setcookie("nick","x",time()-86400*7);
				setcookie("sid","x",time()-86400*7);
				setcookie("iden","x",time()-86400*7);
				$_SESSION["auth"]["nick"]=array();
session_destroy();
header("location:loginAgente.php");
exit;

?>