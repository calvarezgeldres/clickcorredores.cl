<?php
ob_start();
session_start();
if(!isset($_SESSION["auth"]["usuario"])){
	header("location:login.php");
	exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">		
		<title>Panel de Control</title>
		<meta name="description" content="Boostrap">
		<!-- Favicon -->
		
		<!-- Bootstrap CSS -->
		<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

		
		<!-- Font Awesome CSS -->
	
		<link href="assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
		<link rel="shortcut icon" href="favicon.png"/> 
		<!-- Custom CSS -->
		<link href="assets/css/style.css" rel="stylesheet" type="text/css" />
		<script src="https://cdn.ckeditor.com/4.16.1/standard/ckeditor.js"></script>
	 <script>
 
 CKEDITOR.replace( 'descripcion', {
        extraPlugins: 'abbr'
} );
		 

	 </script> 
	<style>
		.headerbar .headerbar-left {
    background: #d9112e !important;
    float: left;
    text-align: center;
    height: 50px;
    position: relative;
    width: 250px;
    z-index: 1;
    color: white !important;
}
		.navbar-custom {
    background-color: #d9112e !important;
    border-radius: 0;
    margin-bottom: 0;
    padding: 0 10px;
    margin-left: 250px;
    min-height: 50px;
}
	 .notif .noti-title {
  border-radius: 0;
  background-color: #d9112e  !important;
  margin: 0;
  width: auto;
  padding: 8px 15px 12px 15px;
}
		#sidebar-menu > ul > li > a:hover {
  color: #FFF !important;
  background-color:#d9112e  !important;
  text-decoration: none;
  border-left: 2px solid #fff !important;
  color: white !important;
}
		 
			.profile-dropdown {
    width: 250px !important;
}
#sidebar-menu > ul > li > a.active {
  color: #e8e8e8 !important;
  background-color: #d9112e  !important;
}
.main-sidebar {
    top: 50px;
    width: 250px;
    z-index: 10;
    background: #636363 !important;
    bottom: 70px;
    margin-bottom: -70px;
    margin-top: 0;
    padding-bottom: 70px;
    position: absolute;
}
 bar-menu .subdrop {
     
    color: #023574 !important;
 
    background-color: #e8e8e8 !important;
    border-left: 2px solid #023574 !important;
}
.btn-success {
    background-color: #023574 !important;
    border-color: #023574 !important;
    color: #fff;
}
			 
.button-menu-mobile {
    color: #fff !important;
    font-size: 28px;
    line-height: 50px;
    cursor: pointer;
}
.logo {
    color: #fff !important;
    font-size: 20px;
    font-weight: 200;
    text-transform: uppercase;
    letter-spacing: 1px;
    line-height: 48px;
}

.btn-primary {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
}
#sidebar-menu .subdrop {
  color: white !important;
}
.bg-default {
  background-color: #d9112e  !important;
}
 
#sidebar-menu ul ul a {
  color: #fff !important;
  -webkit-transition: all 0.3s ease-out;
  -moz-transition: all 0.3s ease-out;
  -o-transition: all 0.3s ease-out;
  -ms-transition: all 0.3s ease-out;
  transition: all 0.3s ease-out;
  display: block;
  padding: 10px 20px 10px 60px;
}

