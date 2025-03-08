<?php

/**
 * @author Luis Olguin - Programador 
 www.programacionwebchile.cl
 luisalbchile21@gmail.com
 * @copyright Marzo - 2014
 revisión: 27/7/2017
 *  
 */
 
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
    public function __construct($numReg=false,$index=false,$campoIndice=false,$campoFoto=false,$opciones=false,$prod=false){
        $this->opciones=$opciones;
        $this->producto=$prod;
        $this->campoFoto=$campoFoto;
        $this->campoIndice=$campoIndice; // para borrar y editar
        $this->numReg=$numReg;
        $this->index=$index;
        
        $this->paginator=new paginator($this->numReg,9);
    }
    public function setColor($color){        
         $this->listaColores=$color;
    }
  
    public function abrirGridView($titulo){
       echo "<table width='100%' border=0>";
       echo "<tr><td><span style='font-size:18px; font-weight:bold;'>";
       echo $titulo;
       echo "</span></td><td align='right'><a href='?op=2'><img src='./images/a.png'/></a>&nbsp;<a href='?op=1'><img src='./images/b.png'/></a></td></tr>";
       echo "<tr><td colspan=2>";
       echo "<div id='res'>";
       return(true);
    }
    public function cerrarGridView(){
        echo "</div>";
        echo "</td></tr>";
        echo "</table>";
        return(true);
    }
    public function openGridTable(){
        if(isset($_GET["op"]) && $_GET["op"]==2){$modo=2;}else{$modo=1;}
        echo "<table width='100%'  cellspacing=8 cellpading=6 border=0>";
        if($modo==2){echo "<tr>";}
    }
    public function closeGridTable(){
        echo "</table>";
    }
    public function gridView($cont,$k,$numCol){
        if(isset($_GET["op"]) && $_GET["op"]==2){$modo=2;}else{$modo=1;}
        if($modo==1){echo "<tr>";}
        echo "<td colspan=2>";
        echo $cont;
        echo "</td>";
        if($modo==2){
        if($k%$numCol==0){
            echo "</tr>";
        } 
        }
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
    public function asignarConsulta($sql){
        $this->sql=$sql;
    }    
    public function extraerNomTabla(){
        $c=explode("from",$this->sql);
        $cadena=explode("where",$c[1]);
        $this->nomTabla=$cadena[0];
        return($this->nomTabla);
    }
    private function procesarDatos($d){
        if(isset($_POST["eliminar"])){
            if(isset($_POST["opcion"])){
            $lista=implode(",",$_POST["opcion"]);  
            $this->extraerNomTabla();
            if(isset($_GET["numPag"])){
                $numPag=htmlentities($_GET["numPag"]);
            }else{
                $numPag=1;
            }
            if($this->borrarDatos($lista)){
                header("location:".$this->index."&numPag=".$numPag."&msg=1");
                exit;  
            }
            }
       }
    }
    public function abrirTabla($titulo=false,$boton=false){
        $this->boton=$boton;
        $this->procesarDatos($_POST);
     
        echo "<form method='post' id='form1' name='form1'><table width='100%' border=0>";
        if(isset($_GET["msg"])){
            $op=htmlentities($_GET["msg"]);
        }
        if($op==1){
            echo "Registros se han borrado con exito !!!";
        }
        echo "<tr>";
        echo "<td>";
        echo "<table width='100%' cellspacing=4 cellpadding=3 border=0 style='background:url(../imagen/http://www.programacionwebchile.com/portafolio/inmobiliaria2/imagenes/ind2.jpg) repeat-x;'>";
        echo "<tr><td >";
        echo '<i class="icon-arrow-right-3" style="font-size:22px;"></i>&nbsp;&nbsp;<span style="font-size:18px; font-family:arial;">'.$titulo."</span>";
        if(isset($_GET["mq"]) && $_GET["mq"]=="ver"){
            echo "&nbsp;";
            echo "(<a href='".$this->index."'>Volver</a>)";
            echo "</span>";
        }
        echo "</td>";
        
        echo"</tr>";
        echo "</table>";        
        echo "</td>";     
        echo "</tr>";
        echo "<tr><td>";
    }
    public function cerrarTabla(){
        echo "</td></tr>";
        echo "</table></form>";
    }
    public function asignarCampos($campos,$tamCol){
        $this->campos=$campos;
        $this->tamCol=$tamCol;
    }
    public function devolverColumnas(){
        $numCol=count($this->tamCol);
        return($numCol);
    }
    public function borrarDatos($lista){
        $sql="delete from ".$this->nomTabla. " where ".$this->campoIndice." in (".$lista.")";              
        mysql_query($sql);
        return(true);
    }
    public function desplegarGaleriaFotos($sql,$numCol,$campo){
        $this->paginator->agregarConsulta($sql);
        $nCol=$numCol;
        $k=0;
        $this->paginator->estableceIndex($this->index);
        $total=$this->paginator->obtenerTotalReg();        
        echo "<table width='1%' border=1 cellspacing=4 cellpadding=3>";  
            
          if($total==0){
            echo "<tr><td colspan='".$numCol."'>Sin Contenido</td></tr>";
          }else{   
            echo "<tr>";
            
            while($row=$this->paginator->devolverResultados()){
                $k++;
                    $idCampo=$row[$campo[0]];
                    $foto=$row[$campo[1]];
                    echo "<td>";
                    echo "<table width='1%' border=1>";
                    echo "<tr><td>";
                    echo "<img src='./upload/".$foto."' width='100' height='100'/>";
                    echo "</td></tr>";
                    echo "<tr><td>";
                    echo "<input type='checkbox' name='fotos[]' id='fotos' value='".$idCampo."'/>Borrar";
                    echo "</td></tr>";
                    echo "</table>";
                    echo "</td>";                
                
                if($k%$nCol==0){
                    echo "</tr>";
                }
            }
         }
        echo "</table>";        
    }
    public function desplegarDatos($n=false){
        
        $c=1;
        $numero_colores=2;      
            
        $this->paginator->agregarConsulta($this->sql);  
        echo "<table width='100%' border=0 class='table cellspacing=0 cellpadding=0>";
        echo "<tr height='30px'>";
       
        $k=0;
        foreach($this->campos as $clave=>$valor){
            
            echo "<td width='".$this->tamCol[$k]."%'><span style='padding:10px;font-family:arial; font-size:14px; font-weight:bold;'>".ucfirst($valor)."</span></td>";
            $k++;
        }
        echo "<td width='1%' colspan=3>Acciones</td>";
        
        echo "</tr>";
        $this->paginator->estableceIndex($this->index);
        $total=$this->paginator->obtenerTotalReg();
          $numCol=$this->devolverColumnas()+2;
        if($total==0){
            echo "<tr><td colspan='".$numCol."'>Sin Contenido</td></tr>";
        }else{      
            while($row=$this->paginator->devolverResultados()){
               
                $c++;
            
                echo "<tr valign='top'>";                
                 
                foreach($this->campos as $clave=>$valor){                    
                    if($clave==$this->campoFoto[$clave]){
                        echo "<td style='margin-left:10px;'>";
                        echo "<img src='./upload/".trim($row[$clave])."' style='margin-left:10px; margin-top:5px; padding:2px;border-width:1px; border-style:solid; border-color:#f3f3f3;'  height='60' width='80'/>";
                        echo "</td>";    
                    }else{
                        
                        echo "<td style='padding-left:10px;padding-top:5px;padding-bottom:10px;'>";                        
                        if(is_array($this->opciones[$clave])){
                            echo '<span style="font-family:arial; margin-top:5px; font-size:14px;">'.$this->opciones[$clave][$row[$clave]].'</span>';
                        }else{
                            if($clave=="fecha"){
                                   echo '<span style="margin:10px; font-family:arial; font-size:14px;">'.date("m-d-Y",$row[$clave])."</span>";
                            }else if($clave=="descripcion"){
                            		 echo '<span style="margin:10px; font-family:arial; font-size:14px;">'.substr($row[$clave],0,205).'...</span>';
							  }else if($clave=="texto"){
                            		 echo '<span style="padding:10px; font-family:arial; font-size:14px;">'.substr($row[$clave],0,205).'...</span>';
                            }else{
                            	 
                               		 echo '<span style="padding:10px;font-family:arial; font-size:14px;">'.$row[$clave].'</span>';
								 
                            }
                        }                        
                        echo "</td>";
                    }                    
                }        
                if($this->producto!=false){
                    echo "<td style='margin:10px;'>";
                    echo "<a href='".$this->index."&mq=ver&idq=".$row[$this->campoIndice]."'>Ver Fotos</a>";
                    echo "</td>";
                }
                     echo "<td align='center'>";
                echo "<a href='".$this->index."&mq=editar&idq=".$row[$this->campoIndice]."'><img style='width:20px;padding-top:5px;' src='./imagen/editar.png'></a>";
                echo "</td>";
                    echo "<td align='center'>";
                echo "<a href='".$this->index."&mq=borrar&idq=".$row[$this->campoIndice]."'><img style='padding-top:5px;width:20px;' src='./imagen/boton.png'></a>";
                echo "</td>";          
                echo "</tr>";       
            } 
        }    
      
        if($total>5){
         echo '<tr><td colspan='.$numCol.' valign="top">';
            echo "<table width='100%' border=0>";
            echo "<tr><td width='20%'>&nbsp;</td><td>";
            $this->paginator->navegacion();
            echo "</td><td width='20%'>&nbsp;</td></tr>";
            echo "</table>";
            echo '</td></tr>';
            }
            
        echo "</table>";
    }
	public function devolverSubMenu($id){
		$sql="select* from mm_submenu where idSub='".$id."'";
		$q=mysql_query($sql);
		$r=mysql_fetch_array($q);
		$this->cerrar();
		return($r["nombre"]);
	}
	public function devolverMenu($id){
		$sql="select* from mm_coti_categoria where idCategoria='".$id."'";
		$q=mysql_query($sql);
		$r=mysql_fetch_array($q);
		$this->cerrar();
		return($r["nombre"]);
	}
	 public function desplegarDatosPagina($n=false){
        $c=1;
        $numero_colores=2;      
            
        $this->paginator->agregarConsulta($this->sql);  
        echo "<table width='100%' border=0  class='table' cellspacing=0 cellpadding=0>";
        echo "<tr height='30px'>";
       
        $k=0;
        foreach($this->campos as $clave=>$valor){
            
            echo "<td width='".$this->tamCol[$k]."%'><span style='padding:10px;font-family:arial; font-size:14px; font-weight:bold;'>".ucfirst($valor)."</span></td>";
            $k++;
        }
        echo "<td width='1%' colspan=3><span style='font-size:15px; font-weight:bold;'>Acciones</span></td>";
        
        echo "</tr>";
	 
        $this->paginator->estableceIndex($this->index);
        $total=$this->paginator->obtenerTotalReg();
          $numCol=$this->devolverColumnas()+2;
        if($total==0){
            echo "<tr><td colspan='".$numCol."'>Sin Contenido</td></tr>";
        }else{      
            while($row=$this->paginator->devolverResultados()){
              
                $c++;
            
                echo "<tr valign='top'>";                
                 
                foreach($this->campos as $clave=>$valor){                    
                    if($clave==$this->campoFoto[$clave]){
                        echo "<td style='margin-left:0px;' align='center'>";
						if(empty($row[$clave])){
							echo "<img src='./imagen/sin.jpg' style='margin-left:0px; margin-top:5px; padding:2px;border-width:1px; border-style:solid; border-color:#f3f3f3;'  height='60' width='80'/>";
                      
						}else{
							echo "<img src='./upload/".trim($row[$clave])."' style='margin-left:0px; margin-top:5px; padding:2px;border-width:1px; border-style:solid; border-color:#f3f3f3;'  height='60' width='80'/>";
                        }
						echo "</td>";    
                    }else{
                     
                        echo "<td style='padding-left:4px;padding-top:5px;padding-bottom:10px;'>";                        
                        if(is_array($this->opciones[$clave])){
                            echo '<span style="font-family:arial; margin-top:5px; font-size:14px;">'.$this->opciones[$clave][$row[$clave]].'</span>';
                        }else{
                            if($clave=="fecha"){
                                   echo '<span style="margin:5px; font-family:arial; font-size:14px;">'.date("m-d-Y",$row[$clave])."</span>";
                            }else if($clave=="idCate"){
										 echo '<span style="margin:10px; font-family:arial; font-size:14px;">'.ucfirst(strtolower($this->devolverMenu($row["idCate"]))).'</span>';
						
							}else if($clave=="idSubMenu"){
									echo '<span style="margin:10px; font-family:arial; font-size:14px;">'.ucfirst(strtolower($this->devolverSubMenu($row["idSubMenu"]))).'</span>';
						
							}else if($clave=="descripcion"){
                            		 echo '<span style="margin:10px; font-family:arial; font-size:14px;">'.substr($row[$clave],0,105).'...</span>';
							  }else if($clave=="texto"){
                            		 echo '<span style="padding:10px; font-family:arial; font-size:14px;">'.substr($row[$clave],0,105).'...</span>';
                            }else{
                            	 
                               		 echo '<span style="padding:10px;font-family:arial; font-size:14px;">'.$row[$clave].'</span>';
								 
                            }
                        }                        
                        echo "</td>";
                    }                    
                }        
                if($this->producto!=false){
                    echo "<td style='margin:10px;'>";
                    echo "<a href='".$this->index."&mq=ver&idq=".$row[$this->campoIndice]."'>Ver Fotos</a>";
                    echo "</td>";
                }
                echo "<td align='center'>";
                echo "<a href='".$this->index."&mq=editar&idq=".$row[$this->campoIndice]."'><img style='width:20px;padding-top:5px;' src='./imagen/editar.png'></a>";
                echo "</td>";
                echo "<td align='center'>";
                echo "<a href='".$this->index."&mq=borrar&idq=".$row[$this->campoIndice]."'><img style='padding-top:5px;width:20px;' src='./imagen/boton.png'></a>";
                echo "</td>";          
                echo "</tr>";       
            } 
        }    
      
        if($total>5){
         echo '<tr><td colspan='.$numCol.' valign="top">';
            echo "<table width='100%' border=0>";
            echo "<tr><td width='20%'>&nbsp;</td><td>";
            $this->paginator->navegacion();
            echo "</td><td width='20%'>&nbsp;</td></tr>";
            echo "</table>";
            echo '</td></tr>';
            }
            
        echo "</table>";
    }
	public function desplegarDatosMenu($n=false){
        $c=1;
        $numero_colores=2;      
            
        $this->paginator->agregarConsulta($this->sql);  
		 
        echo "<table width='100%' border=0 class='table' cellspacing=0 cellpadding=0>";
        echo "<tr height='30px'>";
       
        $k=0;
        foreach($this->campos as $clave=>$valor){
            
            echo "<td width='".$this->tamCol[$k]."%'><span style='padding:10px;font-family:arial; font-size:14px; font-weight:bold;'>".ucfirst($valor)."</span></td>";
            $k++;
        }
        echo "<td width='1%' colspan=3>Acciones</td>";
        
        echo "</tr>";
        $this->paginator->estableceIndex($this->index);
        $total=$this->paginator->obtenerTotalReg();
          $numCol=$this->devolverColumnas()+2;
        if($total==0){
            echo "<tr><td colspan='".$numCol."'>Sin Contenido</td></tr>";
        }else{      
            while($row=$this->paginator->devolverResultados()){
                
                $c++;
            
                echo "<tr valign='top' >"; 
				echo "<td style='padding-left:10px;padding-top:5px;padding-bottom:10px;'>";         
                echo '<span style="margin:10px; font-family:arial; font-size:14px;">'.$row["nombre"]."</span>";
				echo "</td>";
				echo "<td align='center'>";
                echo "<a href='".$this->index."&mq=editar&idq=".$row[$this->campoIndice]."'><img style='width:20px;padding-top:5px;' src='./imagen/editar.png'></a>";
                echo "</td>";
                echo "<td align='center'>";
                echo "<a href='".$this->index."&mq=borrar&idq=".$row[$this->campoIndice]."'><img style='padding-top:5px;width:20px;' src='./imagen/boton.png'></a>";
                echo "</td>";          
                echo "</tr>";  
				
				$idMenu=$row["idCategoria"];
				$sql2="select* from mm_submenu where idCategoria='".$idMenu."'";
				$q2=mysql_query($sql2);
				if(mysql_num_rows($q2)!=0){
				// SubMenu
				while($r1=mysql_fetch_array($q2)){
				  echo "<tr valign='top' bgcolor='".$color."'>"; 
				echo "<td style='padding-left:30px;padding-top:5px;padding-bottom:10px;'>";         
                echo '<span style="margin:10px; font-family:arial; font-size:14px;">'.$r1["nombre"]."</span>";
				echo "</td>";
				     echo "<td align='center'>";
                echo "<a href='".$this->index."&mq=editar&sb=menu&idq=".$r1["idSub"]."'><img style='width:20px;padding-top:5px;' src='./imagen/editar.png'></a>";
                echo "</td>";
                    echo "<td align='center'>";
                echo "<a href='".$this->index."&mq=borrar&sb=menu&idq=".$r1["idSub"]."'><img style='padding-top:5px;width:20px;' src='./imagen/boton.png'></a>";
                echo "</td>";          
                echo "</tr>"; 
				}
				}
				
				
            } 
        }    
      
        if($total>5){
         echo '<tr><td colspan='.$numCol.' valign="top">';
            echo "<table width='100%' border=0>";
            echo "<tr><td width='20%'>&nbsp;</td><td>";
            $this->paginator->navegacion();
            echo "</td><td width='20%'>&nbsp;</td></tr>";
            echo "</table>";
            echo '</td></tr>';
            }
            
        echo "</table>";
    }
	
	public function totalFotos($idProp){
		$sql="select count(*) as total from mm_cape_fotos where idProp='".$idProp."'";
	 
		$query=mysql_query($sql) or die(mysql_error());
		$row=mysql_fetch_array($query);
		return($row["total"]);
		
	}
 	public function devolverFoto($idProp){
		$s="SELECT * FROM `mm_cape_fotos` WHERE idProp='".$idProp."' order by idFoto asc";
		 
		$q=mysql_query($s);
		$r=mysql_fetch_array($q);
		$foto=$r["ruta"];
		return($foto);
	}
 public function desplegarDatos11($n=false,$numFotos=false){
 
        $c=1;
        $numero_colores=2;      
            
        $this->paginator->agregarConsulta($this->sql);  
        echo "<table width='100%' id='tbl' border=0 class='table bordered' cellspacing=0 cellpadding=6>";
		 
		
        echo "<tr   style='background:url(./imagen/background.jpg) repeat-x;'>";
        if($this->boton){
        //    echo "<td width='0%'><input type='checkbox' id='opciones' name='opciones'></td>";
      
        }
        $k=0;
        foreach($this->campos as $clave=>$valor){
            
            echo "<td width='".$this->tamCol[$k]."%'><span style='font-family:arial; font-size:14px; font-weight:bold;'>".$valor."</span></td>";
            $k++;
        }
        echo "<td>N°&nbsp;Fotos</td>";
        echo "<td width='1%' colspan=3>Acciones</td>";
        
        echo "</tr>";
        $this->paginator->estableceIndex($this->index);
        $total=$this->paginator->obtenerTotalReg();
          $numCol=$this->devolverColumnas()+2;
        if($total==0){
            echo "<tr id='tbl'><td colspan='".$numCol."'>Sin Contenido</td></tr>";
        }else{      
            while($row=$this->paginator->devolverResultados()){
                $color=$this->listaColores[$c % $numero_colores];
                $c++;
            
                echo "<tr id='tbl' valign='top' bgcolor='".$color."'>";                
                
                foreach($this->campos as $clave=>$valor){
                	$idProp=$row[$this->campoIndice];
					 
                	                    
                	                    
                    if($clave==$this->campoFoto[$clave]){
                     	$foto=$this->devolverFoto($idProp);
					 
                        echo "<td>";
                        echo "<img src='./upload/".$foto."' height='50' width='70'/>";
                        
                    }else{
                        
                        echo "<td>";                        
                        if(is_array($this->opciones[$clave])){
                            echo '<span style="font-family:arial; font-size:14px;">'.$this->opciones[$clave][$row[$clave]].'</span>';
                        }else{
                            if($clave=="fecha"){
                                echo date("m-d-Y",$row[$clave]);
                            }else if($clave=="descripcion"){
                            		 echo '<span style="font-family:arial; font-size:14px;">'.substr($row[$clave],0,205).'...</span>';
                            }else if($clave=="numFotos"){
                            	echo $this->totalFotos($idProp);
                            	 
                            }else{
                           
                               		 echo '<span style="font-family:arial; font-size:14px;">'.$row[$clave].'</span>';
								 
                            }
                        }                        
                        echo "</td>";
						   
                    }                    
                }        
                if($this->producto!=false){
                    echo "<td>";
                    echo "<a href='".$this->index."&mq=ver&idq=".$row[$this->campoIndice]."'>Ver Fotos</a>";
                    echo "</td>";
                    
                }
				
				 echo "<td>";
                echo "<a href='".$this->index."&mq=editarFotos&mod=panel&op=4&idq=".$row[$this->campoIndice]."'>Editar&nbsp;Fotos</a>";
                echo "</td>";
                echo "<td>";
                echo "<a href='".$this->index."&mq=editar&mod=panel&op=4&idq=".$row[$this->campoIndice]."'>Editar</a>";
                echo "</td>";
                echo "<td>";
                echo "<a href='".$this->index."&mq=borrar&mod=panel&op=4&idq=".$row[$this->campoIndice]."'>Borrar</a>";
                echo "</td>";                
                echo "</tr>";       
            } 
        }    
      
        if($total>5){
         echo '<tr><td colspan=11 align="center" align="top">';
         
            $this->paginator->navegacion();
             
            echo '</td></tr>';
            }
            
        echo "</table>";
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
		if($id==1){
			$k="Casa";
		}else if($id==2){
			$k="Departamentos";
		}else if($id==3){
			$k="Parcelas";
		}else if($id==4){
			$k="Sitios";
		}else if($id==5){
			$k="Oficina Comercial";
		}else if($id==6){
			$k="Propiedad Industrial";
		}else if($id==7){
			$k="Terreno";
		}
		return($k);
	}
	public function devolverCiudad($id){
		$sql="select* from mm_ciudad where idCiudad='".$id."'";
		$query=mysql_query($sql) or die(mysql_error());
		$row=mysql_fetch_array($query);
		$this->cerrar();
		return($row["ciudad"]);
	}
 public function desplegarDatos113($n=false,$numFotos=false){
 
        $c=1;
        $numero_colores=2;      
            
        $this->paginator->agregarConsulta($this->sql);  
        echo "<table width='100%' id='tbl' border=0 class='table' cellspacing=0 cellpadding=6>";
	 
        echo "<tr  >";
        if($this->boton){
        //    echo "<td width='0%'><input type='checkbox' id='opciones' name='opciones'></td>";
      
        }
        $k=0;
        foreach($this->campos as $clave=>$valor){
             
            echo "<td width='".$this->tamCol[$k]."%'><span style='font-family:arial; font-size:14px; font-weight:bold;'>".ucfirst($valor)."</span></td>";
            $k++;
        }
 
        echo "<td width='1%' style='font-size:14px;' colspan=3><b>Acciones</b></td>";
        
        echo "</tr>";
        $this->paginator->estableceIndex($this->index);
        $total=$this->paginator->obtenerTotalReg();
          $numCol=$this->devolverColumnas()+2;
        if($total==0){
            echo "<tr id='tbl'><td>Sin Contenido</td></tr>";
        }else{      
            while($row=$this->paginator->devolverResultados()){
                
                $c++;
            
                echo "<tr id='tbl' valign='top'>";                
                
                foreach($this->campos as $clave=>$valor){
         
                	$idProp=$row[$this->campoIndice];
					 
                	                    
                	                    
                    if($clave==$this->campoFoto[$clave]){
                     	$foto=$this->devolverFoto($idProp);
					 
                        echo "<td>";
                        echo "<img src='./upload/".$foto."' height='50' width='70'/>";
                        
                    }else{
                     
                        echo "<td>";                        
                        if(is_array($this->opciones[$clave])){
                            echo '<span style="font-family:arial; font-size:14px;">'.$this->opciones[$clave][$row[$clave]].'</span>';
                        }else{
                            if($clave=="fecha"){
                                echo date("m-d-Y",$row[$clave]);
                            }else if($clave=="descripcion"){
                            		 echo '<span style="font-family:arial; font-size:14px;">'.substr($row[$clave],0,205).'...</span>';
                            }else if($clave=="numFotos"){
                            	echo $this->totalFotos($idProp);
                            	 
                            
                            }else if($clave=="precio"){
								
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
							}else if($clave=="operacion"){
								echo '<span style="font-family:arial; font-size:14px;">'.$this->devolverOperacion($row[$clave]).'</span>';
							}else if($clave=="tipoProp"){
								
									 echo '<span style="font-family:arial; font-size:14px;">'.$this->devolverTipoProp($row[$clave]).'</span>';
						
							}else if($clave=="ciudad"){
							
									 echo '<span style="font-family:arial; font-size:14px;">'.$this->devolverCiudad($row[$clave]).'</span>';
							}else{
                             	 echo '<span style="font-family:arial; font-size:14px;">'.$row[$clave].'</span>';
								 
                            }
                        }                        
                        echo "</td>";
						   
                    }                    
                }        
                
                echo "<td align='center'>";
                echo "<a href='panel.php?mq=editar&mod=panel&op=4&idq=".$row[$this->campoIndice]."'><img style='width:20px;padding-top:5px;' src='./imagen/editar.png'></a>";
                echo "</td>";
                echo "<td align='center'>";
                echo "<a href='panel.php?mq=borrar&mod=panel&op=4&idq=".$row[$this->campoIndice]."'><img style='padding-top:5px;width:20px;' src='./imagen/boton.png'></a>";
                echo "</td>";                
                echo "</tr>";       
            } 
        }                
        echo "</table>";		 
        if($total>5){
			echo "<div align='center'>";
         
            $this->paginator->navegacion();
             
            echo "</div>";
            }
    }     
}
?>