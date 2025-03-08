
<!doctype html>
<html lang="es">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;400&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    
    <link rel="shortcut icon" href="https://clickcorredores.cl/favicon.png" type="image/x-icon" />
    <link rel="icon" href="https://clickcorredores.cl/favicon.png" type="image/x-icon" />   
    <link rel="shortcut icon" href="https://clickcorredores.cl/favicon.png"/>
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <meta name="theme-color" content="#000" />
    
 
    <?php 
      if(isset($_GET["mod"]) || isset($_GET["idProp"])){
         echo '<script>var userLogged=false;</script>';
         require_once("./clases/class.detOrbis.php");
         $detOrbis=new detOrbis();
         
      } 
         require_once("./clases/class.orbisPlus23.php");
        $miOrbis=new orbis();
        $d=$miOrbis->datosPag();
        
        
    
         ?>


 
 
    <style> 
    .navbar-expand-lg .navbar-nav .nav-link {
    padding-right: 1rem !important;
    padding-left: 1rem !important;
}
    body{ font-size:12px !important;}
    a:link,a:visited,a:active,a:hover{font-size:16px;}
   
    .navbar-nav .nav-link {
    color: #ffffff !important;
    font-size: 16px !important;
    font-weight: 400 !important;
}
    
    .form-label{ font-size:16px;}
    a.red:link,a.red:visited,a.red:active{
      color:black;
      text-decoration:none;
    }
    a.red:hover{
      color:white;
      text-decoration:none;
    }
         .contact-list {
      list-style: none;
      padding: 0;
      text-align: right;
    }
    .contact-list li {
      display: inline-block;
      margin-right: 20px;
    }
    .contact-list a {
      color: #333;
      text-decoration: none;
      display: flex;
      align-items: center;
    }
    .contact-list .info {
      margin-left: 10px;
    }
    .contact-list .title {
      font-weight: bold;
      text-align: center;
    }
    .contact-list .value {
      color: #888;
      text-align: center;
    }
    .contact-list .icon {
      font-size: 24px;
      margin-right: 10px;
    }
    
    .bg-light {
  background-color: #fff !important;
}
    
    .navbar {  
  padding-top: .0rem !important;
  padding-bottom: .0rem !important;
}
    .btn-primary2 {
      color: #fff;
      background-color: #303031 !important;
      border-color: #303031 !important;
    }
    
    
 
  a.m3:link, a.m3:active, a.m3:visited, a.m3:hover {
  color: #000 !important;
  text-decoration: none;
  font-size: 14px;
}
    
a.m:link, a.m:active, a.m:visited, a.m:hover {
  color: white !important;
  text-decoration: none;
  font-size: 14px;
}
    .form-select-lg {
  padding-top: .5rem;
  padding-bottom: .5rem;
  padding-left: 1rem;
  font-size: 1.25rem; 
  background-color: #d2cfcf !important;
}

 
    hr {
  margin: 1rem 0;
  color: inherit;
  
  border: 0;
  opacity: .25;
  background-color: #aaa !important;
}
    a.mt1:link,a.mt1:active,a.mt1:hover,a.mt1:visited{ font-size:16px; text-decoration:none; color:black;}

    
    .container, .container-lg, .container-md, .container-sm, .container-xl {
  max-width: 1120px !important;
} 

    
    .accordion-button:not(.collapsed) {
  color: #0c63e4;
  background-color: #fbfbfb !important;
  box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
}
.btn-light1 {
  color: #000;
  background-color: #f8f9fa;
  border-color: #f8f9fa;
  border: 1px solid #ced4da !important;
}

