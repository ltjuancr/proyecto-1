 
<?php
	require_once __DIR__ . '/vendor/autoload.php';
	use PhpAmqpLib\Connection\AMQPConnection;
		$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
		$channel = $connection->channel();
		$channel->queue_declare('audio', false, false, false, false);		
			echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

 $callback = function($msg) 
{
	echo " [x] Received ", $msg->body, "\n";

	$mensaje = json_decode($msg->body,true);
	$id = $mensaje["id"];
	$file = $mensaje["file"];
	$partes = $mensaje["parts"];
    $time = $mensaje["time_per_chunk"];

	$tiempo = shell_exec('ffmpeg -i ' . $file . ' 2>&1 |grep -oP "[0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{2}"');           
	$array = explode("/", $file);
    $status = count($array)-1;          
    $fileName = $array[$status];
    //var_dump($fileName);

    $arrayfile = explode(".", $fileName);
    $statusfile = count($arrayfile)-1;          
    $extencion = $arrayfile[$statusfile];
    //var_dump($extencion);
            
    $arrayTime = explode(" ", $time);         
    $minutos = $arrayTime[0];
    //var_dump($minutos);
    if(($minutos=="")||($minutos==0)||($minutos== null))
    {        
        list($horas,$minutos,$segundos,$microsegundos) = split("[:.]",$tiempo);
        $duracion = $horas.$minutos.$segundos.$microsegundos;  
        //$partes = 2;
        $microsegundos = '10';
        //$horas = 1 ;
       // $minutos = 30;
		$totalSegundos = $segundos;
       
		if($horas > 0){
			$horas = $horas*60;
           $totalSegundos += $horas*60;
		}       
		if($minutos > 0){
            $totalSegundos += $minutos*60;
		}
		//var_dump("segundos totales".$totalSegundos);
		$horas = "0";
        $minutos ="0";
        $segundos = "0";
		$totalSegundos = $totalSegundos / $partes;
        // var_dump($totalSegundos."segundos por parte ");
         if($totalSegundos > 59)
         {
             $minutos = $totalSegundos / 60;
             if($minutos > 59)
             {
                $horas = $minutos / 60;
                if(is_float($horas))
                {
	                $horas =  explode(".", $horas);
	                $horas = $horas[0];
	                $minutos = '0.'.$horas[1];
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
         else
         {
         	$segundos = $totalSegundos;
         	$segundos = floor($segundos);
         	//var_dump($segundos);
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
	 $duracion = $horas.$minutos.$segundos.$microsegundos;	

	//var_dump($duracion."de cada parte");	
    }
    else
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
                else
                {
                    $minutos="00";
                    $hora = $horas;
                }
	              	                
	                //var_dump($minutos);
                    //var_dump($hora.':'.$minutos);
                    $duracion = "0.".$hora.":".$minutos.":"."00".".00";
	        } 
	        else
	        {                       
                 $duracion = "00".":".$minutos.":"."00".".00";
            }
  // var_dump($duracion);
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
  	$numero= $numero +1;
    $consulta = shell_exec("ffmpeg -i ".$file." -acodec copy -t ".$duracion." -ss ".$fin." "."..".$ruta[2].$numero.".".$ruta[3]);        


				$user = "postgres";
				$password = "12345";
				$dbname = "audios";
				$port = "5432";
				$host = "localhost";
				$cadenaConexion = "host=$host port=$port dbname=$dbname user=$user password=$password";
				#Conectamos con PostgreSQL
				$conexion = pg_connect($cadenaConexion) or die ("Fallo en el establecimiento de la conexión");

				#Efectuamos la consulta SQL
				$URL = $ruta[2].$numero.".".$ruta[3];
				var_dump($URL);
				$query = "insert into url(file,id_archivo) values ('".$URL."','".$id."')";
				$result = pg_query ($conexion, $query) or die("Error en la consulta SQL");
				pg_close($conexion);
  

       // var_dump( $ruta[2].$numero.".".$ruta[3]);
        var_dump($tiempo);
		var_dump($duracion."tamano de cada corte");
		var_dump($fin."donde inicia a cortar cadacorte");
		var_dump($segundosDuracion."  duracion restante");

       list($horas,$minutos,$segundos,$microsegundos) = split("[:.]",$fin); 

        $totalSegundos = $segundos + $segundos2;
       var_dump($totalSegundos."segundos sumanos");
		if($horas > 0){
			$horas = $horas*60;
           $totalSegundos += $horas*60;
        var_dump($totalSegundos."segundos sumanos");
		}       
		if($minutos > 0){
            $totalSegundos += $minutos*60;
             var_dump($totalSegundos."segundos sumanos");
		}

		if($horas2 > 0){
		   $horas2 = $horas2*60;
           $totalSegundos += $horas2*60;
             var_dump($totalSegundos."segundos sumanos22");
		}
	     if($minutos2 > 0){
           $totalSegundos += $minutos2*60;
             var_dump($totalSegundos."segundos sumanos22");
		}
		$horas = "0";
        $minutos ="0";
        $segundos = "0";
          var_dump($totalSegundos."total de segundos sumanos");
         if($totalSegundos > 59)
         {
             $minutos = $totalSegundos / 60;
             if($minutos > 59)
             {
                $horas = $minutos / 60;
                if(is_float($horas))
                {
	                $horas =  explode(".", $horas);
	                $horas = $horas[0];
	                $minutos = '0.'.$horas[1];
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

		$channel->basic_consume('audio', '', false, true, false, false, $callback);
		while(count($channel->callbacks)) {
		$channel->wait();
		}
		$channel->close();
		$connection->close();
		
?> 
