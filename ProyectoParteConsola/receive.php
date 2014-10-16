 
<?php
	require_once __DIR__ . '/vendor/autoload.php';
	use PhpAmqpLib\Connection\AMQPConnection;
		$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
		$channel = $connection->channel();
		$channel->queue_declare('audio', false, false, false, false);		
			echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

			$callback = function($msg) {
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

            




			};

		$channel->basic_consume('audio', '', false, true, false, false, $callback);
		while(count($channel->callbacks)) {
		$channel->wait();
		}
		$channel->close();
		$connection->close();
?> 
