 
<?php
	require_once __DIR__ . '/vendor/autoload.php';
	use PhpAmqpLib\Connection\AMQPConnection;
		//Conexión y designación del canal
		$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
		$channel = $connection->channel();
		$channel->queue_declare('audio', false, false, false, false);		
			echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

 //Función que busca mensajes SACANDO LA PRIMERA COLA, la cual se repite hasta que se presione CRTL+C
 $callback = function($msg) 
{
	echo " [x] Received ", $msg->body, "\n";

	//Decodificación del json
	$mensaje = json_decode($msg->body,true);
	$id = $mensaje["id"];
	$file = $mensaje["file"];
	$partes = $mensaje["parts"];
    $time = $mensaje["time_per_chunk"];
    var_dump($file);

    //"shell_exec" funciona para abrir (ejecutar) programas en consola y se le envia el archivo
	$tiempo = shell_exec('ffmpeg -i '.$file.' 2>&1 |grep -oP "[0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{2}"');           
	//Se seleciona toda la dirección del archivo y se va colocando en el array cada "/", Se parte por / el url
	$array = explode("/", $file);
	var_dump($array);
	//Mide el array y lo coloca en la última posición
    $status = count($array)-1;          
    $fileName = $array[$status];
    //var_dump($fileName);

    //Se le vuelve hacer el explode para que sea por punto, con solo el nombre
    $arrayfile = explode(".", $fileName);
    $statusfile = count($arrayfile)-1;          
    $extencion = $arrayfile[$statusfile];
    //var_dump($extencion);
    
    //El explode para deshacerse de la palabra minutos: "4 minutos"          
    $arrayTime = explode(" ", $time);         
    $minutos = $arrayTime[0];
    //var_dump($minutos);

    //Si cumple con esas condiciones se hace por partes
    if(($minutos=="")||($minutos==0)||($minutos== null))
    {   
    	//El split ordena partir el string en 4 variables cada : según ffmpeg     
        list($horas,$minutos,$segundos,$microsegundos) = split("[:.]",$tiempo);
        $duracion = $horas.$minutos.$segundos.$microsegundos;  
        //$partes = 2;
        $microsegundos = '10';
        //$horas = 1 ;
       // $minutos = 30;

		$totalSegundos = $segundos;//no hay que convertirlos
       
       //Se trata de nivelar a un grado de iguales
		if($horas > 0){
			$horas = $horas*60; //1 h. = 60 min.
           $totalSegundos += $horas*60; //1 min. = 60 s
           var_dump($totalSegundos."segundos con las horas");
		}       
		if($minutos > 0){
            $totalSegundos += $minutos*60;
            var_dump($totalSegundos."segundos con las horas minutos");
		}
		//var_dump("segundos totales".$totalSegundos);

		//Estas variables vuelven a cero porque se necesita armar de nuevo
		$horas = "0";
        $minutos ="0";
        $segundos = "0";
        //Se divide en las partes que el usuario digitó
		$totalSegundos = $totalSegundos / $partes;
        // var_dump($totalSegundos."segundos por parte ");

        //Se pasan segundo a minutos
         if($totalSegundos > 59)
         {
             $minutos = $totalSegundos / 60;
             //Si se cumple la condicción quiere decir que hay horas
             if($minutos > 59)
             {
                $horas = $minutos / 60;
                $minutos='0';

                //Significa que si la conversión anterior dio 1,86 -> hay minutos
                if(is_float($horas)) // Tiene decimal?
                {
                	//Se quita por el "."
	                $horas =  explode(".", $horas);
	                $minutos = '0.'.$horas[1];
	                $horas = $horas[0];
	                //Se necesita aún pasarlo a minutos porque el ",86" todavía son horas
	                $minutos = $minutos * 60;
	                if(is_float($minutos))	                	
                    {
                    	$minutos =  explode(".", $minutos);
	                    $segundos = '0.'.$minutos[1];
	                     $minutos = $minutos[0];
	                     //Se necesita aún pasarlo a segundos porque siguen siendo minutos
	                    $segundos = $segundos * 60;
	                    //Se redondea porque microsegundos no es indispensable 
	                    $segundos =round($segundos, 0, PHP_ROUND_HALF_UP); 
	                    $segundos = floor($segundos);                          
                    }
                }
             }
             else//Si los minutos no son mayor a 59
             {
             	if(is_float($minutos))//Si es un float se hace la converción para sacarle segundos	                	
                    {
                    	//var_dump($minutos."minutos enteros ");
                    	$minutos =  explode(".", $minutos);	                   
	                    $segundos = "0.".$minutos[1];
	                    $minutos = $minutos[0];
	                  //  var_dump($minutos.'minutos');
	                   // var_dump($segundos.'segundos sin convertir');
	                    $segundos = $segundos * 60;
	                    $segundos =round($segundos,0, PHP_ROUND_HALF_UP);
                       $segundos = floor($segundos);                       
                    }
             }

         }
         else//Si los segundos no son mayor a 59, lo que significa que solo va a haber segundos, no hay min ni hrs
         {
         	$segundos = $totalSegundos;
         	$segundos = floor($segundos);
         	//var_dump($segundos);
         }


        //12:00:00:00 
	    if($horas > 9)
		{
			$horas = $horas.":";
			//horas
		}
		else //08:00:00:00
		{
            $horas = "0".$horas.":";
		}

		if($minutos > 9)
		{
			$minutos = $minutos.":";	                    			         			     
		}
		else
		{
           $minutos = "0".$minutos.":";
		}

		if($segundos > 9)
		{
           $segundos = $segundos.".";
		}
		else
		{
			$segundos = "0".$segundos.".";
		}
	 $duracion = $horas.$minutos.$segundos.$microsegundos;	

	//var_dump($duracion."de cada parte");	
    }
    	  // Si no hay que partir en partes sino en el tiempo
    else //Si NO cumple las condiciones if(($minutos=="")||($minutos==0)||($minutos== null))
    {
            if($minutos > 59)
            {
               $horas =$minutos/60;
                //var_dump($horas);
                if(is_float($horas))
                {
                    $horas =  explode(".", $horas);
                    $hora = $horas[0];                      
	                $minutos = '0.'.$horas[1];
	                $minutos= $minutos * 60 ;

	                //var_dump($minutos);
	                $minutos =round($minutos, 0, PHP_ROUND_HALF_UP); 
	                $segundos = floor($segundos);
                }
                else//Si horas no tiene decimales significa que solo hay hora
                {
                    $minutos="00";
                    $hora = $horas;
                }
	              	                
	                //var_dump($minutos);
                    //var_dump($hora.':'.$minutos);
                    $duracion = "0".$hora.":".$minutos.":"."00".".00";
	        } 
	        else//Si los minutos no son mayor ha 59 significa que solo hay minutos
	        {                       
                 $duracion = "00".":".$minutos.":"."00".".00";
            }
   var_dump($duracion);
    }  
   $fin = "00:00:00.00";
    //$duracion = "00:01:59.07";
    $numero = 0;  
    $ruta = explode(".", $file);
    list($horas2,$minutos2,$segundos2,$microsegundos2) = split("[:.]",$duracion);       
    $segundosCadaCorte = $segundos2;
    if($horas2 > 0)
    {
	     $horas2 = $horas2*60;
         $segundosCadaCorte += $horas2*60;
	}       
	if($minutos2 > 0)
	{
        $segundosCadaCorte += $minutos2*60;
	}
 
    list($horas,$minutos,$segundos,$microsegundos) = split("[:.]",$tiempo);
    $segundosDuracion = $segundos;
    if($horas > 0)
    {
	     $horas = $horas*60;
         $segundosDuracion += $horas*60;
	}       
	if($minutos > 0)
	{
        $segundosDuracion += $minutos*60;
	} 

  do{ 	
    $segundosDuracion = $segundosDuracion - $segundosCadaCorte;
  	$numero= $numero +1;//Variable para las particiones, de modo que queda el mismo nombre pero .1, .2, etc. Nombre a las carpetas
    
    //La duración del corte que se va a hacer
  	//fin va ser igual = 00:00:00:00 la primera vez, luego va ser 00:00:30:00, -> 00:00:60:00, hasta que se agote la canción
    $consulta = shell_exec("ffmpeg -i ".$file." -acodec copy -t ".$duracion." -ss ".$fin." "."..".$ruta[2].$numero.".".$ruta[3]);        

    			//Parametros de Base de Datos
				$user = "postgres";
				$password = "12345";
				$dbname = "audios";
				$port = "5432";
				$host = "localhost";
				$cadenaConexion = "host=$host port=$port dbname=$dbname user=$user password=$password";
				#Conectamos con PostgreSQL
				//Se envia la conección
				$conexion = pg_connect($cadenaConexion) or die ("Fallo en el establecimiento de la conexión");

				#Efectuamos la consulta SQL
				//Se crea el URL (id, url)
				$URL = $ruta[2].$numero.".".$ruta[3];
				//var_dump($URL);
				$query = "insert into url(file,id_archivo) values ('".$URL."','".$id."')";
				//Se envia el insert
				$result = pg_query ($conexion, $query) or die("Error en la consulta SQL");
				pg_close($conexion);
  

       // var_dump( $ruta[2].$numero.".".$ruta[3]);
        var_dump($tiempo);
		var_dump($duracion."tamano de cada corte");
		var_dump($fin."donde inicia a cortar cadacorte");
		var_dump($segundosDuracion."  duracion restante");

       list($horas,$minutos,$segundos,$microsegundos) = split("[:.]",$fin); 
       list($horas2,$minutos2,$segundos2,$microsegundos2) = split("[:.]",$duracion); 
        $totalSegundos = $segundos + $segundos2;
		if($horas > 0){
			$horas = $horas*60;
            $totalSegundos += $horas*60;
        //var_dump($totalSegundos."segundos sumados");
		}       
		if($minutos > 0){
            $totalSegundos += $minutos*60;
            // var_dump($totalSegundos."segundos sumados");
		}

		if($horas2 > 0){
		   $horas2 = $horas2*60;
           $totalSegundos += $horas2*60;
             var_dump($totalSegundos."segundos sumados22");
		}
	     if($minutos2 > 0){
           $totalSegundos += $minutos2*60;
             var_dump($totalSegundos."segundos sumados22");
		}
		$horas = "0";
        $minutos ="0";
        $segundos = "0";
          var_dump($totalSegundos."total de segundos sumados");
         if($totalSegundos > 59)
         {
             $minutos = $totalSegundos / 60;
             if($minutos > 59)
             {
                $horas = $minutos / 60;
                $minutos='0';
                if(is_float($horas))
                {
	                $horas =  explode(".", $horas);
	                $minutos = '0.'.$horas[1];
	                $horas = $horas[0];
	                $minutos = $minutos * 60;
	                if(is_float($minutos))	                	
                    {
                    	$minutos =  explode(".", $minutos);
	                    $segundos = '0.'.$minutos[1];
	                     $minutos = $minutos[0];
	                    $segundos = $segundos * 60;
	                    $segundos =round($segundos, 0, PHP_ROUND_HALF_UP); 
	                    $segundos = floor($segundos);                          
                    }
                }
             }
             else
             {
             	if(is_float($minutos))	                	
                    {
                    	$minutos =  explode(".", $minutos);	                   
	                    $segundos = "0.".$minutos[1];
	                    $minutos = $minutos[0];
	                    $segundos = $segundos * 60;
	                    $segundos =round($segundos, 0, PHP_ROUND_HALF_UP); 
	                    $segundos = floor($segundos);                        
                    }
             }
         }
         else
         { 
         	$segundos = $totalSegundos;
         	$segundos = floor($segundos);
         }

	    if($horas > 9)
		{
			$horas = $horas.":";
			//horas
		}
		else
		{
            $horas = "0".$horas.":";
		}

		if($minutos > 9)
		{
			$minutos = $minutos.":";	                    			         			     
		}
		else
		{
           $minutos = "0".$minutos.":";
		}

		if($segundos > 9)
		{
           $segundos = $segundos.".";
		}
		else
		{
			$segundos = "0".$segundos.".";
		}
	    $fin= $horas.$minutos.$segundos.$microsegundos;
			
     }while(($segundosDuracion > 0)&&($segundosDuracion > 5 ));

};
		//Se consume el audio con todos los mensajes
		$channel->basic_consume('audio', '', false, true, false, false, $callback);
		while(count($channel->callbacks)) { //Esto es lo que hace que se encicle
		$channel->wait();
		}
		$channel->close();
		$connection->close();
		
?> 
