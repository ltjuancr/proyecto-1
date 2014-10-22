<!DOCTYPE html>
<html>
<head>
	{{HTML::style('bootstrap/css/bootstrap.min.css')}}
	{{HTML::style('style/index.css')}}

	<script type="text/javascript">
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
<body id="header" >
	<div id="container" style="height: 550px;">
		 <h1 class="animar-entrada" id="title">Music Box</h1>
		 <div id="letra">
		 	<nav>
		 		

			{{ Form::open(array('url' => 'audio','files' => 'true','enctype' => "multipart/form-data")) }}

				<div id="label">
					{{ Form::label('audio', 'Audio:') }}
				</div>
				<div id="input" style="color: white;">
				  	<input type="file" name="audio" accept="audio/*">
				</div>
				<br>

				<div style="margin-left: 200px;">
					{{ Form::label('partes', 'Cantidad de Partes: ') }}
				</div>
				<div id="input">
				 	<input type="number" name="partes">
				 </div>
				<br>
				<div style="margin-left: 320px;">
					{{ Form::label('minutos', 'Minutos:') }}
				</div>
				<div id="input">
				 	<input type="number" name="minutos">
				</div>
				<br>
				<br>
				<div style="margin-left: 500px;" >
			    	{{Form::submit('Convertir', array())}}
			    </div>

			{{ Form::close() }}
			{{HTML::script('bootstrap/js/bootstrap.min.js')}}
				
			</nav>
		</div>
	</div>
</body>
</html>


