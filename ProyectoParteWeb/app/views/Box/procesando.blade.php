
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
  <style type="text/css">
    body {
          padding-top: 50px;
    }
    .starter-template {
      padding: 40px 15px;
      text-align: center;
    }

    p{
      text-align: center;
    }

    progress[value] {
      /* Elimino la apariencia por defecto */
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
       
      /* Quito el borde que aparece en Firefox */
      border: none;
   
      /* Añado mis propios estilos */
      width: 1170px;
      height: 40px;
      overflow:hidden;
       
  /*  Estos estilos solo se aplicaran al fondo de la barra en mozilla */
      border:1px inset #666;
      background-color:#D8D8D8;
      border-radius : 20px ;
    }
   
   progress::-moz-progress-bar{
      background: #FF8000;
      border-radius : 20px;
   }

  </style>
  <script type="text/javascript">
      var progresso = new Number();
      var maximo = new Number(); 
      var progresso = 0;
      var maximo = 100;
          function start(){

            if(progresso < maximo){
              progresso = progresso + 1;
              document.getElementById("barra").value=progresso;
              setTimeout("start();",100);
              
            }
            else{
              
              progresso = 0;
              setTimeout("start();",100);
              
            }
            

          }
 
      
  </script>
  
</head>

<body onload="start();">
  <p>Processing file</p>
	<div class="progress">
      <progress max="100" id="barra"></progress>
      
  </div>



<table border= "solid" width= "50%">
  <tr>
    <th>Id</th>
    <th>File</th>
    <th>Id File</th>
  </tr>
  
</table>

</body>
</html>