@media (max-width: 979px){
  #desk{display:block;}  
  #cel{display:none;}  
 
  .w-100 {
  width: 100% !important;
  height: 300px !important;
}
  .navbar-brand {
  padding-top: .3125rem;
  padding-bottom: .3125rem;
  margin-right: 1rem;
  font-size: 1.25rem;
  text-decoration: none;
  white-space: nowrap;
  width: 53% !important;
}
}
@media (max-width: 439px){  
  .w-100 {
  width: 100% !important;
  height: 180px !important;
}
  #desk{display:none;}
  #cel{display:block;}
 
  .navbar-brand {
  padding-top: .3125rem;
  padding-bottom: .3125rem;
  margin-right: 1rem;
  font-size: 1.25rem;
  text-decoration: none;
  white-space: nowrap;
  width: 53% !important;
}
} 
@media (min-width: 992px){ 
  #desk{display:block;}  
  #cel{display:none;}  
 
  .navbar-brand {
  padding-top: .3125rem;
  padding-bottom: .3125rem;
  margin-right: 1rem;
  font-size: 1.25rem;
  text-decoration: none;
  white-space: nowrap;
  width: 53% !important;
}
}
@media (min-width: 1200px){
  #desk{display:block;}  
  #cel{display:none;}  
 
  .navbar-brand {
  padding-top: .3125rem;
  padding-bottom: .3125rem;
  margin-right: 1rem;
  font-size: 1.25rem;
  text-decoration: none;
  white-space: nowrap;
  width:22% !important;
}
}

    

