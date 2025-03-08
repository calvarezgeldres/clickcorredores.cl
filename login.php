<?php session_start();
ob_start();

require_once("./clases/class.coneccion.php");
$miCon=new coneccion();
$link=$miCon->conectar();

?>
<!DOCTYPE html>
<html>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<meta name="theme-color" content="#113964" />
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Corretaje de Propiedades | Inicio de Sesión</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  
  
     
    <link rel="shortcut icon" href="favicon.png"/> 
	<script src="./js/jquery.min.js"></script>
	<script>
	$(document).ready(function(){
		 $("#login").click(function(){
			 var usuario=$("#user_name").val();
			 var pass=$("#user_password").val();
			 if(usuario.length==0){
				 alert("Ingrese el nombre de usuario");
				 $("#user_name").focus();
			 }else if(pass.length==0){
				 alert("Ingrese su contraseña");
				 $("#user_password").focus();
			 }else{
				  $("#form1").submit();
			 }
			return(false);
		 });
		return(false);
	});
	
	</script>
<style>
  
  .btn-primary {
  color: #fff;
  background-color: #fb2750 !important;
  border-color: #fb2750 !important;
}
body {
  margin: 0;
  font-family: var(--bs-font-sans-serif);
  font-size: 1rem;
  font-weight: 400;
  line-height: 1.5;
  color: #1c1c1c;
  background-color: #ced4da !important;
  -webkit-text-size-adjust: 100%;
  -webkit-tap-highlight-color: transparent;
}
@media (max-width: 979px){ 
  #caja{width: 90%;}
}

 
@media (max-width: 439px){
  #caja{width: 90%;}
} 
@media (min-width: 992px){ 
  #caja{width: 33%;}
}
@media (min-width: 1200px){ 
  #caja{width: 33%;}

} 
.card {
  position: relative;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -ms-flex-direction: column;
  flex-direction: column;
  min-width: 0;
  word-wrap: break-word;
  background-color: #0058b2;
  background-clip: border-box;
  border: 1px solid rgba(0,0,0,.125);
  border-radius: .5rem !important;
}
        .card {
    border: 0;
    -webkit-box-shadow: 0 10px 15px -3px rgba(0,0,0,.41),0 4px 6px -2px rgba(0,0,0,.31);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.41),0 4px 6px -2px rgba(0, 0, 0, 0.31);
}  
 
</style>
  </head>
  <body>
  <?php
 
  if(isset($_POST["action"])){
 
	  $usuario=$_POST["user_name"];
	  $pass=$_POST["user_password"];
	  $sql1="select* from admin where nick='".$usuario."' and pass='".$pass."'";

    $q1 = mysqli_query($link, $sql1);
 
  
       

    if($usuario!="admin" && $pass!="admin21a"){
    
		   echo "<script>alert('Usuario o Contraseña incorrecta, intente nuevamente!!.');</script>";
		 
	  }else{
 
		  $_SESSION["auth"]["usuario"]=$usuario;
		 
		   header("location:panel.php");
		   exit;
	  }
  }
   
  ?>
  <div class="container">
    <div class="row">
      <div class="col-md-12" align="center" style="margin-top:60px;">
        <div align="center" style="margin-bottom:20px;"><span style="font-size:25px; color:black;"><b>Corretaje </b> de propiedades</span></div>
        
        <form action="" name="form1" id="form1" method="post">
        <div class="card" id="caja" name="caja">  
        
        <div align="center" style="margin-top:10px;margin-bottom:10px;"><a href="index.php"><img src="https://www.clickcorredores.cl/logoClick.png" style="width:70%;margin-top:15px;"    class="card-img-top" alt="Login"></a></div>
          <div class="card-body">
            
            <span class="card-title" style="color:white !important;">Ingrese su nombre de usuario y contraseña</span>
              <p class="card-text">
              <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
                <input type="text"  name="user_name" value="prueba" id="user_name" class="form-control"
                 placeholder="Su nombre de usuario" aria-label="Usuario" aria-describedby="Usuario">
              </div>  
              
              <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1"><i class="fas fa-lock"></i></span>
                <input type="password" value="prueba123" class="form-control" name="user_password" id="user_password" 
                placeholder="Su Contraseña" aria-label="Contraseña" aria-describedby="Contraseña">
              </div>  


              </p>
              <input type="hidden" name="action" value="true"/>
              <div style="margin-bottom:20px;">
              <button class="btn btn-danger btn-mb" name="login" id="login" style="width:100% !important;">
              <i class="far fa-hand-point-up"></i> Entrar</button>
              </div>
          </div>
          </div>

        </div>
</form>
        <div style="margin-top:30px;">
            <div align="center" STYLE="color:black !Important;"><b>Click Corredores</b></div>
            <div align="center" style="color:black !important;">© 2023 Todos los derechos reservados</div>        
        </div>
    </div>
  </div>

     
    <!-- jQuery 2.1.4 -->
    <script src="plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

  </body>
 </html>
	 