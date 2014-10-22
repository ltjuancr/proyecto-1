
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
  {{HTML::style('style/index.css')}}

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

      $(function(){
          //configuraciones
          var _elem       = $('.animar-entrada');     //usar selector CSS para determinar los elementos a los que se aplica el efecto
          var _velocidad  = 400;                      //duración de la animación
          var _demora     = 200;                      //pausa entre cada letra
          //..
          _elem.each(function(){
              //acciones para cada elemento seleccionado
              var $esto = $(this);
              var arr_letras = $esto.text().split('');    //dividir el texto letra por letra
              $esto.html('');                             //vaciar el html del elemento
              for (var i=0; i<arr_letras.length; i++){    //encerrar cada letra en un <span> con clase especial, solo para identificarla
                  arr_letras[i] = $('<span class="jq-letra">' + arr_letras[i] + '</span>').hide();    //crear el elemento y ocultarlo
                  $esto.append(arr_letras[i]);                                                        //agregar elemento al texto
                  arr_letras[i].delay( i * _demora ).fadeIn(_velocidad);                              //mostrar letra con efecto
              }
          });
      });
  </script>
  
</head>

<body onload="start();">
  <div id="container2" >
    <p class="animar-entrada">Processing file</p>
    <br>
  	<div class="progress">
        <progress max="100" id="barra"></progress>
        
    </div>
  </div>
    <br>
    <center>
      <table border= "solid;" width= "90%;">
        <tr>
          <th>ID</th>
          <th>File</th>
          <th>ID File</th>
        </tr>
        
      </table>
    </center>
</body>
</html>


