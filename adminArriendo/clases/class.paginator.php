
<?php
ini_set('display_errors',1);

if(preg_match("/proceso7.php/i",$_SERVER["PHP_SELF"])){
    require_once("./clases/class.coneccion.php");    
}else{
    require_once("./clases/class.coneccion.php");
}
   require_once("./clases/class.coneccion.php");
   error_reporting(0);
class paginator extends coneccion{
    public $intervalo;
    public $numRegistros;
    public $numPagActual;
    public $inicio;
    public $coneccion;
    public $index;
    public $totalRegistros;
    public $arrayQuery;
    public $query;
    public $numPaginas;
    public $sql;
    public $link;
    public function __construct($numReg,$intervalo){     
           $this->link=$this->conectar();
        $this->numRegistros=$numReg;
        $this->intervalo=$intervalo;  
                              
    }
    public function estableceIndex($index){
        $this->index=$index;
    }
    public function calcularTotalRegistros(){  
        $this->link=$this->conectar();
        $query=$this->consultar($this->sql);
        $totalReg=mysqli_num_rows($query);        
        return($totalReg);
    }    
    public function obtenerTotalReg(){
        return($this->totalRegistros);
    }
    public function devolverResultados(){
        $this->arrayQuery=mysqli_fetch_array($this->query);        
        return($this->arrayQuery);
    }
    public function paginaActual(){
        if(isset($_GET["numPag"])){
            $numPag=htmlentities($_GET["numPag"]);            
        }else if(isset($_POST["numPag"])){
            $numPag=htmlentities($_POST["numPag"]);
        }else{
            $numPag=1;
        }
        return($numPag);
    }
    public function numInicio(){
        $this->numPagActual=$this->paginaActual();
        $inicio=($this->numPagActual-1)*$this->numRegistros;
        if($inicio<0){
            $inicio=0;
        }    
        return($inicio);
    }
  
    public function agregarConsulta($sql){ 
        $this->sql=$sql;		 
        $this->conectar();  
        $this->inicio=$this->numInicio();    
		
        $cadena =$sql." limit ".$this->inicio.",".$this->numRegistros;   
            
        $this->query=$this->consultar($cadena);
        $this->totalRegistros=$this->calcularTotalRegistros();
    } 
     public function navegacion($col=false){
        $this->numPagActual=$this->paginaActual();
        $this->totalReg=$this->calcularTotalRegistros();        
        $this->numPaginas=ceil($this->totalReg/$this->numRegistros);
        echo '<nav aria-label="Page navigation example">';

    

         echo ' <ul class="pagination  pagination-'.$col.'">';
         if($this->numPagActual>5 || $this->numPagActual<2){
            $pagina=1;
            echo '<li  class="page-item "><a class="page-link"  style="color:gray !important;padding-top:8px;height:100%;" href="'.$this->index."&numPag=".$pagina.'#final"><i class="fas fa-step-backward"></i></a></li>';
          }
         if($this->numPagActual>1){			
            $pagina=$this->numPagActual-1;
            
            echo '<li  class="page-item"><a class="page-link"  style="color:gray !important;padding-top:8px;height:100%;" href="'.$this->index."&numPag=".$pagina.'#final"><i class="fas fa-fast-backward"></i></a></li>';
         }
         $i=1;
         $inicio=($this->numPagActual-1);
         if($inicio==0){$inicio=1;}
         $fin=$inicio+$this->intervalo;        
         for($i=$inicio; $i<=$this->numPaginas; $i++){	
             if($i<$fin){
                  if($i==$this->numPagActual){
                    echo '<li  class="page-item active"><a   style=" height:100%;color:white !important;" class="page-link" href="'.$this->index."&numPag=".$i.'#final">'.$i.'</a></li>';
                  }else{
                    echo '<li   class="page-item"><a   style=" height:100%;color:gray !important;" class="page-link" href="'.$this->index."&numPag=".$i.'#final">'.$i.'</a></li>';
                  }
                }
            }

            if($this->numPagActual<$this->numPaginas){
				$pagina=$this->numPagActual+1; 
                echo '<li  class="page-item"><a class="page-link"  style="padding-top:8px;height:100%;color:gray !important;" href="'.$this->index."&numPag=".$pagina.'#final"><i class="fas fa-fast-forward"></i></a></li>';
            }		
            
                if($this->numPagActual<7){
                    $pagina=$this->numPaginas; 
                    echo '<li  class="page-item"><a class="page-link"  style="padding-top:8px;height:100%;color:gray !important;"  href="'.$this->index."&numPag=".$i.'#final"><i class="fas fa-step-forward"></i></a></li>';
                }
         echo '
       </ul></nav>';
     }
	 
    public function navegacionAjax(){
         $this->numPagActual=$this->paginaActual();
            $this->totalReg=$this->calcularTotalRegistros();        
            $this->numPaginas=ceil($this->totalReg/$this->numRegistros);            
            echo '<div class="pagination pagination-small"><ul>';  
            if($this->numPagActual>5 || $this->numPagActual<2){
				$pagina=1;
				echo "<li><a style='font-family:arial; font-size:12px; border-color:black; border-width:1px; border-style:solid; font-color:black;' id='primera'  onclick='kPag(1);' href='#'>Primera</a></td>";
			}
            if($this->numPagActual>1){			
			$pagina=$this->numPagActual-1;
				echo "<td><a id='anterior' style='font-family:arial; border-color:black; border-width:1px; border-style:solid; font-size:12px; font-color:black;'  onclick='kPag(".$pagina.");' href='#'>&lt;&lt; anterior</a></td>";
			}            
        	$i=1;
		   $inicio=$this->numInicio();  
            if($inicio==0){$inicio=1;}
			$fin=$inicio+$this->intervalo;    
            
                           
			for($i=$inicio; $i<=$this->numPaginas; $i++){	
				if($i<$fin){
				     if($i==$this->numPagActual){
                        echo "<td class='active'><a  onclick='kPag(".$i.");' href='#'>".$i."</a></td>";
                    }else{
                        echo "<td><a  onclick='kPag(".$i.");' href='#'>".$i."</a></td>"; 
                    }
				}
			}
             
            if($this->numPagActual<$this->numPaginas){
				$pagina=$this->numPagActual+1; 
			     echo "<td><a  onclick='kPag(".$pagina.");' href='#'>Siguiente</a></td>";
			}			
			if($this->numPagActual<7){
				$pagina=$this->numPaginas; 
 				  echo "<td><a  onclick='kPag(".$pagina.");' href='#'>Ultima</a></td>";
			}
           echo "</ul>";
        echo "</div>"; 
        echo "<a href='final'><a>";
         }    
}
?>