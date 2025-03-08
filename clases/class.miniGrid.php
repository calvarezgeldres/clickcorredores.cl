<?php
/*
Fecha:20/10/2020
Autor:Luis Olguin
Descripción: clase para materialize
*/ 
ob_start();
 
header('Content-Type: text/html; charset=UTF-8');

include_once("./clases/class.coneccion.php");
require_once("./clases/class.paginator.php");

class miniGrid extends coneccion{
    private $sql;
    private $paginator;
    private $numReg;
    private $index;
    private $campos;
    private $tamCol;
    private $campoIndice;
    private $boton;
    private $nomTabla;
    private $campoFoto;
    private $opciones;
    private $listaColores;
    private $producto;
    public $link;
    private $tabla;
    public function __construct($numReg=false,$index=false,$campoIndice=false,$campoFoto=false,$opciones=false,$prod=false){
        $this->opciones=$opciones;
        $this->producto=$prod;
        $this->campoFoto=$campoFoto;
        $this->campoIndice=$campoIndice; // para borrar y editar
        $this->numReg=$numReg;
        $this->index=$index;
        
    
    } 
     
    public function jquery(){
        echo '<script src="./js/jquery-1.9.1.js"></script>';
        echo '<script>';
        
        echo '$(document).ready(function(){          
          
           
          
              $("input[name=opciones]").change(function(){';
              
              echo "
	   	$('input[type=checkbox]').each( function() {";
           echo '			
			if($("input[name=opciones]:checked").length == 1){
				this.checked = true;
			} else {
				this.checked = false;
			}
		});
	}); 
            return(false);
        });';
        echo '</script>';
    }
     
    public function asignarCampos($campos,$tamCol,$sql,$tabla){
        $this->sql=$sql;
        $this->campos=$campos;
        $this->tamCol=$tamCol;
        $this->tabla=$tabla;
    }
    public function devolverColumnas(){
        $numCol=count($this->tamCol);
        return($numCol);
    }
    public function formatoNumerico($num){
      $n=number_format($num, 0,",",".");
      return($n);
    }
    public function devolverOperacion($id){
      if($id==1){
        $k="Venta";
      }else if($id==2){
        $k="Arriendo";
      }
      return($k);
    }
    public function devolverTipoProp($id){
      $arrSel3=array(1=>"Casas",
      2=>"departamento",
   3=>"Oficina",
   4=>"Agrícola",
   5=>"Bodega",
   6=>"Comercial",
   7=>"Estacionamiento",
   8=>"Galpón",
   9=>"Industrial",
   10=>"Terreno",
   11=>"Turístico"					
  );
      return($arrSel3[$id]);
    }
    public function devolverCiudad($id){
      $this->link=$this->conectar();
      $sql="select* from mm_ciudad where idCiudad='".$id."'";
      $query=mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
      $row=mysqli_fetch_array($query);
      $this->cerrar();
      return($row["ciudad"]);
    }
    public function numFotos($idProp){   
      $this->link=$this->conectar();   
      $sql="select count(*) as total from mm_cape_fotos where idProp='".$idProp."'";
      $q=mysqli_query($this->link,$sql);
      $r=mysqli_fetch_array($q);      
      $total=$r["total"];
      return($total);
    }
    public function devolverFoto($idProp,$campo){
      $this->link=$this->conectar();
      if($this->tabla!="mm_propiedad"){
        $s="select ".$campo." from ".$this->tabla." where ".$this->campoIndice."='".$idProp."' order by portada desc";    
      }else{
        $s="select ".$campo." from mm_cape_fotos where idProp='".$idProp."' order by portada desc";
      }
   
      $q=mysqli_query($this->link,$s);
      $r=mysqli_fetch_array($q);      
      $foto=$r[$campo];     
      return($foto);
    }
    public function devolverFtp(){
      $this->link=$this->conectar();
      $sql="select ftp from mm_coti_datos";
      $q=mysqli_query($this->link,$sql);     
      $r=mysqli_fetch_array($q); 
 
      $d=$r["ftp"];
       
       return($d);
    }
    public function contarPapelera(){
      $this->link=$this->conectar();
      $sql="select count(*) as total from mm_propiedad where papelera=1";
      $q=mysqli_query($this->link,$sql);
      $r=mysqli_fetch_array($q);
      $t=$r["total"];
      return($t);
    }
    public function contarPropiedades(){
      $this->link=$this->conectar();
      $sql="select count(*) as total from mm_propiedad where papelera=0";
      $q=mysqli_query($this->link,$sql);
      $r=mysqli_fetch_array($q);
      $t=$r["total"];
      return($t);
    }
    public function addAlert($titulo,$mensaje,$tipo){        
      echo '<div class="alert alert-';
       if($tipo==1){
           echo "primary";
       }else if($tipo==2){
           echo "secondary";
       }else if($tipo==3){
           echo "success ";
       }else if($tipo==4){
           echo "danger";
       }else if($tipo==5){
           echo " warning";
       }else if($tipo==6){
           echo "info";
       }else if($tipo==7){
           echo "light ";
       }else if($tipo==8){
           echo "dark";
       }
      echo ' alert-dismissible fade show"  data-dismiss="alert" aria-label="Close" role="alert">
      <strong><i class="fas fa-exclamation-circle"></i> '.$titulo.'!</strong> '.$mensaje.'
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
          </div>';
 
   }

   public function devolverCorredora($id){
		$sql="select nomEmpresa,nombre from registro where idReg='".$id."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
	 
		if(empty($r["nomEmpresa"])){
			$n=$r["nombre"]."&nbsp;".$r["apellido"];			
		}else{
			$n=$r["nomEmpresa"];
		}
		return($n);
	}

    public function desplegarDatos(){
      $this->link=$this->conectar();
      $ftp=$this->devolverFtp();
     
        $this->paginator=new paginator(24,24);
        $this->paginator->agregarConsulta($this->sql);  
  if($_GET["op"]!=9 && $_GET["op"]!=14 && $_GET["op"]!=19  && $_GET["op"]!=6  && $_GET["op"]!=445 ){
        if(isset($_POST["accionMasiva"]) && $_POST["accionMasiva"]==1){          
          if(isset($_POST["sel"])){
            foreach($_POST["sel"] as $c=>$v){
              $sql1="update mm_propiedad set papelera=1 where idProp='".$v."'";
             mysqli_query($this->link,$sql1);              
            }
          }
          header("location:panel.php?mod=panel&op=4&ms=1");
            exit;
        }else  if(isset($_POST["accionMasiva"]) && $_POST["accionMasiva"]==3){       
          if(isset($_POST["sel"])){
            foreach($_POST["sel"] as $c=>$v){
              $sql1="update mm_propiedad set papelera=0 where idProp='".$v."'";
             mysqli_query($this->link,$sql1);              
            }
            header("location:panel.php?mod=panel&op=4&ms=0");
            exit;
          }
          
        }else  if(isset($_POST["accionMasiva"]) && $_POST["accionMasiva"]==2){       
          $lista=implode(",",$_POST["sel"]);
          $sql="delete from mm_propiedad where idProp in (".$lista.")";
         mysqli_query($this->link,$sql);      
          header("location:panel.php?mod=panel&op=4&ms=3");
          exit;
        }       
     
        if(isset($_GET["ms"]) && $_GET["ms"]==1 ){          
            $this->addAlert("Información","Propiedad se movio a la papelera",1);                      
        }else  if(isset($_GET["ms"]) && $_GET["ms"]==0){
          $this->addAlert("Información","Propiedad sacada de la papelera",1);            
        }else  if(isset($_GET["ms"]) && $_GET["ms"]==3){          
          $this->addAlert("Información","Propiedad se ha borrado con exito !!",1);                      
        }
      
       
        echo "<div class='row' style='margin-bottom:5px; margin-left:0; margin-top:5px;'>";

echo "<div class='col-md-1' align='left'>
        <button role='button' id='sel' name='sel' class='btn btn-primary btn-sm'>Seleccionar</button>
      </div>";

// Filtro de corredora
echo "<div class='col-md-2'>";
$sql77 = "SELECT DISTINCT idCorredora FROM mm_propiedad ORDER BY idCorredora DESC";
$q77 = mysqli_query($this->link, $sql77);

echo "<select name='filtrar' id='filtrar' onchange='filtrar();' class='form-select form-select-sm'>
        <option value='0' selected>Filtrar Corredora</option>
        <option value='1'>Ver Todos</option>";
        
while ($r99 = mysqli_fetch_array($q77)) {
    if (!empty($r99["idCorredora"])) {
        echo "<option value='" . $r99["idCorredora"] . "'>" . $this->devolverCorredora($r99["idCorredora"]) . "</option>";
    }
}
echo "</select></div>";
echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Modal Loading</title>
</head>
<body>

 

<!-- Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-labelledby="loadingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p>Cargando...</p>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $("#filtrar").change(function() {
        // Muestra el modal de carga
        var loadingModal = new bootstrap.Modal(document.getElementById("loadingModal"), {
            backdrop: "static",
            keyboard: false
        });
        loadingModal.show();

        // Envía el formulario
        $("#form1").submit();
    });
