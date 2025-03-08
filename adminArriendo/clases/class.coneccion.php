<?php
ob_start();
error_reporting(0);

 
class coneccion
{
     public $server;
     public $user;
     public $pass;
     public $dataBase;
     public $coneccion;   
     public $link;
     public function __construct(){         
       $this->asignarDatosConeccion();
     }     
     public function asignarDatosConeccion(){        
        $this->server="localhost";
        $this->user="clickcorredores_clickcorredores2";        
		$this->pass="JV&K4I6_ee{=";
		$this->dataBase="clickcorredores_clicrdyj_ccl92865_clickcorredores"; 
        

        
     }
     public function prueba(){
        $con=mysqli_connect($this->server,$this->user,$this->pass);        
        if($con){
            echo "coneccion establecida";
        }else{
            echo "no se pudo establecer la coneccion";
        }
         return(true); 
     }
     public function consultar($cadena){
       $this->link=$this->conectar();	       
       
       $query=mysqli_query($this->link,$cadena) or die (mysqli_error($this->link));                    
       mysqli_close($this->link);
       return($query);       
       
     }
     public function ejecutar($cadena){ 
        $this->link=$this->conectar();	          
        $q=mysqli_query($this->link,$cadena) or die(mysqli_error($this->link));                
       mysqli_free_result($q);
       mysqli_close($this->link);
        return(true);  
     }      
     
     public function conectar(){
         $this->asignarDatosConeccion();      
		 $this->coneccion=mysqli_connect($this->server,$this->user,$this->pass);
		 if (mysqli_connect_errno()) {
			printf("Conexión fallida: %s\n", mysqli_connect_error());
			exit();
		}else{
			mysqli_select_db($this->coneccion,$this->dataBase) or die(mysqli_error($this->coneccion));
		}         
        return($this->coneccion);
     }
     public function desconectar(){
        if(!is_null($this->coneccion)){
            mysqli_close($this->coneccion);
        }
     }   
}   
 
?>
 