#sidebar-menu > ul > li > a {
    color: #fff;
    font-weight: 400;
    font-size: 0.90rem;
}
 
		.widget-messages .message-item .message-user-img {
display: block;
float: left;
margin-right: 15px;
width: 90px !important;
}
.dropdown-menu {
position: absolute;
top: 100%;
left: 0;
z-index: 1000;
display: none;
float: left;
min-width: 10rem !important;

padding: .5rem  !important;
margin: .125rem 0 0;
font-size: 1rem;
color: gray;
text-align: left;
list-style: none;
background-color: #fff;
background-clip: padding-box;
border: 1px solid rgba(0,0,0,.15);
border-radius: .25rem;
}
		</style>
		<style>
	.profile-dropdown {
    width: 270px !important;
}
		.widget-messages .message-item .message-user-img {
display: block;
float: left;
margin-right: 15px;
width: 90px !important;
}
.dropdown-menu {
position: absolute;
top: 100%;
left: 0;
z-index: 1000;
display: none;
float: left;
min-width: 10rem !important;

padding: .5rem  !important;
margin: .125rem 0 0;
font-size: 1rem;
color: gray;
text-align: left;
list-style: none;
background-color: #fff;
background-clip: padding-box;
border: 1px solid rgba(0,0,0,.15);
border-radius: .25rem;
}
		</style>
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript">
function estado(i,idProp){
 
	$.ajax({
			type:"post",
			url: "./include/prog1.php", 
        	data:"idProp="+idProp+"&id="+i,
			success:function(datos){			 
			 
				if(datos=="ok"){
					document.location="panel.php?mod=panel&op=4";
				}
			}
		});    
}
function cambiarEstado(estado, idProp) {
    // Muestra el modal de carga
    var loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'), {
        backdrop: 'static',
        keyboard: false
    });
    loadingModal.show();

    $.ajax({
        url: './include/proceso55.php', 
        type: 'POST',
        data: {
            estado: estado,
            idProp: idProp
        },
        success: function(response) {            
            if (response.trim() === 'true') {                
                loadingModal.hide();                
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('mod') === 'panel' && urlParams.get('op') === '4') {                    
                    
					window.location.href = "panel.php?mod=panel&op=4";
                } else {                    
                    window.location.href = "panel.php";
                }
            } else {                
                alert("Error al actualizar el estado: " + response);
            }
        },
        complete: function() {            
            loadingModal.hide();
        }
    });
}




