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
   public function devolverBanco($id){
    $arrSel = array(1=>
    "Banco De Chile - Edwards",
    "Banco Bice",
    "Banco Consorcio",
    "Banco del Estado de Chile",
    "Banco Do Brasil S.A.",
    "Banco Falabella",
    "Banco Internacional",
    "Banco Paris",
    "Banco Ripley",
    "Banco Santander",
    "Santander Office Banking",
    "Banco Security",
    "Banco Coopeuch",
    "Citibank",
    "BBVA",
    "BCI",
    "HSBC Bank",
    "Itau",
    "Itau-Corpbanca",
    "Scotiabank"
  );
  return($arrSel[$id]);
   }
   public function devolverTipoCuenta($id){
    $arrSel1=array(1=>"Cuenta Corriente","Cuenta Vista","Cuenta de Ahorro");
    return($arrSel1[$id]);
   }
   public function devolverPropietario($id){
    $sql="select* from mm_propietarios where idPropietario='".$id."'";
    $q=mysqli_query($this->link,$sql);
    $r=mysqli_fetch_array($q);
    $nombre=ucfirst($r["nombre"]."&nbsp;".$r["apellido"]);
    return($nombre);
   }
   public function devolverIdPropi($idProp){
      $sql="select idPropietario from mm_propiedad1 where idProp='".$idProp."'";
      $q=mysqli_query($this->link,$sql);
      $r=mysqli_fetch_array($q);
      return($r["idPropietario"]);
   }
   public function devolverTipo($id){
    $arrSel2=array(1=>"Agua","Luz","Gas","Gastos Comunes");
    return($arrSel2[$id]);
   }
   public function devolverEmpresa($id,$idServ){
      
		if($idServ==1){
			$arraySel5 = array(1=>
				"Aguas Andinas",
				"Aguas Antofagasta",
				"Aguas Araucanía",
				"Aguas Chacabuco",
				"Aguas Cordillera",
				"Aguas Decima",
				"Aguas Los Guaicos",
				"Aguas Magallanes",
				"Aguas Manquehue",
				"Aguas Patagonia",
				"Aguas San Isidro",
				"Aguas San Pedro",
				"Aguas Santiago Poniente",
				"Aguas Sepra",
				"Aguas del Altiplano",
				"Aguas del Valle",
				"Essal",
				"Essbio",
				"Esval",
				"Nueva Atacama",
				"Nuevo Sur",
				"Otro",
				"Sacyr Agua Santiago",
				"Smapa"
			);
		}else if($idServ==2){
			$arraySel5 = array(1=>
				"CEC",
				"CGE",
				"Chilquinta",
				"Codiner",
				"Coopelan",
				"EEPA",
				"Edelaysen",
				"Edelmag",
				"Eléctrica Colina",
				"Enel",
				"Frontel",
				"Luz Casablanca",
				"Luz Linares",
				"Luz Litoral",
				"Luz Osorno",
				"Luz Parral",
				"Otro",
				"Saesa"
			);
		// luz
		}else if ($idServ==3){
		// gas
		$arraySel5 = array(1=>
			"Abastible",
			"Energas",
			"Gas Sur",
			"GasValpo",
			"Gasco",
			"Lipigas",
			"Metrogas",
			"Otro"
		);
		}
    return($arraySel5[$id]);
   }
   public function devolverEstado($idProp){
    $sql="select count(*) as total  from mm_arriendos where idProp='".$idProp."'";
    
    $q=mysqli_query($this->link,$sql);
    $r=mysqli_fetch_array($q);
    $t=$r["total"];
    if($t==0){
      return(0);
    }else{
      return(1);
    }
   }



   public function desplegarGarantias(){

 


    $this->link=$this->conectar();
 
      $this->paginator=new paginator(24,24);
      $this->paginator->agregarConsulta($this->sql);  

      echo '<div class="col-md-12 table-responsive-sm">';      
      echo '<table class="table table-bordered table-sm">
      <thead class="table-light" style="font-size:14px !important;">';
      
        echo '<tr>';
        
        foreach($this->campos as $clave=>$valor){
          echo "<th width='".$this->tamCol[$k]."%'>".ucfirst($clave)."</th>";
          $k++;
        }
        if($_GET["op"]!=20 && $_GET["op"]!=14 && $_GET["op"] !=23){ 
        echo "<th>Acciones</th>";
        }
        echo '</tr>';
        echo '
        </thead>
      <tbody>';    
      $this->paginator->estableceIndex($this->index);
      $total=$this->paginator->obtenerTotalReg();    
      $numCol=$this->devolverColumnas()+3;
      if($total==0){
          echo "<tr>
       
          <td colspan='".$numCol."'>Sin Resultados</td></tr>";
      }else{      

        

          while($row=$this->paginator->devolverResultados()){
            $abono[]=$row["abono"];
            $des[]=$row["descuento"];
              echo '<tr>';    
           
              foreach($this->campos as $clave=>$valor){       
                
           
                 if($clave=="fecha"){
                 
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">'.date("m-d-Y",strtotime($row[$clave])).'</span>';
                    echo "</td>";
                  
                }else if($clave=="abono"){
                  echo "<td>";
                  echo '<span style="font-family:arial; font-size:14px !important;">$'.$this->formatoNumerico($row[$clave]).'</span>';
                  echo "</td>";
                }else if($clave=="descuento"){
                  echo "<td>";
                  echo '<span style="font-family:arial; font-size:14px !important;">$'.$this->formatoNumerico($row[$clave]).'</span>';
                  echo "</td>";
                }else{                        
                  
                    echo '<td><span style="font-size:14px;">';
                    echo $row[$clave];
                    echo '</span></td>';                    
                  }        
              }
              
           
              if($_GET["op"]!=20 && $_GET["op"] !=14 && $_GET["op"] !=23){ 
            
              echo "<td>";
              echo '<div>';
              if($row["concepto"]!="Mes de Garantia"){              
              echo '<div class="dropdown">
              <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Acciones
              </button>
              <ul class="dropdown-menu">';
              
            
            echo '<a class="dropdown-item" href="'.$this->index."&mq=editar&idq=".$row[$this->campoIndice].'">Editar</a>
                  <a class="dropdown-item" href="'.$this->index."&mq=borrar&idq=".$row[$this->campoIndice].'">Borrar</a>';

              echo '</ul>
            </div>';
              }
              echo '</div>';
             
             
           
              echo "</div>";
              echo "</td>";
            }
            
              echo '</tr>';
             
            
          }
          echo "<tr>";
          echo "<td colspan=2>&nbsp;</td>";
          echo "<td>$ ".$this->formatoNumerico(array_sum($abono))."</td>";
          echo "<td>$ ".$this->formatoNumerico(array_sum($des))."</td>";
          echo "<td>&nbsp;</td>";
        echo "</tr>";
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
    public function desplegarDatos(){
      
      $this->link=$this->conectar();
      $ftp=$this->devolverFtp();
     
        $this->paginator=new paginator(24,24);
        $this->paginator->agregarConsulta($this->sql);  
 
        echo '<div class="col-md-12 table-responsive-sm">';      
        echo '<table class="table table-bordered table-sm">
        <thead class="table-light" style="font-size:14px !important;">';
        
          echo '<tr>';
          
          foreach($this->campos as $clave=>$valor){
            echo "<th width='".$this->tamCol[$k]."%'>".ucfirst($clave)."</th>";
            $k++;
          }
          if($_GET["op"]!=20 && $_GET["op"]!=14 && $_GET["op"] !=23){ 
          echo "<th>Acciones</th>";
          }
          echo '</tr>';
          echo '
          </thead>
        <tbody>';    
        $this->paginator->estableceIndex($this->index);
        $total=$this->paginator->obtenerTotalReg();    
        $numCol=$this->devolverColumnas()+3;
        if($total==0){
            echo "<tr>
         
            <td colspan='".$numCol."'>No se han agregado reajustes a este arriendo, se mantiene el valor original del arriendo</td></tr>";
        }else{      
            while($row=$this->paginator->devolverResultados()){
                echo '<tr>';    
             
                foreach($this->campos as $clave=>$valor){       
                
                  $idProp=$row[$this->campoIndice];
                  if($clave=="banco"){
                    echo '<td><span style="font-size:14px;">';
                    echo $this->devolverBanco($row["banco"]);
                    echo '</span></td>'; 
      
                  }else if($clave=="estado"){                      
                    echo '<td><span style="font-size:14px;">';
                    if($row["estado"]==0){
                      echo "Pendiente ";
                    }else if($row["estado"]==1){
                      echo "Moroso";
                    }else{
                      echo "Pagado";
                    }
                    echo '</span></td>'; 
                    
                  }else if($clave=="montoPagar"){                      
                    echo '<td><span style="font-size:14px;">';
                    echo "$ ".$this->formatoNumerico($row["montoPagar"]);
                    echo '</span></td>'; 
                  }else if($clave=="porcentaje"){                      
                    echo '<td><span style="font-size:14px;">';
                    echo $row["porcentaje"]."%";
                    echo '</span></td>'; 
                    
                  }else if($clave=="precioReajustado"){                      
                    echo '<td><span style="font-size:14px;">$ ';
                    echo $this->formatoNumerico($row[$clave]);
                    echo '</span></td>'; 
                  }else if($clave=="precioOriginalArriendo"){                      
                    echo '<td><span style="font-size:14px;">$ ';
                    echo $this->formatoNumerico($row[$clave]);
                    echo '</span></td>'; 
                  }else if($clave=="garantia"){                      
                    echo '<td><span style="font-size:14px;">';
                    echo "$ ".$this->formatoNumerico($row["garantia"]);
                    echo '</span></td>'; 
                  }else if($clave=="propiedad"){  
                    
                    echo '<td><span style="font-size:14px;">';
                    echo "<a href='panel.php?op=15&idProp=".$row["idProp"]."'>".$this->devolverPropiedad($row["idProp"])."</a>";
                    echo '</span></td>'; 

                  }else if($clave=="tipo"){
                      echo '<td><span style="font-size:14px;">';
                      echo $this->devolverTipo($row["tipo"]);
                      echo '</span></td>'; 
                    }else if($clave=="Estado"){
                      echo '<td><span style="font-size:14px;">';
                      $estado=$this->devolverEstado($row["idProp"]);
                     
                      if($estado==0){
                        echo '<span class="badge text-bg-info">Disponible</span>';
                      }else{
                        echo '<span class="badge text-bg-success">Arrendada</span>';
                      }
                      
                      echo '</span></td>'; 

                    }else if($clave=="empresa"){
                      echo '<td><span style="font-size:14px;">';
                      echo $this->devolverEmpresa($row["empresa"],$row["tipo"]);
                      echo '</span></td>'; 
                  }else if($clave=="tipoCuenta"){
                      echo '<td><span style="font-size:14px;">';
                      echo $this->devolverTipoCuenta($row["tipoCuenta"]);
                      echo '</span></td>';     
                  }else if($clave=="direccionProp"){
                        echo '<td><span style="font-size:14px;">';
                        echo "<a href='panel.php?op=15&idProp=".$row["idProp"]."'>";
                        echo $row["direccionProp"];                        
                        echo "</a>";
                        echo '</span></td>';  
                  }else if($clave=="idProp"){
                        echo '<td><span style="font-size:14px;">';
                        echo "<a href='panel.php?op=15&idProp=".$row["idProp"]."'>";
                        echo "Ficha #".$row["idProp"];                        
                        echo "</a>";
                        echo '</span></td>';     
                    }else if($clave=="fechaInicio"){
                        echo '<td><span style="font-size:14px;">';
                        $fecha=date("d-m-Y",$row["fechaInicio"]);
                        echo $this->mesAno($fecha);
                        echo '</span></td>';    

                      }else if($clave=="Direccion"){
                        echo '<td><span style="font-size:14px;">';
                        echo "<a href='panel.php?op=15&idProp=".$row["idProp"]."'>";
                        echo $this->devolverPropiedad($row["idProp"]);
                        echo "</a>";
                        echo '</span></td>';     
                  }else if($clave=="titulo"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:12x !important;">';
                    echo '<a href="panel.php?op=15&idProp='.$row["idProp"].'">'.$row["titulo"]."</a>";
                    echo '</span>';
                    echo "</td>";
                  }else if($clave=="Propietario"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">';
                    echo "<a href='panel.php?op=14&idPropi=".$row["idPropietario"]."'>";
                    echo $this->devolverPropietario($row["idPropietario"]);
                    echo "</a>";
                    echo '</span>';
                    echo "</td>";
                 
                  }else if($clave=="fecha"){
                    if($this->campoIndice=="idGarantia" || $this->campoIndice=="idDet"){
                      echo "<td>";
                      echo '<span style="font-family:arial; font-size:14px !important;">'.date("m-d-Y",strtotime($row[$clave])).'</span>';
                      echo "</td>";
                    }else{
                      echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">'.date("d-m-Y",$row[$clave]).'</span>';
                    echo "</td>";
                    }
                  }else if($clave=="abono"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">$'.$this->formatoNumerico($row[$clave]).'</span>';
                    echo "</td>";

                  }else if($clave=="operacion"){
                      echo "<td>";
                      echo '<span style="font-family:arial; font-size:14px !important;">'.$this->devolverOperacion($row[$clave]).'</span>';
                      echo "</td>";
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
                      echo utf8_encode($row[$clave]);
                      echo '</span></td>';                    
                    }        
                }
                
             
                if($_GET["op"]!=20 && $_GET["op"] !=14 && $_GET["op"] !=23){ 
              
                echo "<td>";
                echo '<div>';
                echo '<div class="dropdown">
                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Acciones
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
              if(isset($_GET["op"]) && $_GET["op"]!=26){
                echo '<a class="dropdown-item" href="'.$this->index."&mq=editar&idq=".$row[$this->campoIndice].'">Editar</a>';
              }
              echo '<a class="dropdown-item" href="'.$this->index."&mq=borrar&idq=".$row[$this->campoIndice].'">Borrar</a>';

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
              }
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



public function mesAno($fecha){
  $fecha_texto = $fecha;
  $dateObj = DateTime::createFromFormat('d-m-Y', $fecha_texto);
  setlocale(LC_TIME, 'es_ES');
  $mes = strftime('%B', $dateObj->getTimestamp());
  $año = $dateObj->format('Y');
  $cad=ucfirst($mes)."&nbsp;".$año;
  return($cad);
}

    public function desplegarPagos(){
      
      $this->link=$this->conectar();
      $ftp=$this->devolverFtp();
     
        $this->paginator=new paginator(24,24);
        $this->paginator->agregarConsulta($this->sql);  
 
        echo '<div class="col-md-12 table-responsive-sm">';      
        echo '<table class="table table-bordered table-sm">
        <thead class="table-light" style="font-size:14px !important;">';
        
          echo '<tr>';
          
          foreach($this->campos as $clave=>$valor){
            echo "<th width='".$this->tamCol[$k]."%'>".ucfirst($clave)."</th>";
            $k++;
          }
        
          echo '</tr>';
          echo '
          </thead>
        <tbody>';    
        $this->paginator->estableceIndex($this->index);
        $total=$this->paginator->obtenerTotalReg();    
        $numCol=$this->devolverColumnas()+3;
        if($total==0){
            echo "<tr>
         
            <td colspan='".$numCol."'>Sin Resultados</td></tr>";
        }else{      
            while($row=$this->paginator->devolverResultados()){
                echo '<tr>';    
             
                foreach($this->campos as $clave=>$valor){       
                  
                  $idProp=$row[$this->campoIndice];
                  if($clave=="banco"){
                    echo '<td><span style="font-size:14px;">';
                    echo $this->devolverBanco($row["banco"]);
                    echo '</span></td>'; 
      
                  }else if($clave=="estado"){                      
                    $e=$row["estado"];
                    
                    echo '<td><span style="font-size:14px;">';
                    if($e==0){
                      echo '<span class="badge text-bg-primary">Pendiente</span>';
                    }else if($e==1){
                      echo '<span class="badge text-bg-info">Proceso</span>';
                    }else if($e==2){
                      echo '<span class="badge text-bg-success">Pagada</span>';
                    }else if($e==3){
                      echo '<span class="badge text-bg-danger">En Mora</span>';
                    }else if($e==4){
                      echo '<span class="badge text-bg-primary">Indeterminada</span>';
                    }
                    
                    echo '</span></td>'; 
                    
                  }else if($clave=="montoPagar"){                      
                    echo '<td><span style="font-size:14px;">';
                    echo "$ ".$this->formatoNumerico($row["montoPagar"]);
                    echo '</span></td>'; 
                  }else if($clave=="propiedad"){  
                    
                    echo '<td><span style="font-size:14px;">';
                    echo "<a href='panel.php?op=15&idProp=".$row["idProp"]."'>".$this->devolverPropiedad($row["idProp"])."</a>";
                    echo '</span></td>'; 

                  }else if($clave=="tipo"){
                      echo '<td><span style="font-size:14px;">';
                      echo $this->devolverTipo($row["tipo"]);
                      echo '</span></td>'; 
                    }else if($clave=="Estado"){
                      echo '<td><span style="font-size:14px;">';
                      $estado=$this->devolverEstado($row["idProp"]);
                     
                      if($estado==0){
                        echo '<span class="badge text-bg-info">Disponible</span>';
                      }else{
                        echo '<span class="badge text-bg-success">Arrendada</span>';
                      }
                      
                      echo '</span></td>'; 

                    }else if($clave=="empresa"){
                      echo '<td><span style="font-size:14px;">';
                      echo $this->devolverEmpresa($row["empresa"],$row["tipo"]);
                      echo '</span></td>'; 
                  }else if($clave=="tipoCuenta"){
                      echo '<td><span style="font-size:14px;">';
                      echo $this->devolverTipoCuenta($row["tipoCuenta"]);
                      echo '</span></td>';     
                  }else if($clave=="direccionProp"){
                        echo '<td><span style="font-size:14px;">';
                        echo "<a href='panel.php?op=15&idProp=".$row["idProp"]."'>";
                        echo $row["direccionProp"];                        
                        echo "</a>";
                        echo '</span></td>';  
                  }else if($clave=="idProp"){
                        echo '<td><span style="font-size:14px;">';
                        echo "<a href='panel.php?op=15&idProp=".$row["idProp"]."'>";
                        echo "Ficha #".$row["idProp"];                        
                        echo "</a>";
                        echo '</span></td>';     
                    }else if($clave=="fechaInicio"){
                        echo '<td><span style="font-size:14px;">';
                        echo date("d-m-Y",$row["fechaInicio"]);
                        echo '</span></td>';    

                      }else if($clave=="Direccion"){
                        echo '<td><span style="font-size:14px;">';
                        echo "<a href='panel.php?op=15&idProp=".$row["idProp"]."'>";
                        echo $this->devolverPropiedad($row["idProp"]);
                        echo "</a>";
                        echo '</span></td>';     
                  }else if($clave=="titulo"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:12x !important;">';
                    echo '<a href="panel.php?op=15&idProp='.$row["idProp"].'">'.$row["titulo"]."</a>";
                    echo '</span>';
                    echo "</td>";
                  }else if($clave=="Propietario"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">';
                    echo "<a href='panel.php?op=14&idPropi=".$row["idPropietario"]."'>";
                    echo $this->devolverPropietario($row["idPropietario"]);
                    echo "</a>";
                    echo '</span>';
                    echo "</td>";
                 
                  }else if($clave=="fecha"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">'.date("d-m-Y",$row[$clave]).'</span>';
                    echo "</td>";
                  }else if($clave=="operacion"){
                      echo "<td>";
                      echo '<span style="font-family:arial; font-size:14px !important;">'.$this->devolverOperacion($row[$clave]).'</span>';
                      echo "</td>";
                  }else if($clave=="tipoProp"){
                      echo "<td>";
                      echo '<span style="font-family:arial; font-size:12x !important;">'.$this->devolverTipoProp($row[$clave]).'</span>';
                      echo "</td>";
                  }else if($clave=="fechaLiqui"){
                    $timestamp = strtotime($row["fechaLiqui"]);
                      
                      $nombreMes1 = ucfirst(strftime('%B', $timestamp));
                      
                      $nombreMes=ucfirst($this->devolverMes($nombreMes1));
                      
                      $año = date('Y', $timestamp);
                      
                      echo '<td><span style="font-size:14px;">';
                      echo $nombreMes."&nbsp;".$año;
                      echo '</span></td>';
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
                      echo utf8_encode($row[$clave]);
                      echo '</span></td>';                    
                    }        
                }
                
           
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



    public function desplegarDatosArren(){
      
      $this->link=$this->conectar();
      $ftp=$this->devolverFtp();
     
        $this->paginator=new paginator(24,24);
        $this->paginator->agregarConsulta($this->sql);  
 
        echo '<div class="col-md-12 table-responsive-sm">';      
        echo '<table class="table table-bordered table-sm">
        <thead class="table-light" style="font-size:14px !important;">';
        
          echo '<tr>';
          
          foreach($this->campos as $clave=>$valor){
            echo "<th width='".$this->tamCol[$k]."%'>".ucfirst($clave)."</th>";
            $k++;
          }
          if($_GET["op"]!=20 && $_GET["op"]!=14 && $_GET["op"] !=23){ 
          echo "<th>&nbsp;</th>";
          echo "<th>&nbsp;</th>";
          }
          echo '</tr>';
          echo '
          </thead>
        <tbody>';    
        $this->paginator->estableceIndex($this->index);
        $total=$this->paginator->obtenerTotalReg();    
        $numCol=$this->devolverColumnas()+3;
        if($total==0){
            echo "<tr>
         
            <td colspan='".$numCol."'>Sin Resultados</td></tr>";
        }else{      
            while($row=$this->paginator->devolverResultados()){
                echo '<tr>';    
             
                foreach($this->campos as $clave=>$valor){       
                  
                  $idProp=$row[$this->campoIndice];
                  if($clave=="banco"){
                    echo '<td><span style="font-size:14px;">';
                    echo $this->devolverBanco($row["banco"]);
                    echo '</span></td>'; 
      
                  }else if($clave=="estado"){                      
                    echo '<td><span style="font-size:14px;">';
                    if($row["estado"]==0){
                      echo "Pendiente ";
                    }else if($row["estado"]==1){
                      echo "Moroso";
                    }else{
                      echo "Pagado";
                    }
                    echo '</span></td>'; 
                    
                  }else if($clave=="montoPagar"){                      
                    echo '<td><span style="font-size:14px;">';
                    echo "$ ".$this->formatoNumerico($row["montoPagar"]);
                    echo '</span></td>'; 
                  }else if($clave=="propiedad"){  
                    
                    echo '<td><span style="font-size:14px;">';
                    echo "<a href='panel.php?op=15&idProp=".$row["idProp"]."'>".$this->devolverPropiedad($row["idProp"])."</a>";
                    echo '</span></td>'; 

                  }else if($clave=="tipo"){
                      echo '<td><span style="font-size:14px;">';
                      echo $this->devolverTipo($row["tipo"]);
                      echo '</span></td>'; 
                    }else if($clave=="Estado"){
                      echo '<td><span style="font-size:14px;">';
                      $estado=$this->devolverEstado($row["idProp"]);
                     
                      if($estado==0){
                        echo '<span class="badge text-bg-info">Disponible</span>';
                      }else{
                        echo '<span class="badge text-bg-success">Arrendada</span>';
                      }
                      
                      echo '</span></td>'; 

                    }else if($clave=="empresa"){
                      echo '<td><span style="font-size:14px;">';
                      echo $this->devolverEmpresa($row["empresa"],$row["tipo"]);
                      echo '</span></td>'; 
                  }else if($clave=="tipoCuenta"){
                      echo '<td><span style="font-size:14px;">';
                      echo $this->devolverTipoCuenta($row["tipoCuenta"]);
                      echo '</span></td>';     
                  }else if($clave=="direccionProp"){
                        echo '<td><span style="font-size:14px;">';
                        
                        echo $row["direccionProp"];                        
                        
                        echo '</span></td>';  
                  }else if($clave=="idProp"){
                        echo '<td><span style="font-size:14px;">';
                        echo "<a href='panel.php?op=15&idProp=".$row["idProp"]."'>";
                        echo "Ficha #".$row["idProp"];                        
                        echo "</a>";
                        echo '</span></td>';     
                    }else if($clave=="fechaInicio"){
                        echo '<td><span style="font-size:14px;">';
                        echo date("d-m-Y",$row["fechaInicio"]);
                        echo '</span></td>';    

                      }else if($clave=="Direccion"){
                        echo '<td><span style="font-size:14px;">';
                        echo "<a href='panel.php?op=15&idProp=".$row["idProp"]."'>";
                        echo $this->devolverPropiedad($row["idProp"]);
                        echo "</a>";
                        echo '</span></td>';     
                  }else if($clave=="titulo"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:12x !important;">';
                    echo '<a href="panel.php?op=15&idProp='.$row["idProp"].'">'.$row["titulo"]."</a>";
                    echo '</span>';
                    echo "</td>";
                  }else if($clave=="Propietario"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">';
                    echo "<a href='panel.php?op=14&idPropi=".$row["idPropietario"]."'>";
                    echo $this->devolverPropietario($row["idPropietario"]);
                    echo "</a>";
                    echo '</span>';
                    echo "</td>";
                 
                  }else if($clave=="fecha"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">'.date("d-m-Y",$row[$clave]).'</span>';
                    echo "</td>";
                  }else if($clave=="operacion"){
                      echo "<td>";
                      echo '<span style="font-family:arial; font-size:14px !important;">'.$this->devolverOperacion($row[$clave]).'</span>';
                      echo "</td>";
                  }else if($clave=="tipoProp"){
                      echo "<td>";
                      echo '<span style="font-family:arial; font-size:12x !important;">'.$this->devolverTipoProp($row[$clave]).'</span>';
                      echo "</td>";
                  
                  }else if($clave=="montoArriendo"){
                    echo "<td>";
                        echo '<span style="font-family:arial; font-size:14px;">';					 
                        echo '$'.$this->formatoNumerico($row[$clave]);
                      echo '</span>';
                      echo "</td>";
                  }else{                        
                    
                      echo '<td><span style="font-size:14px;">';
                      echo utf8_encode($row[$clave]);
                      echo '</span></td>';                    
                    }        
                }
                
              echo "<td>";
              echo "<a href='panelArrendatario.php?op=30&idProp=".$row["idProp"]."' class='btn btn-primary btn-sm' ><i class='fas fa-tools'></i> Solicitar Mantenimiento</a>";
              echo "</td>";
              echo "<td>";
              echo "<a href='panelArrendatario.php?op=36&idProp=".$row["idProp"]."' class='btn btn-info btn-sm' ><i class='fas fa-tools'></i> Historial de pagos</a>";
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


    public function desplegarDatosMantencion(){
      
      $this->link=$this->conectar();
      $ftp=$this->devolverFtp();
     
        $this->paginator=new paginator(24,24);
        $this->paginator->agregarConsulta($this->sql);  
 
        echo '<div class="col-md-12 table-responsive-sm">';      
        echo '<table class="table table-bordered table-sm">
        <thead class="table-light" style="font-size:14px !important;">';
        
          echo '<tr>';
          
          foreach($this->campos as $clave=>$valor){
            echo "<th width='".$this->tamCol[$k]."%'>".ucfirst($clave)."</th>";
            $k++;
          }
          if($_GET["op"]!=20 && $_GET["op"]!=14 && $_GET["op"] !=23){ 
          echo "<th>&nbsp;</th>";
    
          }
          echo '</tr>';
          echo '
          </thead>
        <tbody>';    
        $this->paginator->estableceIndex($this->index);
        $total=$this->paginator->obtenerTotalReg();    
        $numCol=$this->devolverColumnas()+3;
        if($total==0){
            echo "<tr>
         
            <td colspan='".$numCol."'>Sin Resultados</td></tr>";
        }else{      
            while($row=$this->paginator->devolverResultados()){
                echo '<tr>';    
             
                foreach($this->campos as $clave=>$valor){       
              
                  $idProp=$row[$this->campoIndice];
                  if($clave=="tipo_servicio"){
                    echo '<td><span style="font-size:14px;">';
                    echo $row["tipo_servicio"];
                    echo '</span></td>'; 
      
                  }else if($clave=="estado"){                      
                    echo '<td><span style="font-size:14px;">';
                    if($row["estado"]==0){
                      echo "Pendiente ";
                    }else if($row["estado"]==1){
                      echo "Moroso";
                    }else{
                      echo "Pagado";
                    }
                    echo '</span></td>'; 
                    
                  }else if($clave=="montoPagar"){                      
                    echo '<td><span style="font-size:14px;">';
                    echo "$ ".$this->formatoNumerico($row["montoPagar"]);
                    echo '</span></td>'; 
                  }else if($clave=="propiedad"){  
                    
                    echo '<td><span style="font-size:14px;">';
                    echo "<a href='panel.php?op=15&idProp=".$row["idProp"]."'>".$this->devolverPropiedad($row["idProp"])."</a>";
                    echo '</span></td>'; 

                  }else if($clave=="tipo"){
                      echo '<td><span style="font-size:14px;">';
                      echo $this->devolverTipo($row["tipo"]);
                      echo '</span></td>'; 
                    }else if($clave=="Estado"){
                      echo '<td><span style="font-size:14px;">';
                      $estado=$this->devolverEstado($row["idProp"]);
                     
                      if($estado==0){
                        echo '<span class="badge text-bg-info">Disponible</span>';
                      }else{
                        echo '<span class="badge text-bg-success">Arrendada</span>';
                      }
                      
                      echo '</span></td>'; 

                    }else if($clave=="empresa"){
                      echo '<td><span style="font-size:14px;">';
                      echo $this->devolverEmpresa($row["empresa"],$row["tipo"]);
                      echo '</span></td>'; 
                  }else if($clave=="tipoCuenta"){
                      echo '<td><span style="font-size:14px;">';
                      echo $this->devolverTipoCuenta($row["tipoCuenta"]);
                      echo '</span></td>';     
                  }else if($clave=="direccionProp"){
                        echo '<td><span style="font-size:14px;">';
                        
                        echo $row["direccionProp"];                        
                        
                        echo '</span></td>';  
                  }else if($clave=="idProp"){
                        echo '<td><span style="font-size:14px;">';
                        echo "<a href='panel.php?op=15&idProp=".$row["idProp"]."'>";
                        echo "Ficha #".$row["idProp"];                        
                        echo "</a>";
                        echo '</span></td>';     
                    }else if($clave=="fechaInicio"){
                        echo '<td><span style="font-size:14px;">';
                        echo date("d-m-Y",$row["fechaInicio"]);
                        echo '</span></td>';    

                      }else if($clave=="Direccion"){
                        echo '<td><span style="font-size:14px;">';
                        echo "<a href='panel.php?op=15&idProp=".$row["idProp"]."'>";
                        echo $this->devolverPropiedad($row["idProp"]);
                        echo "</a>";
                        echo '</span></td>';     
                  }else if($clave=="titulo"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:12x !important;">';
                   echo $row["titulo"];
                    echo '</span>';
                    echo "</td>";
                  }else if($clave=="Propietario"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">';
                    echo "<a href='panel.php?op=14&idPropi=".$row["idPropietario"]."'>";
                    echo $this->devolverPropietario($row["idPropietario"]);
                    echo "</a>";
                    echo '</span>';
                    echo "</td>";
                 
                  }else if($clave=="fecha"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">'.date("d-m-Y",$row[$clave]).'</span>';
                    echo "</td>";
                  }else if($clave=="operacion"){
                      echo "<td>";
                      echo '<span style="font-family:arial; font-size:14px !important;">'.$this->devolverOperacion($row[$clave]).'</span>';
                      echo "</td>";
                  }else if($clave=="tipoProp"){
                      echo "<td>";
                      echo '<span style="font-family:arial; font-size:12x !important;">'.$this->devolverTipoProp($row[$clave]).'</span>';
                      echo "</td>";
                  
                  }else if($clave=="montoArriendo"){
                    echo "<td>";
                        echo '<span style="font-family:arial; font-size:14px;">';					 
                        echo '$'.$this->formatoNumerico($row[$clave]);
                      echo '</span>';
                      echo "</td>";
                  }else{                        
                    
                      echo '<td><span style="font-size:14px;">';
                      echo utf8_encode($row[$clave]);
                      echo '</span></td>';                    
                    }        
                }
                
              echo "<td>";
              
              if($_SESSION["auth"]["tipo"]=="admin"){
                echo "<a href='panel.php?op=20&idArrendatario=".$row["idArrendatario"]."&act=solicitud&idMan=".$row["idMantenimiento"]."' class='btn btn-primary btn-sm' ><i class='fas fa-eye'></i>";
              }else{
                echo "<a href='panelArrendatario.php?op=35&idMan=".$row["idMantenimiento"]."' class='btn btn-primary btn-sm' ><i class='fas fa-eye'></i>";
              }
              
               echo "Ver Detalle</a>";
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


    public function desplegarDatosPortada(){
      
      $this->link=$this->conectar();
      $ftp=$this->devolverFtp();
     
        $this->paginator=new paginator(24,24);
        $this->paginator->agregarConsulta($this->sql);  
 
        echo '<div class="col-md-12 table-responsive-sm">';      
        echo '<table class="table table-bordered table-sm">
        <thead class="table-light" style="font-size:14px !important;">';
        
          echo '<tr>';
          
          foreach($this->campos as $clave=>$valor){
            echo "<th width='".$this->tamCol[$k]."%'>".ucfirst($clave)."</th>";
            $k++;
          }
          if($_GET["op"]!=20 && $_GET["op"]!=14 && $_GET["op"] !=23){ 
          echo "<th>Liquidaciones</th>";
          }
          echo '</tr>';
          echo '
          </thead>
        <tbody>';    
        $this->paginator->estableceIndex($this->index);
        $total=$this->paginator->obtenerTotalReg();    
        $numCol=$this->devolverColumnas()+3;
        if($total==0){
            echo "<tr>
         
            <td colspan='".$numCol."'>Sin Resultados</td></tr>";
        }else{      
            while($row=$this->paginator->devolverResultados()){
                echo '<tr>';    
             
                foreach($this->campos as $clave=>$valor){       
                  
                  $idProp=$row[$this->campoIndice];
                  if($clave=="banco"){
                    echo '<td><span style="font-size:14px;">';
                    echo $this->devolverBanco($row["banco"]);
                    echo '</span></td>'; 
      
                  }else if($clave=="estado"){                      
                    echo '<td><span style="font-size:14px;">';
                    if($row["estado"]==0){
                      echo "Pendiente ";
                    }else if($row["estado"]==1){
                      echo "Moroso";
                    }else{
                      echo "Pagado";
                    }
                    echo '</span></td>'; 
                    
                  }else if($clave=="montoPagar"){                      
                    echo '<td><span style="font-size:14px;">';
                    echo "$ ".$this->formatoNumerico($row["montoPagar"]);
                    echo '</span></td>'; 
                  }else if($clave=="propiedad"){  
                    
                    echo '<td><span style="font-size:14px;">';
                    echo "<a href='panel.php?op=15&idProp=".$row["idProp"]."'>".$this->devolverPropiedad($row["idProp"])."</a>";
                    echo '</span></td>'; 

                  }else if($clave=="tipo"){
                      echo '<td><span style="font-size:14px;">';
                      echo $this->devolverTipo($row["tipo"]);
                      echo '</span></td>'; 
                    }else if($clave=="Estado"){
                      echo '<td><span style="font-size:14px;">';
                      $estado=$this->devolverEstado($row["idProp"]);
                     
                      if($estado==0){
                        echo '<span class="badge text-bg-info">Disponible</span>';
                      }else{
                        echo '<span class="badge text-bg-success">Arrendada</span>';
                      }
                      
                      echo '</span></td>'; 

                    }else if($clave=="empresa"){
                      echo '<td><span style="font-size:14px;">';
                      echo $this->devolverEmpresa($row["empresa"],$row["tipo"]);
                      echo '</span></td>'; 
                  }else if($clave=="tipoCuenta"){
                      echo '<td><span style="font-size:14px;">';
                      echo $this->devolverTipoCuenta($row["tipoCuenta"]);
                      echo '</span></td>';     
                  }else if($clave=="direccionProp"){
                        echo '<td><span style="font-size:14px;">';
                        echo "<a href='panel.php?op=15&idProp=".$row["idProp"]."'>";
                        echo $row["direccionProp"];                        
                        echo "</a>";
                        echo '</span></td>';  
                  }else if($clave=="idProp"){
                        echo '<td><span style="font-size:14px;">';
                        echo "<a href='panel.php?op=15&idProp=".$row["idProp"]."'>";
                        echo "Ficha #".$row["idProp"];                        
                        echo "</a>";
                        echo '</span></td>';     
                    }else if($clave=="fechaInicio"){
                        echo '<td><span style="font-size:14px;">';
                        echo date("d-m-Y",$row["fechaInicio"]);
                        echo '</span></td>';    

                      }else if($clave=="Direccion"){
                        echo '<td><span style="font-size:14px;">';
                        echo "<a href='panel.php?op=15&idProp=".$row["idProp"]."'>";
                        echo $this->devolverPropiedad($row["idProp"]);
                        echo "</a>";
                        echo '</span></td>';     
                  }else if($clave=="titulo"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:12x !important;">';
                    echo '<a href="panel.php?op=15&idProp='.$row["idProp"].'">'.$row["titulo"]."</a>";
                    echo '</span>';
                    echo "</td>";
                  }else if($clave=="Propietario"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">';
                    echo "<a href='panel.php?op=14&idPropi=".$row["idPropietario"]."'>";
                    echo $this->devolverPropietario($row["idPropietario"]);
                    echo "</a>";
                    echo '</span>';
                    echo "</td>";
                 
                  }else if($clave=="fecha"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">'.date("d-m-Y",$row[$clave]).'</span>';
                    echo "</td>";
                  }else if($clave=="operacion"){
                      echo "<td>";
                      echo '<span style="font-family:arial; font-size:14px !important;">'.$this->devolverOperacion($row[$clave]).'</span>';
                      echo "</td>";
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
                      echo utf8_encode($row[$clave]);
                      echo '</span></td>';                    
                    }        
                }
                
             
                if($_GET["op"]!=20 && $_GET["op"] !=14 && $_GET["op"] !=23){ 
              
                echo "<td>";
                echo '<div>';
                echo "<a href='panelPropietario.php?ks=1&idProp=".$row["idProp"]."' class='btn btn-info btn-sm'><i class='fa fa-eye'></i> Ver Liquidaciones</a>";
                echo '</div>';
              
                echo "</td>";
              }
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


    public function desplegarPropietario(){
      
      $this->link=$this->conectar();
      $ftp=$this->devolverFtp();
     
        $this->paginator=new paginator(24,24);
        $this->paginator->agregarConsulta($this->sql);  
 
        echo '<div class="col-md-12 table-responsive-sm">';      
        echo '<table class="table table-bordered table-sm">
        <thead class="table-light" style="font-size:14px !important;">';
        
          echo '<tr>';
          
          foreach($this->campos as $clave=>$valor){
            echo "<th width='".$this->tamCol[$k]."%'>".ucfirst($valor)."</th>";
            $k++;
          }
          if($_GET["op"]!=20 && $_GET["op"]!=14 && $_GET["op"] !=23){ 
          echo "<th>Acciones</th>";
          }
          echo '</tr>';
          echo '
          </thead>
        <tbody>';    
        $this->paginator->estableceIndex($this->index);
        $total=$this->paginator->obtenerTotalReg();    
        $numCol=$this->devolverColumnas()+3;
        if($total==0){
            echo "<tr>
         
            <td colspan='".$numCol."'>Sin Resultados</td></tr>";
        }else{      
            while($row=$this->paginator->devolverResultados()){
                echo '<tr>';    
             
                foreach($this->campos as $clave=>$valor){       
                  
                  $idProp=$row[$this->campoIndice];
                  if($clave=="banco"){
                    echo '<td><span style="font-size:14px;">';
                    echo $this->devolverBanco($row["banco"]);
                    echo '</span></td>'; 
      
                  }else if($clave=="estado"){                      
                    echo '<td><span style="font-size:14px;">';
                    if($row["estado"]==0){
                      echo "Pendiente ";
                    }else if($row["estado"]==1){
                      echo "Moroso";
                    }else{
                      echo "Pagado";
                    }
                    echo '</span></td>'; 
                    
                  }else if($clave=="montoPagar"){                      
                    echo '<td><span style="font-size:14px;">';
                    echo "$ ".$this->formatoNumerico($row["montoPagar"]);
                    echo '</span></td>'; 
                  }else if($clave=="propiedad"){  
                    
                    echo '<td><span style="font-size:14px;">';
                    echo "<a href='panelPropietario.php?op=15&idProp=".$row["idProp"]."'>".$this->devolverPropiedad($row["idProp"])."</a>";
                    echo '</span></td>'; 

                  }else if($clave=="tipo"){
                      echo '<td><span style="font-size:14px;">';
                      echo $this->devolverTipo($row["tipo"]);
                      echo '</span></td>'; 
                    }else if($clave=="Estado"){
                      echo '<td><span style="font-size:14px;">';
                      $estado=$this->devolverEstado($row["idProp"]);
                     
                      if($estado==0){
                        echo '<span class="badge text-bg-info">Disponible</span>';
                      }else{
                        echo '<span class="badge text-bg-success">Arrendada</span>';
                      }
                      
                      echo '</span></td>'; 

                    }else if($clave=="empresa"){
                      echo '<td><span style="font-size:14px;">';
                      echo $this->devolverEmpresa($row["empresa"],$row["tipo"]);
                      echo '</span></td>'; 
                  }else if($clave=="tipoCuenta"){
                      echo '<td><span style="font-size:14px;">';
                      echo $this->devolverTipoCuenta($row["tipoCuenta"]);
                      echo '</span></td>';     
                  }else if($clave=="direccionProp"){
                        echo '<td><span style="font-size:14px;">';
                        echo "<a href='panelPropietario.php?op=15&idProp=".$row["idProp"]."'>";
                        echo $row["direccionProp"];                        
                        echo "</a>";
                        echo '</span></td>';  
                  }else if($clave=="idProp"){
                        echo '<td><span style="font-size:14px;">';
                        echo "<a href='panelPropietario.php?op=15&idProp=".$row["idProp"]."'>";
                        echo "Ficha #".$row["idProp"];                        
                        echo "</a>";
                        echo '</span></td>';     
                    }else if($clave=="fechaInicio"){
                        echo '<td><span style="font-size:14px;">';
                        echo date("d-m-Y",$row["fechaInicio"]);
                        echo '</span></td>';    

                      }else if($clave=="Direccion"){
                        echo '<td><span style="font-size:14px;">';
                        echo "<a href='panelPropietario.php?op=15&idProp=".$row["idProp"]."'>";
                        echo $this->devolverPropiedad($row["idProp"]);
                        echo "</a>";
                        echo '</span></td>';     
                  }else if($clave=="titulo"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:12x !important;">';
                    echo '<a href="panelPropietario.php?op=15&idProp='.$row["idProp"].'">'.$row["titulo"]."</a>";
                    echo '</span>';
                    echo "</td>";
                  }else if($clave=="Propietario"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">';
                    echo "<a href='panelPropietario.php?op=14&idPropi=".$row["idPropietario"]."'>";
                    echo $this->devolverPropietario($row["idPropietario"]);
                    echo "</a>";
                    echo '</span>';
                    echo "</td>";
                 
                  }else if($clave=="fecha"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">'.date("d-m-Y",$row[$clave]).'</span>';
                    echo "</td>";
                  }else if($clave=="operacion"){
                      echo "<td>";
                      echo '<span style="font-family:arial; font-size:14px !important;">'.$this->devolverOperacion($row[$clave]).'</span>';
                      echo "</td>";
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
                      echo utf8_encode($row[$clave]);
                      echo '</span></td>';                    
                    }        
                }
                
             
                if($_GET["op"]!=20 && $_GET["op"] !=14 && $_GET["op"] !=23){ 
              
                echo "<td>";
                echo '<div>';
                echo '<div class="dropdown">
                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Acciones
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
              }
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

    public function devolverArrendatario($id){
      $sql="select* from mm_arrendatario where idArrendatario='".$id."'";
      $q=mysqli_query($this->link,$sql);
      $r=mysqli_fetch_array($q);
      return($r["nombre"]);
    }
    public function desplegarDatosArriendo(){
      
      $this->link=$this->conectar();
      $ftp=$this->devolverFtp();
     
        $this->paginator=new paginator(24,24);
        $this->paginator->agregarConsulta($this->sql);  
 
        echo '<div class="col-md-12 table-responsive-sm">';      
        echo '<table class="table table-bordered table-sm">
        <thead class="table-light" style="font-size:14px !important;">';
        
          echo '<tr>';
          echo "<th>";
          echo "<input type='checkbox' name='check' id='check' value=''/>";
          echo "</th>";
          foreach($this->campos as $clave=>$valor){
            echo "<th width='".$this->tamCol[$k]."%'>".ucfirst($valor)."</th>";
            $k++;
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
            
            <td colspan='".$numCol."'>Sin Resultados</td></tr>";
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
 
                  }else if($clave=="Ficha"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:12x !important;">';
                    echo '<a href="panel.php?op=26&idArriendo='.$row["idArriendo"].'">Ficha #'.$row["idArriendo"]."</a>";
                    echo '</span>';
                    echo "</td>";
                    
                  }else if($clave=="Propiedad"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">';
                    
                    echo  $this->devolverPropiedad($row["idProp"]);
                    
                    
                    echo '</span>';
                    echo "</td>";
                  }else if($clave=="Arrendatario"){
                    echo "<td>"; 
                    echo '<span style="font-family:arial; font-size:14px !important;">';
                    echo "<a href='panel.php?op=20&idArrendatario=".$row["idArrendatarios"]."'>";
                    echo $this->devolverArrendatario($row["idArrendatarios"]);                    
                    echo "</a>";
                    echo '</span>';
                    echo "</td>";
                  }else if($clave=="Propietario"){
                    
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">';
                    $idPropi=$this->devolverIdPropi($row["idProp"]);

                    echo "<a href='panel.php?op=14&idPropi=".$idPropi."'>";                
                  echo $this->devolverPropietario($idPropi);
                    echo "</a>";
                    echo '</span>';
                    echo "</td>";

                  }else if($clave=="titulo"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">';                    
                    echo  $this->devolverPropiedad($row["idProp"]);                   
                    
                    echo '</span>';
                    echo "</td>";

                  }else if($clave=="idPropietarios"){
                    $idPropi=$this->devolverIdPropi($row["idProp"]);
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">';                    
                    echo "<a href='panel.php?op=14&idPropi=".$idPropi."'>".$row["nombre"]."&nbsp;".$row["apellido"]."</a>";                    
                    echo '</span>';
                    echo "</td>";

                  }else if($clave=="fechaInicio"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">'.date("d-m-Y",$row[$clave]).'</span>';
                    echo "</td>";
                  }else if($clave=="Precio"){
                      echo "<td>";
                      echo '<span style="font-family:arial; font-size:14px !important;">$ '.$this->formatoNumerico($row["montoArriendo"]).'</span>';
                      echo "</td>";
                   
                  }else{                        
                    
                      echo '<td><span style="font-size:14px;">';
                      echo $row[$clave];
                      echo '</span></td>';                    
                    }        
                }
                
              
                echo "<td>";
                echo '<div>';
                echo '<div class="dropdown">
                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Acciones
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
              
              echo '<a class="dropdown-item" href="'.$this->index."&mq=editar&idq=".$row[$this->campoIndice].'">Editar</a>';

              if(isset($_GET["op"])){
                  echo '<a class="dropdown-item" href="'.$this->index."&mq=borrar&idq=".$row[$this->campoIndice].'">Borrar</a>';
              }
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

    public function devolverPropiedad($idProp){
      $this->link=$this->conectar();
      $sql="select* from mm_propiedad1 where idProp='".$idProp."'";
      $q=mysqli_query($this->link,$sql);
      $r=mysqli_fetch_array($q);
      return($r["titulo"]);
    }
    public function visualizarDatos(){
      
      $this->link=$this->conectar();
 
     
        $this->paginator=new paginator(24,24);
        $this->paginator->agregarConsulta($this->sql);  
 
        echo '<div class="col-md-12 table-responsive-sm">';      
        echo '<table class="table table-bordered table-sm">
        <thead class="table-light" style="font-size:14px !important;">';
        
          echo '<tr>';
   
          foreach($this->campos as $clave=>$valor){
            echo "<th width='".$this->tamCol[$k]."%'>".ucfirst($clave)."</th>";
            $k++;
          }
     
 
          echo '</tr>';
          echo '
          </thead>
        <tbody>';    
        $this->paginator->estableceIndex($this->index);
        $total=$this->paginator->obtenerTotalReg();    
        $numCol=$this->devolverColumnas()+3;
        if($total==0){
            echo "<tr>
            
            <td colspan='".$numCol."'>Sin Resultados</td></tr>";
        }else{      
            while($row=$this->paginator->devolverResultados()){
                echo '<tr>';    
       
                foreach($this->campos as $clave=>$valor){       
                  
                  $idProp=$row[$this->campoIndice];
                  
                  if($clave=="ruta" || $clave=="foto"){
                    $foto=$this->devolverFoto($idProp,$clave);					 
 
                  }else if($valor=="idProp"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:12x !important;">';
                    echo '<a href="panel.php?op=21&idProp='.$row["idProp"].'">Ficha #'.$row["idProp"]." ficha del arriendo</a>";
                    echo '</span>';
                    echo "</td>";
                  }else if($valor=="tituloProp"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">';
                    echo "<a href='panel.php?op=15&idPropi=".$row["idPropietario"]."'>";
                    echo  utf8_encode($this->devolverPropiedad($row["idProp"]));
                    echo "</a>";
                    echo '</span>';
                    echo "</td>";

                  }else if($clave=="fechaInicio"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">'.date("d-m-Y",$row[$clave]).'</span>';
                    echo "</td>";
                  
                  }else{                        
                    
                      echo '<td><span style="font-size:14px;">';
                      echo utf8_encode($row[$clave]);
                      echo '</span></td>';                    
                    }        
                }
                
              
               

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
    
    public function desplegarDatosPropietarios(){
      
      $this->link=$this->conectar();
      $ftp=$this->devolverFtp();
     
        $this->paginator=new paginator(24,24);
        $this->paginator->agregarConsulta($this->sql);  
 
        echo '<div class="col-md-12 table-responsive-sm">';      
        echo '<table class="table table-bordered table-sm">
        <thead class="table-light" style="font-size:14px !important;">';
        
          echo '<tr>';
          echo "<th>";
          echo "<input type='checkbox' name='check' id='check' value=''/>";
          echo "</th>";
          foreach($this->campos as $clave=>$valor){
            echo "<th width='".$this->tamCol[$k]."%'>".ucfirst($clave)."</th>";
            $k++;
          }
          echo "<th>Enviar Clave</th>";
          echo "<th>Cta. Bancarai</th>";
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
            
            <td colspan='".$numCol."'>Sin Resultados</td></tr>";
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
                        echo "<img src='./upload/".$foto."' height='60' width='80'/>";
                      }
                      
                    }
                    echo "</td>";
                  
                  }else if($clave=="nombre"){
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">';
                    echo "<a href='panel.php?op=14&idPropi=".$row["idPropietario"]."'>".ucfirst($row[$clave])."&nbsp;".$row["apellido"]."</a>";
                    echo '</span>';
                    echo "</td>";
                  }else if($clave=="operacion"){
                      echo "<td>";
                      echo '<span style="font-family:arial; font-size:14px !important;">'.$this->devolverOperacion($row[$clave]).'</span>';
                      echo "</td>";
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
                      echo $row[$clave];
                      echo '</span></td>';                    
                    }        
                }
                
                echo "<td>";
                echo "<button class='btn btn-success btn-sm' id='enviarClave' alt='".$row["idPropietario"]."' name='enviarClave'><i class='fas fa-user'></i> Enviar Claves</button>";
                echo "</td>";
                echo "<td>";
                echo '<div class="dropdown">
                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-money-check"></i>&nbsp;Cta.Bancaria
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="panel.php?op=21&idPropi='.$row["idPropietario"].'">Ingresar Cta.Bancaria</a></li>
                  <li><a class="dropdown-item" href="panel.php?op=21&idPropi='.$row["idPropietario"].'&act=edit">Editar Cta.Bancaria</a></li>                  
                </ul>
              </div>';
                
                echo "</td>";
                echo "<td>";
                echo '<div>';
                echo '<div class="dropdown">
                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Acciones
                </button>
                <ul class="dropdown-menu">';
              
                  echo '<a class="dropdown-item" href="'.$this->index."&mq=editar&idq=".$row[$this->campoIndice].'">Editar Propietario</a>
                    <a class="dropdown-item" href="'.$this->index."&mq=borrar&idq=".$row[$this->campoIndice].'">Borrar Propietario</a>
                    <a class="dropdown-item" href="panel.php?op=21&idPropi='.$row[$this->campoIndice].'">Agregar Cuenta Bancaria</a>
                    <a class="dropdown-item" href="panel.php?op=21&act=edit&idPropi='.$row[$this->campoIndice].'">Modificar Cuenta Bancaria</a>
                    ';

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





public function devolverMes($mes){
  $meses = array(
    'January'   => 'enero',
    'February'  => 'febrero',
    'March'     => 'marzo',
    'April'     => 'abril',
    'May'       => 'mayo',
    'June'      => 'junio',
    'July'      => 'julio',
    'August'    => 'agosto',
    'September' => 'septiembre',
    'October'   => 'octubre',
    'November'  => 'noviembre',
    'December'  => 'diciembre'
);
return($meses[$mes]);
}


public function saldoFinal($idLiq){
  $sql="SELECT 
    mm_liquidacionArriendo.idLiquidacion,
    mm_liquidacionArriendo.idArriendo,
    mm_liquidacionArriendo.idProp,
    mm_liquidacionArriendo.idPropietario,
    mm_liquidacionArriendo.idArrendatario,
    mm_liquidacionArriendo.fechaLiqui,
    mm_liquidacionArriendo.idDatosDep,
    SUM(mm_detalleLiquidacion.abono) AS totalAbono,
    SUM(mm_detalleLiquidacion.descuento) AS totalDescuento,
    SUM(mm_detalleLiquidacion.abono) - SUM(mm_detalleLiquidacion.descuento) AS monto
FROM
    mm_liquidacionArriendo
JOIN
    mm_detalleLiquidacion ON mm_liquidacionArriendo.idLiquidacion = mm_detalleLiquidacion.idLiquidacion	
WHERE   mm_liquidacionArriendo.idLiquidacion=$idLiq
GROUP BY
    mm_liquidacionArriendo.idLiquidacion, mm_liquidacionArriendo.idArriendo, mm_liquidacionArriendo.idProp, mm_liquidacionArriendo.idPropietario, mm_liquidacionArriendo.idArrendatario, mm_liquidacionArriendo.fechaLiqui, mm_liquidacionArriendo.idDatosDep
";
$q=mysqli_query($this->link,$sql);
$r=mysqli_fetch_array($q);
$d["monto"]=$r["monto"];
$d["abono"]=$r["totalAbono"];
$d["desc"]=$r["totalDescuento"];

return($d);
}


public function devolverFechaCancelacion($idArriendo){
  $sql="select fechaCancelacion from mm_arriendos where idArriendo='".$idArriendo."'";
  $q=mysqli_query($this->link,$sql);
  $r=mysqli_fetch_array($q);
  return(strtotime($r["fechaCancelacion"]));
}

public function desplegarCobros(){
  $idLiq=$_GET["idLiq"];
  $this->link=$this->conectar();
  
 if(isset($_GET["idArriendo"])){ $idArriendo=$_GET["idArriendo"];}
    $this->paginator=new paginator(24,24);
    $this->paginator->agregarConsulta($this->sql);  
    echo "<div class='row'>";
    echo "<div class='col-md-12'>";
    $sal=$this->saldoFinal($idLiq);
    
    echo "<span style='font-size:18px;'>Saldo: $".$this->formatoNumerico($sal["monto"])."</span>";
    echo "</div>";
    echo "</div>";
    if($_SESSION["auth"]["tipo"]=="admin"){
    echo "<div class='row' style='margin-top:20px;'>";
    
    echo "<div class='col-md-3'>";
    echo "<a href='panel.php?op=26&idArriendo=".$idArriendo."&act=cobros&z=abono&v=det&idLiq=".$idLiq."' class='btn btn-primary btn-sm' role='button'><i class='fas fa-money-check-alt'></i> Ingresar abono</a>";
    echo "</div>";

    echo "<div class='col-md-3'>";
    echo "<a href='panel.php?op=26&idArriendo=".$idArriendo."&act=cobros&z=desc&v=det&idLiq=".$idLiq."' class='btn btn-primary btn-sm' role='button'><i class='fas fa-tags'></i> Ingresar descuento</a>";
    echo "</div>";
    echo "<div class='col-md-3'>";
    echo "<a href='panel.php?op=26&idArriendo=".$idArriendo."&act=cobros&z=comisiones&v=det&idLiq=".$idLiq."' class='btn btn-info btn-sm' role='button'><i class='fas fa-edit'></i> Editar comisiones</a>";
    echo "</div>";

    echo "</div>";
    }

    echo "<div class='row' style='margin-top:10px;'>";
    echo '<div class="col-md-12 table-responsive-sm">';      
    echo '<table class="table table-bordered table-sm">
    <thead class="table-light" style="font-size:14px !important;">';
    
      echo '<tr>';
    
      foreach($this->campos as $clave=>$valor){
        echo "<th width='".$this->tamCol[$k]."%'>".ucfirst($valor)."</th>";
        $k++;
      }

      
      if($_GET["op"]!=20 && $_GET["op"]!=14 && $_GET["op"] !=23  && $_GET["ks"] !=1){ 
      echo "<th>Acciones</th>";
      }

      echo '</tr>';
      echo '
      </thead>
    <tbody>';    
    $this->paginator->estableceIndex($this->index);
    $total=$this->paginator->obtenerTotalReg();    
    $numCol=$this->devolverColumnas()+3;
    if($total==0){
        echo "<tr>
     
        <td colspan='".$numCol."'>Sin Resultados</td></tr>";
    }else{      
        while($row=$this->paginator->devolverResultados()){
            echo '<tr>';    
         
            foreach($this->campos as $clave=>$valor){       
              
              $idProp=$row[$this->campoIndice];
            
               if($clave=="monto"){                      
                echo '<td><span style="font-size:14px;">';
                echo "$ ".$this->formatoNumerico($row["monto"]);
                echo '</span></td>'; 
               }else if($clave=="fecha"){
                $timestamp = $row[$clave];
                
                $nombreMes1 = ucfirst(strftime('%B', $timestamp));
                $nombreMes=ucfirst($this->devolverMes($nombreMes1));
                
                $año = date('Y', $timestamp);
                
                echo '<td><span style="font-size:14px;">';
                echo "$nombreMes $año";
                echo '</span></td>';
               }else if($clave=="idProp"){
                echo '<td><span style="font-size:14px;">';
                  echo "<a href='panel.php?op=15&idProp=".$row["idProp"]."'>".$this->devolverPropiedad($row["idProp"])."</a>";
                  echo '</span></td>';                    
                }else if($clave=="abono"){
                  echo '<td><span style="font-size:14px;">$';
                   echo $this->formatoNumerico($row["abono"]);
                    echo '</span></td>';  
                  }else if($clave=="descuento"){
                    echo '<td><span style="font-size:14px;">$';
                     echo $this->formatoNumerico($row["descuento"]);
                      echo '</span></td>'; 

              }else{                        
                
                  echo '<td><span style="font-size:14px;">';
                  echo utf8_encode($row[$clave]);
                  echo '</span></td>';                    
                }        
            }
            
      
            if($_GET["op"]!=20 && $_GET["op"] !=14 && $_GET["op"] !=23 && $_GET["ks"] !=1 && $row["fijo"]==1){ 
            
            echo "<td>";
            echo '<div>';
            echo '<div class="dropdown">
            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              Acciones
            </button>
            <ul class="dropdown-menu">';
                     
          echo '<a class="dropdown-item" href="'.$this->index."&mq=editar&idq=".$row[$this->campoIndice].'">Editar</a>
                <a class="dropdown-item" href="'.$this->index."&mq=borrar&idq=".$row[$this->campoIndice].'">Borrar</a>';

            echo '</ul>
          </div>';
            echo '</div>';
           
          
         
            echo "</div>";
            echo "</td>";
          }
            echo '</tr>';
            
           
        }
        
        echo "<tr>";
        echo "<td>&nbsp;</td>";
        echo "<td>$".$this->formatoNumerico($sal["abono"])."</td>";
        echo "<td>$".$this->formatoNumerico($sal["desc"])."</td>";
        echo "</tr>";
    }       
 
      echo '
    </tbody>
  </table>';
  
  echo ' </div>
  </div>';
  
  if($total>5){
    echo '<div style="margin-top:20px;">';
    $this->paginator->navegacion("sm");
    echo "</div>";
  }   
}
    public function desplegarDatosLiquidacionArriendo(){
 
      
        $this->link=$this->conectar();
 
       
          $this->paginator=new paginator(24,24);
          $this->paginator->agregarConsulta($this->sql);  
   
          echo '<div class="col-md-12 table-responsive-sm">';      
          echo '<table class="table table-bordered table-sm">
          <thead class="table-light" style="font-size:14px !important;">';
          
            echo '<tr>';
          
            foreach($this->campos as $clave=>$valor){
              echo "<th width='".$this->tamCol[$k]."%'>".ucfirst($valor)."</th>";
              $k++;
            }
            echo "<th>Ver</th>";
            echo "<th>Pdf</th>";
            
            if($_GET["op"]!=20 && $_GET["op"]!=14  &&  $_GET["op"]!=15  && $_GET["op"] !=26 && $_GET["op"] !=23  && $_GET["ks"] !=1){ 
            echo "<th>Acciones</th>";
            }
      
            echo '</tr>';
            echo '
            </thead>
          <tbody>';    
          $this->paginator->estableceIndex($this->index);
          $total=$this->paginator->obtenerTotalReg();    
          $numCol=$this->devolverColumnas()+3;
          if($total==0){
              echo "<tr>
           
              <td colspan='".$numCol."'>Sin Resultados</td></tr>";
          }else{      
              while($row=$this->paginator->devolverResultados()){
                  echo '<tr>';    
               
                  foreach($this->campos as $clave=>$valor){       
                    
                    $idProp=$row[$this->campoIndice];
                  
                     if($clave=="monto"){                      
                      echo '<td><span style="font-size:14px;">';
                      echo "$ ".$this->formatoNumerico($row["monto"]);
                      echo '</span></td>'; 
                     }else if($clave=="fecha"){
                      $timestamp = strtotime($row["fechaLiqui"]);
                      
                      $nombreMes1 = ucfirst(strftime('%B', $timestamp));
                      
                      $nombreMes=ucfirst($this->devolverMes($nombreMes1));
                      
                      $año = date('Y', $timestamp);
                      
                      echo '<td><span style="font-size:14px;">';
                      echo $nombreMes1."&nbsp;".$año;
                      echo '</span></td>';


                    }else if($clave=="fechaCancelacion"){
                      echo '<td><span style="font-size:14px;">';
                        echo date("d-m-Y",$this->devolverFechaCancelacion($row["idArriendo"]));
                        echo '</span></td>'; 
                        
                      }else if($clave=="estado"){
                        echo '<td><span style="font-size:14px;">';
                        // 0=pendiente 1=en proceso 2=pagada 3=moroso

                          if($row["estado"]==0){
                            echo '<span class="badge text-bg-info">Pendiente</span>';
                          }else if($row["estado"]==1){
                            echo '<span class="badge text-bg-success">En Proceso</span>';
                          }else if($row["estado"]==2){
                            echo '<span class="badge text-bg-primary">Pagada</span>';
                          }else if($row["estado"]==3){
                            echo '<span class="badge text-bg-red">En Mora</span>';
                          }else{
                            echo '<span class="badge text-bg-warning">Indeterminada</span>';
                          }
                          echo '</span></td>'; 


                     }else if($clave=="idProp"){
                      echo '<td><span style="font-size:14px;">';
                        echo "<a href='panel.php?op=15&idProp=".$row["idProp"]."'>".$this->devolverPropiedad($row["idProp"])."</a>";
                        echo '</span></td>';                    

                    }else{                        
                      
                        echo '<td><span style="font-size:14px;">';
                        echo utf8_encode($row[$clave]);
                        echo '</span></td>';                    
                      }        
                  }
                  $tipo=$_SESSION["auth"]["tipo"];
                  if($tipo=="admin"){
                    $index1="panel.php";
                  }else{
                    $index1="panelPropietario.php";
                  }
                  
               echo "<td>";
                if($tipo=="admin"){
                  echo "<a href='".$index1."?op=26&idArriendo=".$row["idArriendo"]."&act=cobros&v=det&idLiq=".$row["idLiquidacion"]."' class='btn btn-primary btn-sm' role='button' style='font-size:12px;width:100%;padding-left:4px;padding-right:4px;'><i class='fas fa-eye'></i> Ver Detalle</a>";
                }else{
                  if(isset($_GET["ks"])){
                    echo "<a href='".$index1."?ks=1&act=ver&idProp=".$_GET["idProp"]."&idArriendo=".$row["idArriendo"]."&idLiq=".$row["idLiquidacion"]."' class='btn btn-primary btn-sm' role='button' style='width:100% ;font-size:12px;padding-left:4px;padding-right:4px;'><i class='fas fa-eye'></i> Ver Detalle</a>";
                  }else{
                    echo "<a href='".$index1."?op=26&idArriendo=".$row["idArriendo"]."&act=cobros&v=det&idLiq=".$row["idLiquidacion"]."' class='btn btn-primary btn-sm' role='button' style=' width:100%;font-size:12px;padding-left:4px;padding-right:4px;'><i class='fas fa-eye'></i> Ver Detalle</a>";
                  }

                }
               


               echo "</td>";
               if(!empty($row["rutaPdf"])){
               echo "<td>
               <a href='https://clickcorredores.cl/adminArriendo/pdf/".$row["rutaPdf"]."' target='_blank' class='btn btn-info btn-sm' role='button' style='width:100%;font-size:12px;padding-left:4px;padding-right:4px;'><i class='fas fa-file-pdf'></i> Ver Pdf</a>
               </td>";
               }else{
                echo "<td>";
                echo "&nbsp;";
                echo "</td>";
               }
                  if($_GET["op"]!=20 && $_GET["op"] !=14 && $_GET["op"] !=15 && $_GET["op"] !=26 && $_GET["op"] !=23 && $_GET["ks"] !=1){ 
                
                  echo "<td>";
                  echo '<div>';
                  echo '<div class="dropdown">
                  <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Acciones
                  </button>
                  <ul class="dropdown-menu">';
                 
                
                echo '<a class="dropdown-item" href="'.$this->index."&mq=editar&idq=".$row[$this->campoIndice].'">Editar</a>
                      <a class="dropdown-item" href="'.$this->index."&mq=borrar&idq=".$row[$this->campoIndice].'">Borrar</a>';
  
                  echo '</ul>
                </div>';
                  echo '</div>';
                 
                
               
                  echo "</div>";
                  echo "</td>";
                }
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

    


    public function desplegarDatosArrendatarios(){
      
      $this->link=$this->conectar();
       
        $this->paginator=new paginator(24,24);
        $this->paginator->agregarConsulta($this->sql);  
      
        echo '<div class="col-md-12 table-responsive-sm">';      
        echo '<table class="table table-bordered table-sm">
        <thead class="table-light" style="font-size:14px !important;">';
        
          echo '<tr>';
          echo "<th>";
          echo "<input type='checkbox' name='check' id='check' value=''/>";
          echo "</th>";
          foreach($this->campos as $clave=>$valor){
            echo "<th width='".$this->tamCol[$k]."%'>".ucfirst($clave)."</th>";
            $k++;
          }
          echo "<th>Cta.Bancaria</th>";
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
            
            <td colspan='".$numCol."'>Sin Resultados</td></tr>";
        }else{    
     
   
            while($row=$this->paginator->devolverResultados()){
                echo '<tr>';    
                echo "<td>";
                echo "<input type='checkbox' name='sel[]' id='sel[]' value='".$row["idProp"]."'/>";
                echo "</td>";
                foreach($this->campos as $clave=>$valor){       
           
                  $idProp=$row[$this->campoIndice];
                  if($clave=="Nombre"){
                    
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">';
                    
                      echo "<a href='panel.php?op=20&idArrendatario=".$row["idArrendatario"]."'>".$row["Nombre"]."</a>";                  
                    

                    echo '</span>';
                    echo "</td>";
                  }else if($clave=="nombre"){
                    
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">';
                    if($_GET["op"]==18){
                      echo "<a href='panel.php?op=19&idCoud=".$row["idCodeudor"]."'>".ucfirst($row[$clave])."</a>";
                    }else{
                      echo "<a href='panel.php?op=14&idPropi=".$row["idPropietario"]."'>".ucfirst($row[$clave])."</a>";
                    }
                    

                    echo '</span>';
                    echo "</td>";
                  }else if($clave=="Propiedades"){
                      echo "<td>";
                      echo '<span style="font-family:arial; font-size:14px !important;"><a href="panel.php?op=15&idProp='.$row["idProp"].'">'.$this->devolverPropiedad($row["idProp"]).'</a></span>';
                      echo "</td>";
                  }else if($clave=="Propietario"){
                      echo "<td>";
                     
                      $idPropi=$this->devolverIdPropi($row["idProp"]);
                      $nombre=$this->devolverPropietario($idPropi);
                      echo '<span style="font-family:arial; font-size:12x !important;"><a  href="panel.php?op=14&idPropi='.$idPropi.'">'.$nombre.'</a></span>';
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
                echo '<div class="dropdown">
                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-money-check"></i>&nbsp;Cta.Bancaria
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="panel.php?op=255&act=1&idArrendatario='.$row["idArrendatario"].'">Ingresar Cta.Bancaria</a></li>
                  <li><a class="dropdown-item" href="panel.php?op=255&act=2&idArrendatario='.$row["idArrendatario"].'&act=edit">Editar Cta.Bancaria</a></li>                  
                </ul>
              </div>';
                
                echo "</td>";
                echo "<td>";
                echo '<div>';
                echo '<div class="dropdown">
                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Acciones
                </button>
                <ul class="dropdown-menu">';
              
                  echo '<a class="dropdown-item" href="'.$this->index."&mq=editar&idq=".$row[$this->campoIndice].'">Editar</a>
                    <a class="dropdown-item" href="'.$this->index."&mq=borrar&idq=".$row[$this->campoIndice].'">Borrar</a>';
                  if($_GET["op"]==4){
                    echo '<a class="dropdown-item" href="panel.php?op=255&act=1&idArrendatario='.$row[$this->campoIndice].'">Agregar Cuenta Bancaria</a>
                    <a class="dropdown-item" href="panel.php?op=255&act=edit&idArrendatario='.$row[$this->campoIndice].'">Modificar Cuenta Bancaria</a>';
                  }else{
                    echo '<a class="dropdown-item" href="panel.php?op=13&act=0&idPropi='.$row[$this->campoIndice].'">Agregar Cuenta Bancaria</a>
                    <a class="dropdown-item" href="panel.php?op=13&act=0&idPropi='.$row[$this->campoIndice].'">Modificar Cuenta Bancaria</a>';

                  }
                    

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
     

    
    public function desplegarDatosCodeudor(){
      
      $this->link=$this->conectar();
       
        $this->paginator=new paginator(24,24);
        $this->paginator->agregarConsulta($this->sql);  
      
        echo '<div class="col-md-12 table-responsive-sm">';      
        echo '<table class="table table-bordered table-sm">
        <thead class="table-light" style="font-size:14px !important;">';
        
          echo '<tr>';
          echo "<th>";
          echo "<input type='checkbox' name='check' id='check' value=''/>";
          echo "</th>";
          foreach($this->campos as $clave=>$valor){
            echo "<th width='".$this->tamCol[$k]."%'>".ucfirst($clave)."</th>";
            $k++;
          }
          echo "<th>Cta.Bancaria</th>";
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
            
            <td colspan='".$numCol."'>Sin Resultados</td></tr>";
        }else{    
     
   
            while($row=$this->paginator->devolverResultados()){
                echo '<tr>';    
                echo "<td>";
                echo "<input type='checkbox' name='sel[]' id='sel[]' value='".$row["idProp"]."'/>";
                echo "</td>";
                foreach($this->campos as $clave=>$valor){       
           
                  $idProp=$row[$this->campoIndice];
                  if($clave=="Nombre"){
                    
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">';
                    
                      echo "<a href='panel.php?op=20&idArrendatario=".$row["idArrendatario"]."'>".$row["Nombre"]."</a>";                  
                    

                    echo '</span>';
                    echo "</td>";
                  }else if($clave=="nombre"){
                    
                    echo "<td>";
                    echo '<span style="font-family:arial; font-size:14px !important;">';
                    if($_GET["op"]==18){
                      echo "<a href='panel.php?op=19&idCoud=".$row["idCodeudor"]."'>".ucfirst($row[$clave])."</a>";
                    }else{
                      echo "<a href='panel.php?op=14&idPropi=".$row["idPropietario"]."'>".ucfirst($row[$clave])."</a>";
                    }
                    

                    echo '</span>';
                    echo "</td>";
                  }else if($clave=="Propiedades"){
                      echo "<td>";
                      echo '<span style="font-family:arial; font-size:14px !important;"><a href="panel.php?op=15&idProp='.$row["idProp"].'">'.$this->devolverPropiedad($row["idProp"]).'</a></span>';
                      echo "</td>";
                  }else if($clave=="Propietario"){
                      echo "<td>";
                     
                      $idPropi=$this->devolverIdPropi($row["idProp"]);
                      $nombre=$this->devolverPropietario($idPropi);
                      echo '<span style="font-family:arial; font-size:12x !important;"><a  href="panel.php?op=14&idPropi='.$idPropi.'">'.$nombre.'</a></span>';
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
                      echo $row[$clave];
                      echo '</span></td>';                    
                    }        
                }
                
                
                echo "<td>";
                echo '<div class="dropdown">
                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-money-check"></i>&nbsp;Cta.Bancaria
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="panel.php?op=256&act=1&idCodeudor='.$row["idCodeudor"].'">Ingresar Cta.Bancaria</a></li>
                  <li><a class="dropdown-item" href="panel.php?op=256&idCodeudor='.$row["idCodeudor"].'&act=edit">Editar Cta.Bancaria</a></li>                  
                </ul>
              </div>';
                
                echo "</td>";
                echo "<td>";
                echo '<div>';
                echo '<div class="dropdown">
                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Acciones
                </button>
                <ul class="dropdown-menu">';
              
                  echo '<a class="dropdown-item" href="'.$this->index."&mq=editar&idq=".$row[$this->campoIndice].'">Editar</a>
                    <a class="dropdown-item" href="'.$this->index."&mq=borrar&idq=".$row[$this->campoIndice].'">Borrar</a>';
                  
                    echo '<a class="dropdown-item" href="panel.php?op=256&act=1&idCodeudor='.$row[$this->campoIndice].'">Agregar Cuenta Bancaria</a>
                    <a class="dropdown-item" href="panel.php?op=256&act=edit&idCodeudor='.$row[$this->campoIndice].'">Modificar Cuenta Bancaria</a>';
                  
                    

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