<?php session_start();
ob_start();

require_once("../clases/class.coneccion.php");
$miCon=new coneccion();
$link=$miCon->conectar();

?>
<!DOCTYPE html>
<html lang="es">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<meta name="theme-color" content="#113964" />
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login Agente | Inicio de Sesión</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/a50208ac31.js" crossorigin="anonymous"></script> 
  
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

     
  <link rel="shortcut icon" href="favicon.png"/> 
 
	<script>
	$(document).ready(function(){
    
    $("#login1").click(function(){
			 var usuario=$("#user_name1").val();
			 var pass=$("#user_password1").val();
			 if(usuario.length==0){
				 alert("Ingrese el nombre de usuario");
				 $("#user_name1").focus();
			 }else if(pass.length==0){
				 alert("Ingrese su contraseña");
				 $("#user_password1").focus();
			 }else{ 
      
        // Mostrar la barra de progreso antes de la solicitud AJAX
    $(".progress-bar").width("0%");
    $(".progress").show();

    $.ajax({
      type: "post",
      url: "./include/enter.php",
      data: "nick=" + usuario + "&pass=" + pass + "&tipo=1",
      beforeSend: function() {
        // Configurar la barra de progreso
        $(".progress-bar").width("0%");
      },
      xhr: function() {
        var xhr = new XMLHttpRequest();
        xhr.upload.addEventListener("progress", function(evt) {
          if (evt.lengthComputable) {
            var percentComplete = (evt.loaded / evt.total) * 100;
            // Actualizar la barra de progreso
            $(".progress-bar").width(percentComplete + "%");
          }
        }, false);
        return xhr;
      },
      success: function(datos) {  
        var datosSin = datos.replace(/\s/g, '');        
        $(".progress").hide();
        if (datosSin.indexOf("ok")==0) {         
          document.location="panelAgente.php";          
        }else{
          alert("Usuario o contraseña incorrecto");          
        }
      }
    });


			 }
			return(false);
		 });

     $("#login").click(function() {
  var usuario = $("#user_name").val();
  var pass = $("#user_password").val();

  if (usuario.length == 0) {
    alert("Ingrese el nombre de usuario");
    $("#user_name").focus();
  } else if (pass.length == 0) {
    alert("Ingrese su contraseña");
    $("#user_password").focus();
  } else {
    // Mostrar la barra de progreso antes de la solicitud AJAX
    $(".progress-bar").width("0%");
    $(".progress").show();

    $.ajax({
      type: "post",
      url: "./include/enter.php",
      data: "nick=" + usuario + "&pass=" + pass + "&tipo=2",
      beforeSend: function() {
        // Configurar la barra de progreso
        $(".progress-bar").width("0%");
      },
      xhr: function() {
        var xhr = new XMLHttpRequest();
        xhr.upload.addEventListener("progress", function(evt) {
          if (evt.lengthComputable) {
            var percentComplete = (evt.loaded / evt.total) * 100;
            // Actualizar la barra de progreso
            $(".progress-bar").width(percentComplete + "%");
          }
        }, false);
        return xhr;
      },
      success: function(datos) {      
        var datosSin = datos.replace(/\s/g, '');
        
        $(".progress").hide();
        if (datosSin.indexOf("ok")==0) {              
          document.location="panelAgente.php";          
        }else{
          alert("El nombre de Usuario o contraseña de corredor no existe, intenta nuevamente");          
        }
      }
    });
  }

  return false;
});


		
		return(false);
	});
	
	</script>
<style>
  .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
    color: #495057 !important;
    background-color: #dcdce3 !important;
    border-color: #dee2e6 #dee2e6 #dcdce3 !important;
}
  .nav-tabs .nav-link {
    margin-bottom: -1px;
    background: 0 0;
    border: 1px solid transparent;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
    background-color: white !important;
}
  .nav-tabs {
    border-bottom: 1px solid #dee2e6;
    width: 35% !important;
}
 .tab-content>.active {
    display: block;
    background-color: #dcdce3;
    width: 35% !important;
}
 body {
            background-image: url('https://adm.controlpropiedades.cl/assets/landing-background-2-546dd7cab2063075b9d0f6c8520241fe87cfa879b2ac660737b2cb548e3f5e1c.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center center;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
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
 
</style>
  </head>
  <body>
    <div style="background-color:red;">
  
  </div>
  <div class="container">
    <div class="row">
      <div class="col-md-12" align="center" style="margin-top:60px;">
        <div align="center" style="margin-bottom:20px;"><span style="font-size:25px; color:black;"><b>Inicio Sesión </b> <br>Agente Inmobiliario </span></div>
        
       



        <ul class="nav nav-tabs" id="myTab" role="tablist">
 
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Agente Inmobiliario</button>
  </li>
 
</ul>
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
 
  
  <div  >  
        
  <form action="" name="form11" id="form11" method="post">
        <div class="card-body">
          <div style="margin-top:20px;"><h4>Ingreso Agente</h4></div>
          
          <span class="card-title" style="color:black !important;">Ingrese su nombre de usuario y contraseña</span>
            <p class="card-text">
            <div class="input-group mb-3">
              <span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
              <input type="text"  name="user_name1" value="prueba" id="user_name1" class="form-control"
               placeholder="Su nombre de usuario" aria-label="Usuario" aria-describedby="Usuario">
            </div>  
            
            <div class="input-group mb-3">
              <span class="input-group-text" id="basic-addon1"><i class="fas fa-lock"></i></span>
              <input type="password" value="prueba123" class="form-control" name="user_password1" id="user_password1" 
              placeholder="Su Contraseña" aria-label="Contraseña" aria-describedby="Contraseña">
            </div>  


            </p>
            <input type="hidden" name="action" value="true"/>

            <div align="left">
              <input type="checkbox" name="recordarme" id="recordarme" value="true" /> Recordarme
            </div>
            <div align="left" style="margin-bottom:20px;">
              <a href="#">Recuperar Contraseña</a>
            </div>


            <div style="margin-bottom:20px;">
            <button class="btn btn-primary btn-mb" name="login1" id="login1" style="width:100% !important;">
            <i class="far fa-hand-point-up"></i> Entrar</button>
          
            </div>
            <div class="progress">
              <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          
        </div>

      </div> 
</form>

  
  </div>
  <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
   
  


  <div  >  
        
  <form action="" name="form1" id="form1" method="post">
        <div class="card-body">
          <div style="margin-top:20px;"><h4>Ingreso Corredor</h4></div>
          
          <span class="card-title" style="color:black !important;">Ingrese su nombre de usuario y contraseña</span>
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

            <div align="left">
              <input type="checkbox" name="recordarme" id="recordarme" value="true" /> Recordarme
            </div>
            <div align="left" style="margin-bottom:20px;">
              <a href="#">Recuperar Contraseña</a>
            </div>


            <div style="margin-bottom:20px;">
            <button class="btn btn-primary btn-mb" name="login" id="login" style="width:100% !important;">
            <i class="far fa-hand-point-up"></i> Entrar</button>
         
            </div>
            <div class="progress">
              <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
      
        </div>

      </div> 
      </form>



  </div>  
</div>




       

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
	 