function ejecutar(id){  
	    $("#sMenu").empty();
		 $('#sMenu').prop('disabled', 'disabled');
        var res=new Array(); 	 
		$.ajax({
			type:"post",
			url: "./include/proceso5.php", 
        	data: {id:id},  
			dataType:"json",
			success:function(datos){
			$("#sMenu").empty(); 
				$("#sMenu").attr("disabled",true);
				if(datos=="error"){					
					$('#sMenu').prop('disabled', 'disabled');
				}else{
					 $("#sMenu").empty();
					$("#sMenu").attr("disabled",false);
					res=datos;
					for(k in res){
						for (i in res[k]){
               	 		     $("#sMenu").append("<option value="+i+">"+res[k][i]+"</option>");     
						}
					}
				}
			}
		});    
}
function agregarSubMenu(){
	document.location="panel.php?mod=panel&op=92";
}
		$(document).ready(function() {
			$("#filtrar").change(function(){
				var loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'), {
            backdrop: 'static',
            keyboard: false
        });
        	loadingModal.show();
			$("#form1").submit();
		});
		
			$("#region").change(function(){
    
	   var region=$("#region").val();
		   $("#ciudad").empty();
		
		$.ajax({
			   type: "POST",
			   url: "./include/proceso3.php",
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
   
	
	
			 $("#sMenu").attr("disabled","true");
			$("#b1").click(function(){
				document.location="panel.php?mod=panel&op=4&id=1";
			});
			
		     $("#b2").click(function(){
				document.location="panel.php?mod=panel&op=1&id=2";
			});
			
			$("#b3").click(function(){
				document.location="?mod=panel&op=3";
			});
			
			
			$("#b4").click(function(){
				document.location="panel.php?mod=panel&op=4";
			});
			
			
			$("#b5").click(function(){
				document.location="panel.php?mod=panel&op=5";
			});
			
			
			$("#b6").click(function(){
				document.location="panel.php?mod=panel&op=6";
			});
			
			
			$("#b7").click(function(){
				document.location="panel.php?mod=panel&op=7";
			});
			
			
			$("#b8").click(function(){
				document.location="panel.php?mod=panel&op=9";
			});
			
			
			$("#b9").click(function(){
				document.location="panel.php?mod=panel&op=61";
			});
			$("#c1").click(function(){
				document.location="panel.php?mod=panel&op=4";	
			})
			
			$("#b10").click(function(){
				 
				document.location="panel.php?mod=panel&op=12";
			});
		 
			$("#b11").click(function(){
				document.location="panel.php?mod=panel&op=13";
			});
			
			
			$("#b12").click(function(){
				document.location="panel.php?mod=panel&op=14";
			});
			
			$("#b13").click(function(){
				document.location="panel.php?mod=panel&op=13";
			});
			
			$("#b14").click(function(){
				document.location="panel.php?mod=panel&op=14";
			});
			
			$("#b15").click(function(){
				document.location="panel.php?mod=panel&op=15";
			});
			
			
			$("#b16").click(function(){
				document.location="panel.php?mod=panel&op=16";
			});
			
			$("#b17").click(function(){
				document.location="panel.php?mod=panel&op=17";
			});
			
			$("#b18").click(function(){
				document.location="panel.php?mod=panel&op=18";
			});
			$("#b19").click(function(){
				document.location="panel.php?mod=panel&op=19";
			});
			
			$("#b20").click(function(){
				document.location="panel.php?mod=panel&op=20";
			});
			
			
			
			
			$("#c").click(function(){
				document.location="desconectar.php";	
			});
			
			$("#xx").click(function(){
				document.location="panel.php?mod=panel";	
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
</head>

<body class="adminbody">
<!-- Modal de Carga -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-labelledby="loadingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p>Actualizando estado de la publicación...</p>
            </div>
        </div>
    </div>
</div>

<?php
		
			  require_once("./clases/subClase/class.cmsPropAdmin.php");
			  $miOrbis=new cmsPropAdmin();
?>
<div id="main">

	<!-- top bar navigation -->
	<div class="headerbar">

		<!-- LOGO -->
        <div class="headerbar-left">
			<a href="panel.php" class="logo"><span>Administrador</span></a>
        </div>

        <nav class="navbar-custom">

                    <ul class="list-inline float-right mb-0">
					<li class="list-inline-item dropdown notif">
                            <a class="nav-link dropdown-toggle arrow-none" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <i class="fa fa-fw fa-bell-o"></i><span class="notif-bullet"></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-lg">
								<!-- item-->
                           
								
								<div class="dropdown-item noti-title">
                                    <h5><small>Soporte Técnico</small></h5>
                                </div>

                                <!-- item-->
                                <a target="_blank" href="https://www.programacionwebchile.cl" class="dropdown-item notify-item">                                    
                                    <p class="notify-details ml-0">
                                        <b>Para soporte técnico contáctenos</b>
                                        <span>contacto@programacionwebchile.cl</span>
                                    </p>
                                </a>
								 
                                <!-- item-->
								<a target="_blank" href="https://www.programacionwebchile.cl" class="dropdown-item notify-item">   
                                    <p class="notify-details ml-0">
                                        <b>Sistema de anuncios de corretaje</b>
										<span>Versión 2.4 Lite</span>
										<span>Actualizada en Agosto del 2024</span>
                                      
                                    </p>

									      </a>  

                            </div>
                        </li>
						 

 
						
                        
                        
						
                        <li class="list-inline-item dropdown notif">
                            <a class="nav-link dropdown-toggle nav-user" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
							<?php
								$miOrbis->fotoPerfil();
								?>
								 
								
                            </a>
                            <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                                <!-- item-->
                                <div class="dropdown-item noti-title">
                                    <h5 class="text-overflow"><small>Hola, admin</small> </h5>
                                </div>
								<a href="panel.php?mod=panel&op=144"  class="dropdown-item notify-item">
                                    <i class="fa fa-address-card"></i> <span>Ver Perfil de admin</span>
                                </a>
								<a href="https://www.clickcorredores.cl" target="_blank" class="dropdown-item notify-item">
                                    <i class="fa fa-sitemap"></i> <span>Ver Página Web</span>
                                </a>
								<a href="info.php" target="_blank" class="dropdown-item notify-item">
                                    <i class="fa fa-drivers-license-o"></i> <span>Versión de Php 8.2</span>
									
                                </a>
								<a href="sitemap.xml" target="_blank" class="dropdown-item notify-item">
                                    <i class="fa fa-drivers-license-o"></i> <span>Ver SiteMap.xml</span>
									
                                </a>
								<a href="panel.php?mod=panel&op=244" class="dropdown-item notify-item">
                                    <i class="fa fa-drivers-license-o"></i> <span>Ver Licencia</span>
									
                                </a>
								<a href="panel.php?mod=panel&op=245" class="dropdown-item notify-item">
                                    <i class="fa fa-drivers-license-o"></i> <span>Seguridad y contraseña</span>									
                                </a>
                                <a href="desconectar.php" class="dropdown-item notify-item">
                                    <i class="fa fa-power-off"></i> <span>Salir</span>									
                                </a>							
								 
                            </div>
                        </li>

                    </ul>

                    <ul class="list-inline menu-left mb-0">
                        <li class="float-left">
                            <button class="button-menu-mobile open-left">
								<i class="fa fa-fw fa-bars"></i>
                            </button>
                        </li>                        
                    </ul>

        </nav>

	</div>
	<!-- End Navigation -->
	
 
	<!-- Left Sidebar -->
	<div class="left main-sidebar">
	
	<div class="sidebar-inner leftscroll">

		<div id="sidebar-menu">
	
		<ul>

				<li class="submenu">
					<a href="panel.php"  class="active subdrop"><i class="fa fa-fw fa-bars"></i><span> Panel de Control </span> </a>
				</li>

				<li class="submenu">
					<a href="#" <?php if($_GET["op"]==4){ echo 'class="subdrop"'; } ?>><i class="fas fa-home"></i><span> Propiedades</span> <span class="menu-arrow"></span></a>
					<ul class="list-unstyled" <?php if($_GET["op"]==4){ echo ' style="display: block;"'; } ?>>
						 <li <?php if($_GET["op"]==4){ echo 'class="active"'; } ?> ><a href="panel.php?mod=panel&op=4" >Lista de Propiedades</a></li>
                         <li><a href="panel.php?op=4&m=add&mod=panel">Agregar Propiedad</a></li>							
					</ul>
				</li>
				
				
									
				

				<li class="submenu">
					<a href="#" <?php if($_GET["op"]==19){echo "class='subdrop'";} ?> ><i class="fa fa-fw fa-file-text-o"></i> <span> Páginas </span> <span class="menu-arrow"></span></a>
						<ul class="list-unstyled" <?php if($_GET["op"]==19){ echo 'style="display: block;" '; } ?> >
						<li <?php if($_GET["op"]==19){ echo 'class="active"'; } ?>  >
                                <a href="panel.php?mod=panel&op=19">Lista de páginas</a>
                            </li>
                            <li>

                                <a href="panel.php?mod=panel&op=19&m=add">Agregar Página</a>
                            </li>							
						</ul>
				</li>
				<li class="submenu">
					<a href="#" <?php if($_GET["op"]==445){echo "class='subdrop'";} ?> ><i class="fa fa-fw fa-file-text-o"></i> <span> Blog </span> <span class="menu-arrow"></span></a>
						<ul class="list-unstyled" <?php if($_GET["op"]==445){ echo 'style="display: block;" '; } ?> >
						<li <?php if($_GET["op"]==445){ echo 'class="active"'; } ?>  >
                                <a href="panel.php?mod=panel&op=445&m=add">Blog de noticias</a>
                            </li>
                            <li>

                                <a href="panel.php?mod=panel&op=445">Lista de entradas</a>
                            </li>							
						</ul>
				</li>


				<li class="submenu">
					<a href="#" <?php if($_GET["op"]==14451){echo "class='subdrop'";} ?> ><i class="fa fa-fw fa-file-text-o"></i> <span> Agentes </span> <span class="menu-arrow"></span></a>
						<ul class="list-unstyled" <?php if($_GET["op"]==14451){ echo 'style="display: block;" '; } ?> >
						<li <?php if($_GET["op"]==14451){ echo 'class="active"'; } ?>  >
                                <a href="panel.php?mod=panel&op=14451&m=add">Lista de Agentes</a>
                            </li>
                            
						</ul>
				</li>



				<li class="submenu">
					<a href="#" <?php if($_GET["op"]==4451){echo "class='subdrop'";} ?> ><i class="fa fa-fw fa-file-text-o"></i> <span> Accesos </span> <span class="menu-arrow"></span></a>
						<ul class="list-unstyled" <?php if($_GET["op"]==4451){ echo 'style="display: block;" '; } ?> >
						<li <?php if($_GET["op"]==4451){ echo 'class="active"'; } ?>  >
                                <a href="https://www.clickcorredores.cl/webmail" target="_blank">Acceso a correo</a>
                            </li>
                            <li>
                                <a href="https://www.clickcorredores.cl/cpanel" target="_blank">Acceso al hosting</a>
                            </li>							
							<li>
                                <a href="https://homer.sii.cl/" target="_blank">Acceso a SII</a>
                            </li>
							<li>
                                <a href="https://www.gmail.com" target="_blank">Acceso a gmail</a>
                            </li>							
													
							<li>
                                <a href="https://www.google.com/maps/place/Santiago,+Regi%C3%B3n+Metropolitana/@-33.4718999,-70.9100253,10z/data=!3m1!4b1!4m5!3m4!1s0x9662c5410425af2f:0x8475d53c400f0931!8m2!3d-33.4488897!4d-70.6692655?hl=es-CL" target="_blank">Acceso a Googlemap</a>
                            </li>							
							<li>
                                <a href="https://www.google.com/search?client=firefox-b-d&q=calculadora+online" target="_blank">Calculadora Online</a>
                            </li>							
							<li>
                                <a href="https://servicios.cmfchile.cl/simuladorhipotecario/aplicacion?indice=101.2.1" target="_blank">Simulador Hipotecario</a>
                            </li>							
							<li>
                                <a href="https://calculadoraipc.ine.cl/" target="_blank">Calculadora IPC</a>
                            </li>							
							
						</ul>
				</li> 
				<li class="submenu">
					<a href="panel.php?mod=panel&op=61" <?php if($_GET["op"]==61){echo "class='subdrop'";} ?> ><i class="fa fa-fw fa-table"></i>  <span> Sliders </span></a>						
				</li>
				
				 
				<li class="submenu">
					<a href="panel.php?mod=panel&op=9" <?php if($_GET["op"]==9){echo "class='subdrop'";} ?> ><i class="fa fa-fw fa-cogs"></i> <span> Configuración General </span></a>						
				</li>
				<li class="submenu">
					<a href="panel.php?mod=panel&op=14" <?php if($_GET["op"]==14){echo "class='subdrop'";} ?> ><i class="fa fa-fw fa-area-chart"></i> <span> Monitor de visitas </span></a>						
				</li>
				

				 
				
		</ul>

 

		</div>
	
	 

	</div>

</div>
	 
	<!-- End Sidebar -->


    <div class="content-page">
	
		<!-- Start content -->
        <div class="content">
            
			<div class="container-fluid">
					
						<div class="row">
							<div class="col-xl-12">
									<div class="breadcrumb-holder">
									<h1 class="main-title float-left"> Panel de Control	</h1>
											<ol class="breadcrumb float-right">
												<li class="breadcrumb-item">Inicio</li>
												<li class="breadcrumb-item active">Panel de Control</li>
											</ol>
											<div class="clearfix"></div>
									</div>
							</div>
						</div>
						 <?php
						 if(!isset($_GET["mod"])){
						 ?>
						 <div class="row">
									<div class="col-xs-12 col-md-6 col-lg-6 col-xl-3">
											<div class="card-box noradius noborder bg-default">
													<i class="fa fa-home float-right text-white"></i>
													<h6 class="text-white text-uppercase m-b-20">PROPIEDADES PUBLICADAS</h6>
													<h1 class="m-b-20 text-white counter"><?php  $miOrbis->totalProp();?></h1>
													 
											</div>
									</div>
									<div class="col-xs-12 col-md-6 col-lg-6 col-xl-3">
											<div class="card-box noradius noborder bg-warning">
													<i class="fa fa-home float-right text-white"></i>
													<h6 class="text-white text-uppercase m-b-20">PROPIEDADES DESTACADAS</h6>
													<h1 class="m-b-20 text-white counter"><?php  $miOrbis->totalPropDesc();?></h1>
										 
											</div>
									</div>
									<div class="col-xs-12 col-md-6 col-lg-6 col-xl-3">
											<div class="card-box noradius noborder bg-info">
													<i class="fa fa-file-o float-right text-white"></i>
													<h6 class="text-white text-uppercase m-b-20">TOTAL OPERACIONES</h6>
													<h1 class="m-b-20 text-white counter"><?php  $miOrbis->totalOperaciones();?></h1>
													
											</div>
									</div>
									<div class="col-xs-12 col-md-6 col-lg-6 col-xl-3">
											<div class="card-box noradius noborder bg-danger">
													<i class="fa fa-bar-chart float-right text-white"></i>
													<h6 class="text-white text-uppercase m-b-20">N° DE VISITANTES</h6>
													<h1 class="m-b-20 text-white counter"><?php  $miOrbis->totalVisitantes();?></h1>
												 
											</div>
									</div>

									

									
							</div>
						 <?php 
						 }
						 ?>
							
							 
							<?php
							 	if(!isset($_GET["mod"])){
							 ?>
							
							
							 


							

							<div class="row">
							<div class="col-md-12">

							<div class="card mb-3">
											<div class="card-header">
												<h3><i class="fas fa-home"></i> Propiedades publicadas</h3>
												Lista de propiedades publicadas
											</div>
												
											<div class="card-body" style="margin:0px; padding:0px;">
											<?php $miOrbis->tablaPropiedades(); ?>
											
											</div>							
											 
										</div>

										
							

								</div>
							 
							</div>

							<?php 
								}else if(isset($_GET["mod"])){
							?>
							<div class="row">						
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">		
									<div class="card mb-3">
										<div class="card-header">
											 <?php
		 if(isset($_GET["op"])){
			 $op=htmlentities($_GET["op"]); 
			 if($op==4){
				if(isset($_GET["mq"]) && $_GET["mq"]=="editar"){
					
					echo "<div class='row'><div class='col-md-8'>					 
					<h3><i class='fa fa-edit'></i> Editar Propiedad</h3>Editar datos ingresados";
					echo '</div>
					<div class="col-md-4">';
					if(isset($_GET["idq"])){$id=$_GET["idq"];}
					
					echo "</div>";
	   
					echo '</div>';

				}else if(isset($_GET["mq"]) && $_GET["mq"]=="galeria"){
					if(isset($_GET["mp"])){
					 echo "<div class='row'><div class='col-md-8'>					 
					 <h3><i class='fa fa-edit'></i>Edición de foto individual</h3>Edita y cambia la orientación de fotografia";
					 echo '</div>
					 <div class="col-md-4">';
					 if(isset($_GET["idq"])){$idq=$_GET["idq"];}
					 echo '<div class="btn-group btn-group-sm pull-right">						
					 <a href="panel.php?mq=galeria&mod=panel&op=4&idq='.$idq.'" id="agregarProd" name="agregarProd" class="btn btn-success">
					 <i class="fa fa-plus"></i> Volver a Galeria</a></div>';
					 echo "</div>";
		
					 echo '</div>';
					}else{
				 echo "<div class='row'><div class='col-md-8'>					 
				 <h3><i class='fa fa-edit'></i>Galeria de Fotos</h3>Fotos de la propiedad";
				 echo '</div>
				 <div class="col-md-4">';
				 if(isset($_GET["idq"])){$id=$_GET["idq"];}
				 echo '<div class="btn-group btn-group-sm pull-right"><a href="panel.php?mq=editar&mod=panel&op=4&idq='.$id.'" id="agregarProd" name="agregarProd" class="btn btn-success"><i class="fa fa-plus"></i> Editar Propiedad </a>&nbsp;<a href="panel.php?mod=panel&op=4" id="agregarProd" name="agregarProd" class="btn btn-success"><i class="fa fa-plus"></i> Lista de propiedades </a></div>';
				 echo "</div>";
	
				 echo '</div>';
					}
				}else if(isset($_GET["m"])&& $_GET["m"]=="add"){
					echo "<h3><i class='fa fa-edit'></i> Agregar Propiedad</h3>Agrega Propiedad";
				}else{
					 echo "<div class='row'><div class='col-md-8'>					 
				  <h3><i class='fa fa-table'></i> Lista de Propiedades</h3>Listado completo de Propiedades";
				  echo '</div>
				  <div class="col-md-4">';
				  echo '<div class="btn-group btn-group-sm pull-right"><a href="panel.php?op=4&m=add&mod=panel" id="agregarProd" name="agregarProd" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Propiedad</a></div>';
				  echo "</div>";
	 
				  echo '</div>';
				}
			   }else if($op==6){

				   if(isset($_GET["op"]) && $_GET["op"]==6){
					   if(isset($_GET["mq"])){
						echo "<div class='row'><div class='col-md-12'>					 
						<h3><i class='fa fa-table'></i> Editar Ciudad </h3>Edición de ciudad";
						echo '</div></div>';
					   }else{
					echo "<div class='row'><div class='col-md-8'>					 
					<h3><i class='fa fa-table'></i> Lista de Ciudades </h3>Listado de ciudades";
					echo '</div>
					<div class="col-md-4">';
					echo '<div class="btn-group btn-group-sm pull-right"><a href="panel.php?mod=panel&op=6&m=add" id="agregarProd" name="agregarProd" class="btn btn-success"><i class="fa fa-plus"></i> Agregar ciudad</a></div>';
					echo "</div>";
	   
					echo '</div>';
					   }
 
				   }else{
					echo "<div class='row'><div class='col-md-12'>					 
					<h3><i class='fa fa-table'></i> Ingresar ciudades </h3> Ingrese una ciudad";
					echo '</div>';
					
					echo '</div>';
				   }				
				}else if($op==445){
					if(isset($_GET["mq"]) && $_GET["mq"]=="editar"){
						echo "<h3><i class='fa fa-edit'></i> Editar entrada</h3>Edita entrada";
					}else if(isset($_GET["m"])&& $_GET["m"]=="add"){
						echo "<h3><i class='fa fa-edit'></i> Agregar entrada </h3>Agregar entrada";
					}else{
					 echo "<div class='row'><div class='col-md-8'>					 
					 <h3><i class='fa fa-table'></i> Lista de entradas </h3>Listado completo de entradas publicadas";
					 echo '</div>
					 <div class="col-md-4">';
					 echo '<div class="btn-group btn-group-sm pull-right"><a href="panel.php?mod=panel&op=445&m=add" id="agregarProd" name="agregarProd" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Entrada</a></div>';
					 echo "</div>";
		
					 echo '</div>';
					}

			   }else if($op==19){
				   if(isset($_GET["mq"]) && $_GET["mq"]=="editar"){
					   echo "<h3><i class='fa fa-edit'></i> Editar Contenido</h3>Edita el contenido";
				   }else if(isset($_GET["m"])&& $_GET["m"]=="add"){
					   echo "<h3><i class='fa fa-edit'></i> Agregar página </h3>Agregar página";
				   }else{
					echo "<div class='row'><div class='col-md-8'>					 
					<h3><i class='fa fa-table'></i> Lista de Paginas </h3>Listado completo de páginas del sitio web";
					echo '</div>
					<div class="col-md-4">';
					echo '<div class="btn-group btn-group-sm pull-right"><a href="panel.php?mod=panel&op=19&m=add" id="agregarProd" name="agregarProd" class="btn btn-success"><i class="fa fa-plus"></i> Agregar página</a></div>';
					echo "</div>";
	   
					echo '</div>';
				   }

				}else if($op==244){					
						echo "<h3><i class='fa fa-drivers-license-o'></i> Acerca de la licencia </h3>Acerca de";										
			   }else if($op==18){
				   if(isset($_GET["mq"]) && $_GET["mq"]=="editar"){
					   echo "<h3><i class='fa fa-edit'></i> Editar </h3>Edita un aviso";
				   }else{
				   	 echo "<h3><i class='fa fa-table'></i> Menús </h3>Menu de la página web";
				   }
			   }else if($op==61){
				   if(isset($_GET["mq"]) && $_GET["mq"]=="editar"){
					   echo "<h3><i class='fa fa-edit'></i> Editar </h3>Edita un aviso en vitrina";
				   }else{
				   	 echo "<h3><i class='fa fa-table'></i> Agregar Slider</h3>Listado de Sliders";
				   }
			   }else if($op==9){
				    if(isset($_GET["mq"]) && $_GET["mq"]=="editar"){
					   echo "<h3><i class='fa fa-edit'></i> Editar </h3>Edita una solicitud";
				   }else{
				   	 echo "<h3><i class='fa fa-table'></i> Configuración General</h3>Configuración general de la página web";
				   }
			   }else if($op==13){
				   	 echo "<h3><i class='fa fa-edit'></i> Agregar Categoria</h3>Ingresa una categoría";
			   }else if($op==14){
				   	 echo "<h3><i class='fa fa-area-chart'></i> Monitor de visitas</h3>Ultimos visitantes";
			   }else if($op==15){
				   	 echo "<h3><i class='fa fa-cogs'></i> Configuración General</h3>Información de configuración de la página web";
			   }else if($op==16){
				   	 echo "<h3><i class='fa fa-table'></i> Monitor de Visitas</h3>Usuarios Visitantes";
				}else if($op==35){
					if(isset($_GET["mq"]) && $_GET["mq"]=="editar"){
					   echo "<h3><i class='fa fa-edit'></i> Editar </h3>Edita un usuario";
				   }else{
				   	 echo "<h3><i class='fa fa-table'></i> Usuarios Registrados</h3>Lista de usuarios registrados";
				   }
				}else if($op==36){
				   	 echo "<h3><i class='fa fa-table'></i> Gestionar Usuarios</h3>Gestinar usuarios registrados";
				}else if($op==93){
				   	 echo "<h3><i class='fa fa-edit'></i> Agregar Contenido</h3>Contenido de la página web";
				}else if($op==94){
					 if(isset($_GET["mq"]) && $_GET["mq"]=="editar"){
					   echo "<h3><i class='fa fa-edit'></i> Editar Contenido </h3>Edita contenido";
				   }else{
				   	 echo "<h3><i class='fa fa-table'></i> Gestionar Contenido</h3>Gestionar contenido de la página web";
				   }
				}else if($op==245){
					echo "<h3><i class='fa fa-table'></i> Seguridad y contraseña";

				}else if($op==355){
					if(isset($_GET["mq"]) && $_GET["mq"]=="editar"){
					   echo "<h3><i class='fa fa-edit'></i> Editar Tienda </h3>Edita una tienda";
				   }else{
				   	 echo "<h3><i class='fa fa-table'></i> Lista de Productos </h3>Lista de tiendas";
				   }
				}else if($op==41){
					if(isset($_GET["mq"]) && $_GET["mq"]=="editar"){
					   echo "<h3><i class='fa fa-edit'></i> Productos </h3>Productos";
				   }else if ($_GET["m"]=="addProd") {
					echo "<h3><i class='fa fa-edit'></i> Agregar Productos </h3> Agrega un producto nuevo";
				   }else{
					echo "<div class='row'><div class='col-md-8'>					 
					<h3><i class='fa fa-table'></i> Lista de productos </h3>Listado completo de productos";
					echo '</div>
					<div class="col-md-4">';
					echo '<div class="btn-group btn-group-sm pull-right"><a href="panel.php?op=41&m=addProd&mod=panel" id="agregarProd" name="agregarProd" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Productos</a></div>';
					echo "</div>";
	   
					echo '</div>';
				   }
				}else if($op==55){					
					   echo "<h3><i class='fa fa-edit'></i>Acerca de</h3> Acerca de";
				}else if($op==144){					
						echo "<h3><i class='fa fa-edit'></i> Perfil de administrador</h3> Información sobre tu cuenta de administrador";
				}else if($op==14451){
						if(isset($_GET["mq"]) && $_GET["mq"]=="editar"){
							echo "<h3><i class='fa fa-edit'></i> Editar Contenido</h3>Edita el contenido";
						}else if(isset($_GET["m"])&& $_GET["m"]=="add"){
							echo "<h3><i class='fa fa-edit'></i> Lista de Agentes </h3>Listado de agentes registrados";
						}else{
						 echo "<div class='row'><div class='col-md-8'>					 
						 <h3><i class='fa fa-table'></i> Lista de Agentes </h3>Listado completo de páginas del sitio web";
						 echo '</div>
						 <div class="col-md-4">';
						 echo '<div class="btn-group btn-group-sm pull-right"><a href="panel.php?mod=panel&op=19&m=add" id="agregarProd" name="agregarProd" class="btn btn-success"><i class="fa fa-plus"></i> Listado de Agentes/a></div>';
						 echo "</div>";
			
						 echo '</div>';
						}


			   }else{
				   echo "<h3><i class='fa fa-table'></i> Panel de Control</h3>Panel de control";
			   }
		
		 }else{
			 echo "<h3><i class='fa fa-table'></i> Panel de Control</h3>Panel de control";
		 }
		 ?>
										</div>											
										<div class="card-body" style='padding:10px; margin:0px;'>											
											  <?php 
											  if(!isset($_GET["mod"])){
												  //$miOrbis->bloquePropiedades();
											  }else{
											  $miOrbis->panelBoot();
											  }
											  ?>											
											  
										</div>														
									</div>	
									</div>
							</div>
							<?php 
								}
							?>
            </div>
			<!-- END container-fluid -->

		</div>
		<!-- END content -->

    </div>
	<!-- END content-page -->
    
	<footer class="footer">
		<span class="text-right">
		Copyright 2024 <a target="_blank" href="https://www.programacionwebchile.cl">Progwebchile.cl</a>
		</span>
		<span class="float-right">
		Cms-Prop 3.0
		</span>
	</footer>

 <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Información del sistema</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            ¿Desea eliminar el registro? <input type="hidden" name="m" value="" id="m"/>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <button type="button" id="borrar1" name="borrar1" class="btn btn-primary">Borrar</button>
          </div>
        </div>
      </div>
    </div> 
      
</div>
 
<!-- App js -->
<script src="assets/js/pikeadmin.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script> 		
	<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	


 

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
  
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-173010-60"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-173010-60');
</script>
</body>
</html>