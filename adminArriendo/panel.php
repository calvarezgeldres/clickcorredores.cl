<?php 
session_start();
if(!isset($_SESSION["auth"]["usuario"])){  
  header("location:login.php");
  exit;
}
if($_SESSION["auth"]["tipo"]!="admin"){
  session_destroy();
  header("location:login.php");
exit;
}
require_once("./clases/class.controlPropi.php");
$controlProp=new controlProp();
if(isset($_GET["op"])){
  $op=htmlentities($_GET["op"]);
  if($op==1 || $op==5 || $op==9 || $op==10){
    require_once("./clases/class.publicar.php");
    $publicar=new publicar();
  }
}else{
  require_once("./clases/class.publicar.php");
  $publicar=new publicar();
}

?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administración de arriendos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

 
    <style>
      
      .dropdown-item {
        font-size: 14px !important;
      }
      tbody, td, tfoot, th, thead, tr {    
    font-size: 14px !important;
}
      .breadcrumb {
        margin-top:10px;
  background-color: #f8f9fa; /* Color de fondo */
  padding: 5px 5px 10px 10px; /* Espaciado interior */
 
 
}

.breadcrumb .breadcrumb-item a {
  text-decoration: none; /* Quita el subrayado de los enlaces */
  color: #007bff; /* Color del enlace */
 
  font-size:14px !important;
}

.breadcrumb .breadcrumb-item.active {
  color: #555; /* Color del elemento activo */
  font-size:14px !important;
  padding-top:3px !important;
}

      .navbar-expand-lg .navbar-nav .dropdown-menu {
    position: absolute;
    font-size: 14px !important;
}
    .nav-tabs {
        font-size: 14px !important;
    }
    </style>
  </head>
  <body>
  <nav class="navbar navbar-expand-lg bg-light">
  <div class="container">
    <a class="navbar-brand" href="#">AdminProp</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="panel.php">Inicio</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
           Propiedades
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="panel.php?op=1">Listado de Propiedades</a></li>            
            <li><a class="dropdown-item" href="panel.php?op=5">Agregar Propiedad</a></li>      
          </ul>
        </li>
     

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Arriendos
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="panel.php?op=2">Listado de Arriendos</a></li>  
            <li><a class="dropdown-item" href="panel.php?op=16">Ingresar Nuevo Arriendo</a></li>  
            
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Personas
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="panel.php?op=3">Propietarios</a></li>
            <li><a class="dropdown-item" href="panel.php?op=12">Agregar Propitario</a></li>      
            <li><a class="dropdown-item" href="panel.php?op=4">Arrendatarios</a></li>            
            <li><a class="dropdown-item" href="panel.php?op=6">Agregar Arrendatarios</a></li>     
            <li><a class="dropdown-item" href="panel.php?op=18">Codeudores</a></li>            
            <li><a class="dropdown-item" href="panel.php?op=17">Agregar Codeudor</a></li>            
            
          </ul>
        </li>
        
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Acciones
          </a>
          <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="panel.php?op=24">Liquidación pago a propietario</a></li>            
          <li><a class="dropdown-item" href="panel.php?op=25">Liquidación pago a arrendatario</a></li>            
                       
          </ul>
        </li>
  

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Accesos
          </a>
          <ul class="dropdown-menu">
          
          <li><a class="dropdown-item" href="https://calculadoraipc.ine.cl/" target="_blank">Calculadora IPC</a></li>            
          <li><a class="dropdown-item" href="https://servicios.cmfchile.cl/simuladorhipotecario/aplicacion?indice=101.2.1" target="_blank">Simulador de Credito Hipotecario</a></li>            
          <li><a class="dropdown-item" href="https://www.clickcorredores.cl" target="_blank">Click Corredores</a></li>            
          <li><a class="dropdown-item" href="https://www.gmail.com" target="_blank">Gmail</a></li>            
            <li><a class="dropdown-item" href="https://www.google.com/maps/place/Santiago,+Regi%C3%B3n+Metropolitana/@-33.4718999,-70.9100253,10z/data=!3m1!4b1!4m5!3m4!1s0x9662c5410425af2f:0x8475d53c400f0931!8m2!3d-33.4488897!4d-70.6692655?hl=es-CL" target="_blank">GoogleMap</a></li>            
            <li><a class="dropdown-item" href="https://homer.sii.cl/" target="_blank">SII</a></li>            
            <li><a class="dropdown-item" href="https://www.google.com/search?client=firefox-b-d&q=calculadora+online" target="_blank">Calculadora OnLine</a></li>            
            
          </ul>
        </li>
  

        
        <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-bell"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
        <?php $controlProp->contarSoli();?>
            <span class="visually-hidden">unread messages</span>
        </span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="panel.php?op=50&act=solicitudes">Solicitud de Arrendatarios&nbsp;<span class="badge text-bg-danger rounded-pill"><?php $controlProp->contarSoli();?></span></a></li>
        
         
    </ul>