</script>

</body>
</html>
';
// Búsqueda
echo "<div class='col-md-4'>
        <table width='100%'>
            <tr>
                <td>
                    <input type='text' name='palabra' id='palabra' class='form-control form-control-sm' placeholder='Ingresar una palabra' value=''/>
                </td>
                <td>
                    <button class='btn btn-primary btn-sm'><i class='fas fa-search'></i>&nbsp; Buscar</button>
                </td>
            </tr>
        </table>
      </div>";

// Acciones masivas
echo "<div class='col-md-3'>
        <table width='100%'>
            <tr>
                <td>
                    <select name='accionMasiva' id='accionMasiva' style='width:100%;' class='form-control form-control-sm'>
                        <option value='0' selected='selected'>Acciones masivas</option>";
                        if (isset($_GET["v"]) && $_GET["v"] == 1) {
                            echo "<option value='3'>Sacar de la papelera</option>
                                  <option value='2'>Borrar</option>";
                        } else {
                            echo "<option value='1'>Mover a papelera</option>";
                        }
echo "          </select>
                </td>
                <td>
                    <button class='btn btn-primary btn-sm'>Aplicar</button>
                </td>
            </tr>
        </table>
      </div>";

// Enlace a propiedades o papelera
echo "<div class='col-md-2' align='right' style='padding-right:20px;'>";
if (isset($_GET["v"]) && $_GET["v"] == 1) {
    echo "<a href='panel.php?mod=panel&op=4&v=2' role='button' class='btn btn-primary btn-sm'><i class='fas fa-home'></i>&nbsp;(" . $this->contarPropiedades() . ") Ver&nbsp;Propiedades</a>";
} else {
    echo "<a href='panel.php?mod=panel&op=4&v=1' role='button' class='btn btn-primary btn-sm'><i class='far fa-trash-alt'></i> (" . $this->contarPapelera() . ") Papelera</a>";
}
echo "</div>";

