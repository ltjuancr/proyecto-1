
 <h1>Vistual Box</h1>


{{ Form::open(array('url' => 'Box')) }}
	{{ Form::label('audio', 'Audio') }}
	  <input type="file" name="audio" accept="audio/*">
	<br>
		{{ Form::label('partes', 'Cantidad de Partes ') }}
	 <input type="number" name="partes">
	<br>
		{{ Form::label('minutos', 'Minutos') }}
	 <input type="number" name="minutos">
	<br>
	{{Form::submit('Convertir', array())}}

{{ Form::close() }}