</li>


        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fas fa-home"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="panel.php?op=5">Agregar Propiedad</a></li>
            <li><a class="dropdown-item" href="panel.php?op=6">Agregar Arrendatario</a></li>
            <li><a class="dropdown-item" href="panel.php?op=16">Agregar Nuevo Arriendo</a></li>
            
          </ul>
        </li>

        <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" style="font-size: 14px !important;" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
    <?php 
        $foto=$_SESSION["auth"]["foto"];
        echo '<img src="'.$foto.'" class="rounded-circle" style="width: 30px; height: 30px;"/>&nbsp; Administrador';
    ?>
</a>

          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">
              <?php 
              echo $_SESSION["auth"]["correo"];
              ?>
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="panel.php?op=7">Agentes Inmobiliarios</a></li>
            <li><a class="dropdown-item" href="panel.php?op=8">Configuraciones</a></li>
            <li><a class="dropdown-item" href="panel.php?op=9">Perfil</a></li>
            <li><a class="dropdown-item" href="panel.php?op=10">Seguridad y Contraseña</a></li>
            <li><a class="dropdown-item" href="panel.php?op=11">Acerca de</a></li>
            <li><a class="dropdown-item" href="desconectar.php">Cerrar Sesión</a></li>
          </ul>
        </li>

        
      </ul>
    
</div>

    </div>

  </div>
  
</nav>



<div class="container">


<?php 

if(!isset($_GET["op"])){
?>
  <div class="row">
    <div class="col-md-12">
      <div style="margin-top:10px;">
      <?php 
      $controlProp->indicador();
      ?>
      </div>
     
    </div>
    
  </div>
  <?php 
}else{
  $controlProp->breadcrumb();
}
?>
  <div class="row" style="margin-top:20px;">
    <div class="col-md-12">
    <div  >
    <?php 
      if(isset($_GET["op"])){
        $op=htmlentities($_GET["op"]);
        if($op==1){
            $publicar->tablaPropiedades();
        }else if($op==2){
          $controlProp->tabla_mm_arriendos();     
        }else if($op==3){
          $controlProp->tabla_mm_propietarios();
        }else if($op==4){
          $controlProp->tabla_mm_arrendatario();
        }else if($op==5){         
          $publicar->ingresarPropiedad();
        }else if($op==6){
          $controlProp->ingresar_mm_arrendatario();
        }else if($op==7){
          echo "Agregar lista de usuarios";
        }else if($op==8){
          $controlProp->modificar_mm_configuracion();
        }else if($op==9){
          $controlProp->editarCuentaAdmin();
        }else if($op==10){
          $controlProp->modificarPass();
        }else if($op==11){
          $controlProp->acercaDe();
        }else if($op==12){
          $controlProp->ingresar_mm_propietarios();
        }else if($op==13){          
          if(isset($_GET["act"])){
            $controlProp-> modificar_mm_cuentaBancaria();
          }else{
            $controlProp-> ingresar_mm_cuentaBancaria();
          }          
        }else if($op==14){
          $controlProp->fichaPropietario();
        }else if($op==15){
          $controlProp->fichaPropiedad();
        }else if($op==16){
          $controlProp->ingresar_mm_arriendos();
        }else if($op==17){
          $controlProp->ingresar_mm_codeudor();
        }else if($op==18){
          $controlProp->tabla_mm_codeudor();
        }else if($op==19){
          $controlProp->fichaCodeudor();
        }else if($op==20){
          $controlProp->fichaArrendatario();
        }else if($op==21){
          // propietario
          if(isset($_GET["act"]) && $_GET["act"]=="edit"){
            if(isset($_GET["idPropi"])){
              $idPropi=htmlentities($_GET["idPropi"]);
              $controlProp->modificar_mm_cuentaBancaria($idPropi);
            }            
          }else{
            $controlProp->ingresar_mm_cuentaBancaria(0);
          }
        
          
        }else if($op==22){
          // propietario
          $controlProp->ingresarGastos();
        }else if($op==255){
             // arriendo
           // $controlProp-> modificar_mm_cuentaBancaria();

           if(isset($_GET["act"]) && $_GET["act"]==1){
            $controlProp-> ingresar_mm_cuentaBancaria(1);    
           }else{
            $controlProp-> modificar_mm_cuentaBancaria(0);    
           }        
        
          }else if($op==256){
            // codeudor
          if(isset($_GET["act"]) && $_GET["act"]=="edit"){
            $controlProp-> modificar_mm_cuentaBancaria(0);    
          }else{
            $controlProp-> ingresar_mm_cuentaBancaria(2);               
          }        
        }else if($op==24){
          echo "Modulo en proceso de instalación";
        }else if($op==25){
          echo "Modulo en proceso de instalación";
        }else if($op==26){
          $controlProp->fichaArriendo();
        }else if($op==50){
          $controlProp->bandejaEntrada();
          
        }else if($op==23){
          // propietario
          $controlProp->generarLiquidacion();
       
        }else{
          $publicar->tablaPropiedades();
        }
      
      
          
      }else{
        $controlProp->clasificaArriendos();
      }
      
      ?>

    </div>
    </div></div>

    <section id="piePagina" name="piePagina">
      <div class="row">
        <div class="col-md-12">
          <div style="padding-bottom:15px;">
            <div><hr/></div>
            <div align="center">Administración de propiedades adminProp versión 1.0 - <a href="https://www.programacionwebchile.cl" style="font-size:14px;" target="_blank">Programación Web Chile</a></div>
          </div>
        </div>
      </div>
    </section>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
      $("#duracionContrato").change(function() {
          
          var duracionContrato = parseInt($(this).val());
          var fechaInicio = new Date(); // Fecha actual
          
          // Agregar la duración del contrato a la fecha de inicio
          if (duracionContrato === 1) {
              fechaInicio.setMonth(fechaInicio.getMonth() + 1);
          } else if (duracionContrato === 2) {
              fechaInicio.setMonth(fechaInicio.getMonth() + 3);
          } else if (duracionContrato === 3) {
              fechaInicio.setMonth(fechaInicio.getMonth() + 6);
          } else if (duracionContrato === 4) {
              fechaInicio.setFullYear(fechaInicio.getFullYear() + 1);
          }
          
          // Formatear la fecha de cancelación (opcional)
          var dia = fechaInicio.getDate();
          var mes = fechaInicio.getMonth() + 1;
          var año = fechaInicio.getFullYear();
          // Formatear la fecha de cancelación en formato día-mes-año (DD-MM-YYYY)
// Formatear la fecha de cancelación en formato 'YYYY-MM-DD'
var fechaCancelacionFormatted = año + '-' + mes.toString().padStart(2, '0') + '-' + dia.toString().padStart(2, '0');

// Establecer la fecha de cancelación en el campo correspondiente
$("#fechaCancelacion").val(fechaCancelacionFormatted);
      });

      var today = new Date();
    var year = today.getFullYear();
    var month = (today.getMonth() + 1).toString().padStart(2, '0'); // Añade un 0 al principio si el mes es de un solo dígito
    var day = today.getDate().toString().padStart(2, '0'); // Añade un 0 al principio si el día es de un solo dígito

    // Formatear la fecha como 'YYYY-MM-DD'
    var fechaActual = year + '-' + month + '-' + day;

    // Asignar la fecha actual al campo de entrada
    $('#fecha').val(fechaActual);
    $('#fechaInicio').val(fechaActual);
        // Asociar la función directamente al evento de cambio en el select de duración del contrato
     
    });
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 
    <script>
    // Inicializa los datepickers
    $('#fechaInicio, #fechaCancelacion').datepicker({
      format: 'mm/dd/yyyy', // Establece el formato de la fecha
      autoclose: true, // Cierra automáticamente después de seleccionar una fecha
      language: 'es' // Establece el idioma español
    });
  </script>