.card {
  border: 0;
  -webkit-box-shadow: 0 10px 15px -3px rgba(0,0,0,.07),0 4px 6px -2px rgba(0,0,0,.05);
  box-shadow: 0 10px 15px -3px rgba(0,0,0,.07),0 4px 6px -2px rgba(0,0,0,.05);
}
				 
				 
.card {
  background-color: #FFFFFF !important;
  
  border: 1px solid #F9F9F9 !important;
  
}
.list-group-item {
  color: #212529;
  background-color: #FFFFFF !important;
}
    .m{color: #FFFFFF !important;}
    .t{font-size:16px; font-family: 'Open Sans', sans-serif !important; font-weight:bold;text-decoration:none !important; color:#4D4D4D !important; line-height:16px;}  
    div,b,p{ font-family: 'Open Sans', sans-serif !important; font-size: !important;}
    ul,li{font-family: 'Roboto', sans-serif; }
        #titulo{ font-size:20px; font-weight:bold;font-family: 'Roboto', sans-serif;}
        
        a.en11:link,a.en11:visited,a.en11:hover,a.en11:active{  font-family: 'Open Sans', sans-serif !important; font-size:14px !important; color:#FBFBFB !important; text-decoration:none;}
        body{
           font-family: 'Roboto', sans-serif;
            font-size:20px !important;
        }
        .dropdown-item {  
            color:  #080808 !important;  
        }
        .dropdown-menu {
                background-color: #D1D1D1 !important;
        }
        body{background-color:#EDEDED !important;}
    </style>
    <script src="https://kit.fontawesome.com/a50208ac31.js" crossorigin="anonymous"></script>         
    <title>Formulario de evaluación comercial arrendatario</title>
    <meta name="description" content="Formulario de evaluación comercial arrendatario" />
      <meta name="keywords" content="Formulario de evaluación comercial arrendatario>"/>
  
    

 <style>
  #menu {
    color: #ffffff !important;
}
  .btn-info, .btn-info:hover {
    color: #fff !important;
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
}
  .navbar {
  padding-top: .0rem !important;
  padding-bottom: .0rem !important;
  padding: 12px !important;
}
  .bg-light {
  box-shadow: 0 10px 15px -3px rgba(0,0,0,.07),0 4px 6px -2px rgba(0,0,0,.35);
}
  .box-estado {
  overflow: hidden !important;
  position: absolute !important;
  width: 60% !important;
  height: 30px !important;
  float: right !important;
  top: 195px !important;
  right: 15px !important;
  text-align: left;
  left: 8px;
} 
.btn-whatsapp {
  display: block;
  width: 50px;
  height: 50px;
  color: #fff;
  position: fixed;
  right: 15px;
  bottom: 20px;
  border-radius: 50%;
  line-height: 80px;
  text-align: left;
  z-index: 999;
}
   .btn-discado{
    display: block;
  width: 50px;
  height: 50px;
  color: #fff;
  position: fixed;
  right: 15px;
  bottom: 90px;
  border-radius: 50%;
  line-height: 80px;
  text-align: left;
  z-index: 999;
      }

      .btn-correo{
    display: block;
  width: 70px;
  height: 70px;
  color: #fff;
  position: fixed;
  right: 15px;
  bottom: 160px;
  border-radius: 50%;
  line-height: 80px;
  text-align: left;
  z-index: 999;
      }

      .visitanos {
    margin: 0 auto;
    padding: 0px !important;
    border: 0;
    overflow: hidden;
    clear: both;
    width: 100%;
}

.visitanos {
  background: #333 url("./data1/images/a.jpg") top center no-repeat;
        background-position-x: center;
        background-position-y: top;
        background-repeat: no-repeat;
        background-attachment: scroll;
        background-size: auto;
    background-position-x: center;
    background-position-y: top;
    background-repeat: no-repeat;
    background-attachment: scroll;
    background-size: auto;
    background-position-x: center;
    background-position-y: top;
    background-repeat: no-repeat;
    background-attachment: scroll;
    background-size: auto;
    background-attachment: fixed;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
  
}

.bg-danger {
  background-color: #334255 !important;
  color: white !important;
}
body {top: 0 !important;}
.goog-te-banner-frame { display:none !important; }      
.elementor-icon-list-text,li{
  font-size:14px;
}

   </style>
      <?php if(isset($_GET["idProp"])){?> <link rel="stylesheet" href="./css/k.css"><?php } ?>
  </head>
  <body>
  
  <nav class="navbar  navbar-nav fixed-top  navbar-expand-lg navbar-expand-lg navbar-dark bg-dark" style="background-color:#0059b0 !important;">
         <div class="container-fluid">
         <a class="navbar-brand" href="index.php">
  <img src="logoClick.png" style="width:100%; max-width:100%;"/>
  </a>   

            <button class="navbar-toggler" style="margin-right:20px;" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
               <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                  <li class="nav-item">
                     <a class="nav-link active" aria-current="page" href="index.php">Inicio</a>
                  </li>
              
                  <li class="nav-item">
                     <a class="nav-link" href="index.php?mod=pag&rd=6">Quienes somos</a>
                  </li>
 
                  <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Evaluación Comercial
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="evaluacion-comercial-arrendatario.php">Evaluación comercial arrendatario</a></li>
            <li><a class="dropdown-item" href="evaluacion-comercial-aval.php">Evaluación comercial aval</a></li>
    
          </ul>
        </li>

                  <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Propiedades
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="index.php?m=1">Venta de propiedades</a></li>
            <li><a class="dropdown-item" href="index.php?m=2">Arriendo de propiedades</a></li>
    
          </ul>
        </li>
                  <?php 
                  if(!isset($_SESSION["auth"])){
                    ?>
                  <li class="nav-item">
                     <a class="nav-link" href="index.php?mod=login"><i class="fas fa-user" aria-hidden="true"></i> Ingresar</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" href="index.php?mod=crearCuenta">Registrarse</a>
                  </li> 
                  <li class="nav-item">
                     <a class="nav-link btn btn-danger btn-sm" href="index.php?mod=publicar" style="padding-top: 3px !important;padding-bottom: 3px !important;font-size:14px; color:white !important;"><i class="far fa-check-circle" aria-hidden="true"></i> Publica Gratis</a>
                  </li>
                    <?php 
                  }else{
                    if($_SESSION["auth"]["usuario"]!="admin"){
                      ?>
                      <li class="nav-item dropdown">
          
          <div class="dropdown">
                     <a class="btn btn-light dropdown-toggle show" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="true">
                     <i class="fas fa-user" aria-hidden="true"></i>&nbsp;&nbsp; <?php echo $_SESSION["auth"]["email"];?>                   </a>
                     <ul class="dropdown-menu show" data-bs-popper="static">
                     <li><a class="dropdown-item" href="https://www.clickcorredores.cl/panelUser.php?mod=panel">Mis Publicaciones</a></li>
                        <li><a class="dropdown-item" href="https://www.clickcorredores.cl/panelUser.php?mod=panel&amp;op=2">Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="https://www.clickcorredores.cl/panelUser.php?mod=panel&amp;op=3">Modificar Contraseña</a></li>                         
                        <li><a class="dropdown-item" href="https://www.clickcorredores.cl/desconectar2.php">Salir</a></li>
                     </ul>
                  </div>
        </li>
                      <?php 
                    }
                  }
                  ?>
               </ul>
            </div>
         </div>
      </nav>


<section id="contenedor">
<div class="container">
   <div class="row">
    
      <div class="col-md-12">
      <div class="container">
        <div class="row">
            <div class="col-md-9" style="padding-top:20px;">
              
                <div class="card" style="padding:20px;margin-bottom:20px;">
                <div style="margin-bottom:50px;">
                <div style="margin-top:10px;margin-bottom:40px;"><h4>Formulario de evaluación comercial</h4></div>
                <?php 
                if(isset($_GET["msg"])){
                  $m=$_GET["msg"];
                  if($m==1){
                    echo '<div class="alert alert-primary" role="alert" style="font-size:16px;">
                    Formulario de evaluacion comercial de arrendatario se ha enviado con exito, para volver a la portada haga <a href="index.php">click aqui</a>
                  </div>';
                  }else if($m==5){
                    echo '<div class="alert alert-danger" role="alert" style="font-size:16px;">
                    Para enviar el formulario debe seleccionar el captcha, intentelo nuevamente !!
                  </div>';
                  }
                    
                }
                ?>
                <div style="margin-bottom:20px;margin-top:20px;"><h5>Formulario Arrendatario</h5></div>
               
                <form id="form1" name="form1" action="proceso.php" method="post" >
                <div class="mb-3">
    <input type="text" class="form-control" id="primer-nombre1" name="primer-nombre1" placeholder="Primer nombre">
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="segundo-nombre1" name="segundo-nombre1" placeholder="Segundo nombre">
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="apellido-paterno1" name="apellido-paterno1" placeholder="Apellido paterno">
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="apellido-materno1" name="apellido-materno1" placeholder="Apellido materno">
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="rut1" name="rut1" placeholder="RUT">
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="nacionalidad1" name="nacionalidad1" placeholder="Nacionalidad">
</div>
<div class="mb-3">
    <select class="form-select" id="estado-civil1" name="estado-civil1">
        <option value="">Estado civil</option>
        <option value="Soltero">Soltero</option>
        <option value="Casado">Casado</option>
        <option value="Divorciado">Divorciado</option>
        <option value="Separado">Separado</option>
        <option value="Viudo">Viudo</option>
    </select>
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="profesion-oficio1" name="profesion-oficio1" placeholder="Profesión / Oficio">
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="empresa1" name="empresa1" placeholder="Empresa">
</div>
<div class="mb-3">
    <select class="form-select" id="antiguedad-laboral1" name="antiguedad-laboral1">
        <option value="">Antiguedad laboral</option>
        <option value="1 año">1 año</option>
        <option value="2 años">2 años</option>
        <option value="3 o más años">3 o más años</option>
    </select>
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="banco1" name="banco1" placeholder="Banco">
</div>
<div class="mb-3">

    <select class="form-select" id="cuenta-corriente1" name="cuenta-corriente1">
        <option value="">Cuenta Corriente</option>
        <option value="1 año">Cuenta Vista</option>
        <option value="2 años">Cuenta Corriente</option>
        <option value="3 o más años">Cuenta de Ahorro</option>
    </select>


 
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="renta-liquida1" name="renta-liquida1" placeholder="Renta líquida">
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="direccion-particular1" name="direccion-particular1" placeholder="Dirección particular">
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="direccion-laboral1" name="direccion-laboral1" placeholder="Dirección laboral">
</div>
<div class="mb-3">
    <input type="tel" class="form-control" id="fono-particular1" name="fono-particular1" placeholder="Fono particular">
</div>
<div class="mb-3">
    <input type="tel" class="form-control" id="fono-celular1" name="fono-celular1" placeholder="Fono celular">
</div>
<div class="mb-3">
    <input type="email" class="form-control" id="email1" name="email1" placeholder="Email">
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="propiedad-postulacion1" name="propiedad-postulacion1" placeholder="Propiedad a la que postula">
</div>


                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="acceptance-7211" >
                        <label class="form-check-label" for="acceptance-7211" style="font-size:16px;">Acepto enviar esta información para evaluación comercial.</label>
                    </div>
                    <div style="margin-top:10px;" class="g-recaptcha" data-sitekey="6LdUViwqAAAAAFkMEaYxPwCeByCjI2rN4-KXbICW"></div>
                    <button type="button" role="button" name="enviar-btn" class="btn btn-primary" id="enviar-btn" style="margin-top:10px;" disabled>Enviar</button>
                </form>
            </div>
        </div>
    </div>
</div>
  
 
      </div>
              </div>


   </div>
</div>
   
</section> 


<section id="piePagina" style="background-color:#0059b0;padding-top:20px;">
  <div class="container">
      <div class="row">
          <div class="col-md-4">
            <div><img src="logoClick.png" style="width:70%;max-width:100%;margin-bottom:15px;"/></div>
            <div>
            <div style="margin-top:10px;color:white;font-size:14px;margin-bottom:5px;"><i class="fa fa-map-marker"></i> <?php echo $d["direccion"];?></div>
            </div>
          
          </div>
          <div class="col-md-2">
            <div><h5 style="color:white;">Enlaces</h3></div>
            <div>
                <ul>
                  <li id="menu"><a href="index.php" class="m">Inicio</a></li>
                  <li id="menu"><a href="index.php?mod=pag&rd=6" class="m">Nosotros</a></li>
                 
                  <li id="menu"><a href="index.php?mod=contacto" class="m">Contacto</a></li>
                </ul>

            </div>
            
             
          </div>
          <div class="col-md-3">
            <div><h5 style="color:white;">Servicios</h3></div>
            <div>
                <ul>
                  <li id="menu"><a href="index.php?mod=pag&rd=21" class="m">Servicios</a></li>
                  <li id="menu"><a href="index.php?m=1" class="m">Venta de propiedades</a></li>
                  <li id="menu"><a href="index.php?m=2" class="m">Arriendo de propiedades</a></li>
                  
                 
                </ul>

            </div>
                  </div>
     
          <div class="col-md-3">
          <div><h5 style="color:white;">Contacto</h3></div>

          <div>
              <div style="color:white;font-size:14px;"><i class="fas fa-phone"></i> 	<?php echo "<a href='tel:".$d["telefono"]."' class='m'>".$d["telefono"]."</a>";?></div>
              <div style="color:white;font-size:14px;margin-top:10px; margin-bottom:10px;"><i class="fas fa-phone"></i>	<?php echo "<a href='tel:".$d["telefono1"]."' class='m'>".$d["telefono1"]."</a>";?></div>
              <div style="color:white;font-size:14px;"><i class="fas fa-envelope"></i>	<?php echo "<a href='mailto:".$d["email"]."' class='m'>".$d["email"]."</a>";?></div>
              <div style="margin-top:10px;color:white;font-size:14px;"><i class="fas fa-globe"></i> www.clickcorredores.cl</div>
           
          </div>
          </div>
          
      </div>
      <div style="margin-top:5px;" align="center"><?php 
            //$miOrbis->indicadoresEconomicos2();
            ?></div>
  </div>
</section>
<div id="copyright" style="padding:10px;background-color:#004991;">
    <div class="container">
        <div class="row">
            <div class="col-md-12" align="center"> 
            <div  align="center" style="font-size:14px;color:white !important;">&copy; 2023 <b>Portal Inmobiliario Click Corredores</b> Todos los Derechos Reservados</div>
           
            <div align="center" style="font-size:12px;margin-top:5px;color:#f3f3f3;"><?php $miOrbis->tiempoFin();?></div>
            
            
            </div>
        </div>
    </div>
</div>
<div class="btn-whatsapp" align="left">
      <a href="https://api.whatsapp.com/send?phone=56937228649" target="_blank">
      <img data-lazyloaded="1" data-placeholder-resp="150x150" src="w4.png" data-src="w4.png" alt="WhatsApp" title="WhatsApp" class="img-icon ccw-analytics desktop entered litespeed-loaded" data-ll-status="loaded" width="50" height="50">
      </a>
      </div>
      <div class="btn-discado" align="left">
      <a href="tel:+56937228649" target="_blank">
      <img data-lazyloaded="1" data-placeholder-resp="150x150" src="w2.png" data-src="w2.png" alt="Discado" title="Discado" class="img-icon ccw-analytics desktop entered litespeed-loaded" data-ll-status="loaded" width="50" height="50">
      </a>
      </div>
   
   
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
   
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const checkbox = $('#acceptance-7211');
    const enviarBtn = $('#enviar-btn');

    checkbox.on('change', function () {
        if (this.checked) {
            enviarBtn.prop('disabled', false);
        } else {
            enviarBtn.prop('disabled', true);
        }
    });
    $("#enviar-btn").click(function(){
      
      const c = grecaptcha.getResponse();

     
        const primerNombre = $('#primer-nombre1').val().trim();
        const segundoNombre=$("#segundo-nombre1").val().trim();
        const apellidoPaterno = $('#apellido-paterno1').val().trim();
        const apellidoMaterno = $('#apellido-materno1').val().trim();
        const rut = $('#rut1').val().trim();
        const nacionalidad = $('#nacionalidad1').val().trim();
        const estadoCivil = $('#estado-civil1').val();
        const profesionOficio = $('#profesion-oficio1').val().trim();
        const empresa = $('#empresa1').val().trim();
        const antiguedadLaboral = $('#antiguedad-laboral1').val();

        const cuentaCorriente = $('#cuenta-corriente1').val();


        const banco = $('#banco1').val().trim();
        const rentaLiquida = $('#renta-liquida1').val().trim();
        const direccionParticular = $('#direccion-particular1').val().trim();
        const direccionLaboral = $('#direccion-laboral1').val().trim();
        const fonoCelular = $('#fono-celular1').val().trim();
        const email = $('#email1').val().trim();
        const propiedadPostulacion = $('#propiedad-postulacion1').val().trim();
        const acceptance = checkbox.prop('checked');


       // Validación de campos obligatorios
if (primerNombre === '') {
    alert('Por favor, ingrese el primer nombre.');
    $('#primer-nombre1').focus();
    return;
}
if (segundoNombre === '') {
    alert('Por favor, ingrese el segundo nombre.');
    $('#segundo-nombre1').focus();
    return;
}
if (apellidoPaterno === '') {
    alert('Por favor, ingrese el apellido paterno.');
    $('#apellido-paterno1').focus();
    return;
}

if (apellidoMaterno === '') {
    alert('Por favor, ingrese el apellido materno.');
    $('#apellido-materno1').focus();
    return;
}

if (rut === '') {
    alert('Por favor, ingrese el RUT.');
    $('#rut1').focus();
    return;
}

if (nacionalidad === '') {
    alert('Por favor, ingrese la nacionalidad.');
    $('#nacionalidad1').focus();
    return;
}

if (estadoCivil === '') {
    alert('Por favor, seleccione el estado civil.');
    $('#estado-civil1').focus();
    return;
}

if (profesionOficio === '') {
    alert('Por favor, ingrese la profesión / oficio.');
    $('#profesion-oficio1').focus();
    return;
}

if (empresa === '') {
    alert('Por favor, ingrese el nombre de la empresa.');
    $('#empresa1').focus();
    return;
}

if (antiguedadLaboral === '') {
    alert('Por favor, seleccione la antigüedad laboral.');
    $('#antiguedad-laboral1').focus();
    return;
}
if (cuentaCorriente === '') {
    alert('Por favor, seleccione el tipo de cuenta.');
    $('#cuenta-corriente1').focus();
    return;
}
if (banco === '') {
    alert('Por favor, ingrese el banco.');
    $('#banco1').focus();
    return;
}

if (rentaLiquida === '') {
    alert('Por favor, ingrese la renta líquida.');
    $('#renta-liquida1').focus();
    return;
}

if (direccionParticular === '') {
    alert('Por favor, ingrese la dirección particular.');
    $('#direccion-particular1').focus();
    return;
}

if (direccionLaboral === '') {
    alert('Por favor, ingrese la dirección laboral.');
    $('#direccion-laboral1').focus();
    return;
}

if (fonoCelular === '') {
    alert('Por favor, ingrese el teléfono celular.');
    $('#fono-celular1').focus();
    return;
}

if (email === '') {
    alert('Por favor, ingrese el correo electrónico.');
    $('#email1').focus();
    return;
} else if (!validateEmail(email)) {
    alert('Por favor, ingrese un correo electrónico válido.');
    $('#email1').focus();
    return;
}

if (propiedadPostulacion === '') {
    alert('Por favor, ingrese la propiedad de postulación.');
    $('#propiedad-postulacion1').focus();
    return;
}
if(c===""){
    alert("Por favor, completa el reCAPTCHA.");
 }else{
     $("#form1").submit();
 }


// Función para validar el formato de correo electrónico
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}


       
            
    

        // Validación adicional si es necesario

        // Enviar formulario
        

return(false);

    });
    return(false);
    
});
</script>
     
 

  </body>
</html>




 