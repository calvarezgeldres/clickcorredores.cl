<?php
/*
Autor: Programacion web chile
Fecha 4/4/2020
Descripción: Clase que interpreta datos estadisticos

*/ 
require_once("./clases/class.coneccion.php");

class est extends coneccion{
  public $link;
  public function __construct(){

  }
  public function numSistemaOperativo(){
    // personas por sistema operativo
    $this->link=$this->conectar();
    $sql="SELECT sistemaOperativo, count(sistemaOperativo) as total FROM coti_monitor group by (sistemaOperativo) limit 0,7";
    $q=mysqli_query($this->link,$sql);
    while($r=mysqli_fetch_array($q)){
      if($r["sistemaOperativo"]=="Desconocido"){
        $arch["Otro"]=$r["total"];
      }else{
        $arch[$r["sistemaOperativo"]]=$r["total"];      
      }
      
    }
    
    mysqli_free_result($q);      
    mysqli_close($this->link);  
    return($arch);
  }
  public function navegador22(){
    //  personas por navegador
    $this->link=$this->conectar();
    $sql="SELECT navegador, count(navegador) as total FROM coti_monitor group by (navegador) order by total desc limit 0,3";

    $q=mysqli_query($this->link,$sql);
    while($r=mysqli_fetch_array($q)){
      if($r["navegador"]=="Desconocido"){
        $arch["Otro"]=$r["total"];
      }else{
        $arch[$r["navegador"]]=$r["total"];      
      }
      
    }
    mysqli_free_result($q);      
    mysqli_close($this->link);  
    return($arch);
  }
  public function numPorDispositivo(){
    // total por dispositivos
    $this->link=$this->conectar();
    $sql="SELECT disp, count(disp) as total FROM coti_monitor group by (disp) order by total desc";
    $q=mysqli_query($this->link,$sql);
    while($r=mysqli_fetch_array($q)){
      if($r["disp"]=="Desconocido"){
        $arch["Otro"]=$r["total"];
      }else{
        $arch[$r["disp"]]=$r["total"];      
      }
    }

    mysqli_free_result($q);      
    mysqli_close($this->link);  
    return($arch);
  }
  public function numPorPais(){
    // total por pais
    $this->link=$this->conectar();
    $sql="SELECT pais, count(pais) as total FROM coti_monitor group by (pais)";
    $q=mysqli_query($this->link,$sql);
    while($r=mysqli_fetch_array($q)){
      $arch[$r["pais"]]=$r["total"];      
    }
    mysqli_free_result($q);      
    mysqli_close($this->link);  
    return($arch);
  }
  public function TotalPorCiudad(){
    // total por ciudad
    $this->link=$this->conectar();
    $sql="SELECT ciudad, count(ciudad) as total FROM coti_monitor group by (ciudad) order by total desc";
    $q=mysqli_query($this->link,$sql);
    while($r=mysqli_fetch_array($q)){
      $arch[$r["ciudadr"]]=$r["total"];      
    }
    mysqli_free_result($q);      
    mysqli_close($this->link);  
    return($arch);
  }
  public function paisMes(){
    // cantidad de pais por mes
    $this->link=$this->conectar();
    $sql="SELECT from_unixtime(fecha,'%M') as mes , from_unixtime(fecha,' %Y') as ano , count(fecha) as total FROM coti_monitor WHERE from_unixtime(fecha,'%Y')='2020' group by (from_unixtime(fecha,'%m'))";
    $q=mysqli_query($this->link,$sql);
    $ano=date("Y");
    while($r=mysqli_fetch_array($q)){
      if($r["ano"]!=$ano){
        $arch[$r["mes"]][$r["ano"]]=$r["total"];
      }else  if($r["ano"]==$ano){
          $arch[$r["mes"]][$r["ano"]]=$r["total"];
          arsort($arch);
        }
    }
   
    mysqli_free_result($q);      
    mysqli_close($this->link);  
    return($arch);


  }
  public function devolverMes($m){
    if($m=="January"){
      $k="Ene";
    }else if($m=="February"){
      $k="Feb";
    }else if($m=="March"){
      $k="Mar";
    }else if($m=="April"){
      $k="Apr";
    }else if($m=="May"){
      $k="May";
    }else if($m=="June"){
      $k="Jun";
    }else if($m=="July"){
      $k="Jul";
    }else if($m=="August"){
      $k="Ago";
    }else if($m=="September"){
      $k="Sept";
    }else if($m=="October"){
      $k="Oct";
    }else if($m=="November"){
      $k="Nov";
    }else if($m=="December"){
      $k="Dic";
    }
    return($k);
  }
  public function convertirDona($datos,$titulo){
    $sql='var ctx44 = document.getElementById("doughnutChart").getContext("2d");
    var doughnutChart = new Chart(ctx44, {
      type: "doughnut",
      data: {
          datasets: [{
            data: [';
            foreach($datos as $clave=>$valor){
              $sql.=$valor.",";
          }
          $sql=substr($sql,0,-1);
            $sql.='],
            backgroundColor: [
              "rgba(255,99,132,1)",
              "rgba(54, 162, 235, 1)",
              "rgba(255, 206, 86, 1)",
              "rgba(75, 192, 192, 1)",
              "rgba(153, 102, 255, 1)",
              "rgba(255, 159, 64, 1)"
            ],
            label: "'.$titulo.'"
          }],
          labels: [';
            foreach($datos as $clave=>$valor){
                $sql.="'".$clave."',";
            }
            $sql=substr($sql,0,-1);
            $sql.='
          ]
        },
        options: {
          responsive: true
        }
     
    });';
    echo $sql;
  }
  public function convertirPie($datos,$titulo){
      $sql='var ctx3 = document.getElementById("pieChart").getContext("2d");
      var pieChart = new Chart(ctx3, {
        type: "pie",
        data: {
            datasets: [{
              data: [';
              foreach($datos as $clave=>$valor){
                  $sql.=$valor.",";
              }
              $sql=substr($sql,0,-1);
            $sql.='],
              backgroundColor: [
                "rgba(255,99,132,1)",
                "rgba(54, 162, 235, 1)",
                "rgba(255, 206, 86, 1)",
                "rgba(75, 192, 192, 1)",
                "rgba(153, 102, 255, 1)",
                "rgba(255, 159, 64, 1)"
              ],
              label: "'.$titulo.'"
            }],
            labels: [';
            foreach($datos as $clave=>$valor){
              $sql.="'".$clave."',";
            }
            $sql=substr($sql,0,-1);
            $sql.=']
          },
          options: {
            responsive: true
          }
       
      });';
      echo $sql;
  }
  public function convertirCombo($datos,$titulo){
    
    $sql='var ctx2 = document.getElementById("comboBarLineChart").getContext("2d");
    var comboBarLineChart = new Chart(ctx2, {
      type: "bar",
      data: {
        labels: [';

        foreach($datos as $clave=>$valor){
          $sql.="'".$this->devolverMes($clave)."',";
        }
        $sql=substr($sql,0,-1);
        $sql.='],
        datasets: [  {
            type: "bar",
            label: "'.$titulo.'",
            backgroundColor: "#3EB9DC",
            data: [';
            foreach($datos as $clave=>$arr){
              foreach($arr as $clave1=>$valor1){
               
                  $sql.= $valor1.",";
                
              }
            }
            $sql=substr($sql,0,-1);
            $sql.='],
            borderColor: "white",
            borderWidth: 0
          } ], 
          borderWidth: 1
      },
      options: {
        scales: {
          yAxes: [{
            ticks: {
              beginAtZero:true
            }
          }]
        }
      }
    });	';
    echo $sql;
  }
  public function convertirBarras($datos,$titulo){
   
    $sql= '	var ctx1 = document.getElementById("lineChart").getContext("2d");
    var lineChart = new Chart(ctx1, {
      type: "bar",
      
      data: {
        labels: [';
        foreach($datos as $clave=>$valor){
          $sql.="'".$clave."',";
        }
      $sql=substr($sql,0,-1);
      $sql.='],
        datasets: [{
            label: "'.$titulo.'",
            backgroundColor: "#3EB9DC",
            data: [';
            foreach($datos as $clave=>$valor){
              $sql.=$valor.",";
            }
            $sql=substr($sql,0,-1);
            $sql.= '] 
          }]
          
      },
      options: {
              tooltips: {
                mode: "index",
                intersect: false
              },
              responsive: true,
              scales: {
                xAxes: [{
                  stacked: true,
                }],
                yAxes: [{
                  stacked: true
                }]
              }
            }
    });
  ';
  echo $sql;
  }
  public function __destruct(){
     mysqli_close($this->link);
  }
}

?>