echo "</div>"; // Cierre de la fila

      }
        echo '<div class="col-md-12 table-responsive-sm">';      
        echo '<table class="table table-sm">
        <thead class="thead-light">';
        
          echo '<tr>';
          echo "<th>";
          echo "<input type='checkbox' name='check' id='check' value=''/>";
          echo "</th>";
          foreach($this->campos as $clave=>$valor){
            echo "<th width='".$this->tamCol[$k]."%'>".ucfirst($clave)."</th>";
            $k++;
          }
 
   
          echo "<th>N°Fotos</th>";
          if(!isset($_GET["op"]) || isset($_GET["op"]) && $_GET["op"]==4){
            echo "<th>Estado</th>";
          }
          echo "<th>Acciones</th>";
          echo '</tr>';
          echo '
          </thead>
        <tbody>';    
        $this->paginator->estableceIndex($this->index);
        $total=$this->paginator->obtenerTotalReg();    
        $numCol=$this->devolverColumnas()+3;
        if($total==0){
            echo "<tr>
            
            <td colspan='".$numCol."'>Sin propiedades</td></tr>";
        }else{      
            while($row=$this->paginator->devolverResultados()){
                echo '<tr>';    
                echo "<td>";
                echo "<input type='checkbox' name='sel[]' id='sel[]' value='".$row["idProp"]."'/>";
                echo "</td>";
                foreach($this->campos as $clave=>$valor){       
                  
                  $idProp=$row[$this->campoIndice];
                  
                  if($clave=="ruta" || $clave=="foto"){
                    $foto=$this->devolverFoto($idProp,$clave);					 

                    echo "<td>";
                   
                    if(empty($foto)){
                      echo "<img src='./imagen/sinfoto.png' height='70' width='70'/>";
                    }else{
                      if(preg_match("/https/",$foto)){
                        echo "<img src='".$foto."' height='60' width='80'/>";
                      }else{
                        echo "<img src='./upload/".$foto."' height='90' width='110' style='padding:5px;' />";
                      }
                      
                    }
                    echo "</td>";
                  
                  }else if($clave=="fecha"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">'.date("d-m-Y",$row[$clave]).'</span>';
                    echo "</td>";
                  }else if($clave=="operacion"){
                      echo "<td>";
                      echo '<span style="font-family:arial; font-size:14px !important;">'.$this->devolverOperacion($row[$clave]).'</span>';
                      echo "</td>";
                    }else if($clave=="titulo"){
                      echo '<td>
                          
                      <div><span style="font-family:arial; font-weight:bold;font-size:12px !important;">'.substr($row[$clave],0,90).'</span></div>';
                    
                      echo "<div>";
                      echo "<span style='font-size:13px;'>";                     
                      echo "<i class='fas fa-calendar-alt'></i> Publicado: ".date("d-m-Y",$row["fecha"])."&nbsp;&nbsp;|&nbsp;&nbsp ";
                      echo "<i class='fas fa-globe'></i> Visitantes: ";
                      if(empty($row["numVisitas"]) && $row["numVisitas"]==0){
                        echo "0 ";
                      }else{
                        echo $row["numVisitas"];  
                      }   
                      echo "&nbsp;&nbsp;";
                      echo "</span>";
                      echo "</div>";
                      echo "<div  style='font-size:13px;'><i class='fas fa-user'></i> Corredora : <span style='font-weight:600;'>".$this->devolverCorredora($row["idCorredora"])."</span></div>";
                      echo "<div  style='padding-bottom:5px;padding-top:5px;'>";
                      echo '<span class="badge bg-success" style="color:white !important;">';
                      echo $this->devolverOperacion($row["operacion"]);
                      echo '</span>&nbsp;';
                      echo '<span class="badge bg-primary" style="color:white !important;">';
                      echo $this->devolverTipoProp($row["tipoProp"]);
                      echo '</span>&nbsp;';
  
                      echo '<span class="badge bg-warning" style="color:white !important;">';
               
                      if($row["destacar"]=="Si"){
                        echo "Destacar";
                      }                            
                      echo '</span>';
                      

                      echo '&nbsp;';
               
                      if($row["estadoPublicacion"]==0){
                        echo '<span class="badge bg-success" style="color:white !important;">Publicado</span>';
                      }else if($row["estadoPublicacion"]==1){
                        echo '<span class="badge bg-danger" style="color:white !important;">En Revisión</span>';
                      }else{
                        echo '<span class="badge bg-danger" style="color:white !important;">No Publicado</span>';
                      }                            
                      echo '</span>';


                      echo '</div>';
                      echo '</td>';

                  }else if($clave=="tipoProp"){
                      echo "<td>";
                      echo '<span style="font-family:arial; font-size:12x !important;">'.$this->devolverTipoProp($row[$clave]).'</span>';
                      echo "</td>";
                  
                  }else if($clave=="precio"){
                    echo "<td>";
                        echo '<span style="font-family:arial; font-size:14px;">';					 
                          if($row["precioUf"]==1){
                              echo '$'.$this->formatoNumerico($row[$clave]);
                          }else{ 
                              if(preg_match("/\./i",$row["precio"])){
                                  echo $row[$clave]." U.F";
                              }else{
                                  echo $this->formatoNumerico($row[$clave])." U.F";
                              }          
                          }
                      echo '</span>';
                      echo "</td>";
                  }else{                        
                    
                      echo '<td><span style="font-size:14px;">';
                      echo utf8_decode($row[$clave]);
                      echo '</span></td>';                    
                    }        
                }
             
                  
                echo "<td>";
                echo $this->numFotos($row["idProp"]);
                echo "</td>";
                
                if(!isset($_GET["op"]) || isset($_GET["op"]) && $_GET["op"]==4){

                  echo "<td>";
                  $m5=$row["estadoPublicacion"];
                  $idProp = $row["idProp"]; // 
                  echo "<select name='estado' id='estado_$idProp' class='form-select form-select-sm' onchange='cambiarEstado(this.value, $idProp);'>";
                  
if ($m5 == 1) {
    echo "<option value='1' selected>En Revisión</option>";
    echo "<option value='0'>Aprobar</option>";
    echo "<option value='2'>No Aprobar</option>";
} else if ($m5 == 0) {
    echo "<option value='0' selected>Aprobar</option>";
    echo "<option value='1'>En Revisión</option>";
    echo "<option value='2'>No Aprobar</option>";
} else {
    echo "<option value='0'>Aprobar</option>";
    echo "<option value='1'>En Revisión</option>";
    echo "<option value='2' selected>No Aprobar</option>";
}

echo "</select>";
 
                }

                
                echo "<td>";
                echo '<div>';
                echo '<div class="dropdown">
                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Accion
                </button>
                <ul class="dropdown-menu">';
                if($_GET["op"]==4){
                  if($ftp==0){
                    echo '<li><a href="'.$this->index."&op=4&mq=agregar&idq=".$row[$this->campoIndice].'"  class="dropdown-item"><i class="fas fa-images"></i> Agregar Fotos</a></li>';
                  }else if($ftp==1){
                    echo '<li><a href="'.$this->index."&op=4&mq=agregarFtp&idq=".$row[$this->campoIndice].'"  class="dropdown-item"><i class="fas fa-images"></i>';
                    if($this->numFotos($row["idProp"])==0){
                      echo " Agregar Fotos";
                    }else{
                      echo " Editar Fotos";
                    }
                    echo '</a></li>';
                  }else {
                    echo '<li><a href="'.$this->index."&op=4&mq=agregar&idq=".$row[$this->campoIndice].'"  class="dropdown-item"><i class="fas fa-images"></i> Agregar Fotos</a></li>';
                    echo '<li><a href="'.$this->index."&op=4&mq=agregarAws&idq=".$row[$this->campoIndice].'"  class="dropdown-item"><i class="fas fa-images"></i>';
                    if($this->numFotos($row["idProp"])==0){
                      echo " Agregar Fotos";
                    }else{
                      echo " Editar Fotos";
                    }
                    echo '</a></li>';
                  }
              }
              
              echo '<a class="dropdown-item" href="'.$this->index."&mq=editar&idq=".$row[$this->campoIndice].'">Editar</a>
                    <a class="dropdown-item" href="'.$this->index."&mq=borrar&idq=".$row[$this->campoIndice].'">Borrar</a>';

                echo '</ul>
              </div>';
                echo '</div>';
               
              
              
                echo '<script>
                window.setTimeout(function(){                                      
                  $(".alert").fadeTo(1500,0).slideDown(1000,function(){
                      $(this).remove();
                  });
               }, 1000);
                $(document).ready(function (e) {
                  $(".alert").alert();     
                  $("#sel").click(function(){
                    $("input[type=checkbox]").prop("checked", true);  
                    return(false);                
                 });
                      $("#exampleModal").on("show.bs.modal", function(e) {    
                           var id = $(e.relatedTarget).data().id;
                           var id=$("#m").attr("value",id);      			 
                      });
                      $("#borrar1").click(function(){
                        var idReg=$("#m").val();
                        $.ajax({
                          type:"post",
                          url: "tabla.php", 
                              data:"campo='.$this->campoIndice.'&tabla='.$this->tabla.'&idReg="+idReg,
                          success:function(datos){	                          	 			                              
                             $("#exampleModal").modal("toggle");
                             document.location="'.$this->index.'&msg=1";                             
                          },
                          error:function(e){
                            console.log(e);
                          }
                      }); 				
                        
                      });
                });
                   
                 
                </script>
               ';
                echo "</div>";
                echo "</td>";

                echo '</tr>';
               
            }

        }       
     
          echo '
        </tbody>
      </table>';
      
      echo ' 
      </div>';
      
      if($total>5){
        echo '<div style="margin-top:20px;">';
        $this->paginator->navegacion("sm");
        echo "</div>";
      }   
    }
    
    
     
}
?>