<script>

 

  $(document).ready(function() {
 

    $(document).on('click', '#enviarClave', function() {
      
    var $button = $(this);
    var idPropietario = $button.attr('alt');
    
    $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...').prop('disabled', true);
    
    $.ajax({
        url: 'enviarClaves.php',
        type: 'POST',
        data: {idPropi: idPropietario},
        success: function(response) {
          
            // Procesar la respuesta
            if (response.trim()==0) {
                alert('Las claves se han enviado con éxito.');
            } else {
                alert('Ha ocurrido un error al enviar las claves.');
            }
            
            $button.html('<i class="fas fa-user"></i> Enviar Claves').prop('disabled', false);
        },
        error: function(xhr, status, error) {
            // Manejar errores
            alert('Ha ocurrido un error al enviar las claves.');
            $button.html('<i class="fas fa-user"></i> Enviar Claves').prop('disabled', false);
        }
    });
});
    $("#reajuste").on("change", function() {
    // Obtener el valor seleccionado
    var valorReajuste = $(this).val();

    // Verificar si el valor es 3 o 4
    if (valorReajuste == 3 || valorReajuste == 4) {
        // Activar el campo de texto
        $("#porcentaje").prop("disabled", false);
    } else {
        // Desactivar el campo de texto
        $("#porcentaje").prop("disabled", true);
    }
});

$("#comisionPropietario").on("change", function() {
    // Obtener el valor seleccionado
    var valorComisionPropietario = $(this).val();

    // Desactivar todos los campos por defecto
    $("#valorComisionPropietario").prop("disabled", true);
    $("#monedaComisionPropietario").prop("disabled", true);

    // Verificar el valor de comisionPropietario
    if (valorComisionPropietario == 1) {
        // Activar los campos de texto si comisionPropietario es 1
        $("#valorComisionPropietario").prop("disabled", false);
        $("#monedaComisionPropietario").prop("disabled", false);
    }
});

$("#cobrarComisionAdmin").on("change", function() {
    // Obtener el valor seleccionado
    var valorCobrarComisionAdmin = $(this).val();

    // Desactivar todos los campos por defecto
    $("#valorComisionAdmin").prop("disabled", true);
    $("#monedaComisionAdmin").prop("disabled", true);

    // Verificar el valor de cobrarComisionAdmin
    if (valorCobrarComisionAdmin == 1) {
        // Activar los campos de texto si cobrarComisionAdmin es 1
        $("#valorComisionAdmin").prop("disabled", false);
        $("#monedaComisionAdmin").prop("disabled", false);
    }
});
$("#comisionArriendo").on("change", function() {
    // Obtener el valor seleccionado
    var valorComisionArriendo = $(this).val();

    // Desactivar todos los campos por defecto
    $("#valorComision").prop("disabled", true);
    $("#monedaComision").prop("disabled", true);

    // Verificar el valor de comisionArriendo
    if (valorComisionArriendo == 1) {
        // Activar los campos de texto si comisionArriendo es 1
        $("#valorComision").prop("disabled", false);
        $("#monedaComision").prop("disabled", false);
    }
});
     $("#region").change(function(){
      
    var region=$("#region").val();
      $("#ciudad").empty();
   
   $.ajax({
        type: "POST",
        url: "../include/proceso3.php",
        data: "idRegion="+region, 	
        dataType: "json",
        success: function(datos){	
 
           $('#ciudad').prop('disabled', false);				
         $('#ciudad').empty();
          $('#comuna').prop('disabled', false);				
         $('#comuna').empty();
         $("#ciudad").append("<option value='0' selected='selected'>Selecione ciudad</option>");       
                                   
                                 
         var res=datos;
         for(k in res){
            for (i in res[k]){
              $("#ciudad").append("<option value="+i+">"+res[k][i]+"</option>");                    
            }
          }
        }
    });
  });
 $("#ciudad").change(function(){
   
   var ciudad=$("#ciudad").val();
    $("#comuna").empty();
    
    $.ajax({
     type: "POST",
     url: "./include/proceso3a.php",
     data: "idCiudad="+ciudad, 	
     dataType: "json",
     success: function(datos){	
        
        $('#comuna').prop('disabled', false);				
       $('#comuna').empty();
       $("#comuna").append("<option value='0' selected='selected'>Seleccione Comuna</option>");  
       var res=datos;
               
       for(k in res){
         for (i in res[k]){
             $("#comuna").append("<option value="+i+">"+res[k][i]+"</option>");                    
         }
        }
     }
   });
  });
  




  $("#region5x").change(function(){
   
   var region=$("#region5x").val();
     $("#ciudad").empty();
  
  $.ajax({
       type: "POST",
       url: "./include/proceso3.php",
       data: "idRegion="+region, 	
       dataType: "json",
       success: function(datos){	
   
          $('#ciudad3').prop('disabled', false);				
        $('#ciudad3').empty();
         $('#comuna').prop('disabled', false);				
        $('#comuna').empty();
        $("#ciudad3").append("<option value='0' selected='selected'>Selecione ciudad</option>");       
                                  
                                
        var res=datos;
        for(k in res){
           for (i in res[k]){
             $("#ciudad3").append("<option value="+i+">"+res[k][i]+"</option>");                    
           }
         }
       }
   });
 });
$("#ciudad3").change(function(){
  
  var ciudad=$("#ciudad3").val();
   $("#comuna3").empty();
   
   $.ajax({
    type: "POST",
    url: "./include/proceso3a.php",
    data: "idCiudad="+ciudad, 	
    dataType: "json",
    success: function(datos){	
       
       $('#comuna3').prop('disabled', false);				
      $('#comuna3').empty();
      $("#comuna3").append("<option value='0' selected='selected'>Seleccione Comuna</option>");  
      var res=datos;
              
      for(k in res){
        for (i in res[k]){
            $("#comuna3").append("<option value="+i+">"+res[k][i]+"</option>");                    
        }
       }
    }
  });
 });
 


  
      $("div a#p").click(function(){
       var alt="./upload/"+$(this).attr("alt");				 
        $("#res").html("<a href='"+alt+"' class='fancybox' id='gallery1'><img src=\'"+alt+"\' width=\"300\" height=\"226\" /></a>");		 
       return(false);
     });
     // $(".fancybox").fancybox();
      return(false);
   });
</script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

  </body>
</html>