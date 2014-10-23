<?php
require ('/home/betzy/Desktop/ProyectoAudioLibro/proyecto-1/ProyectoParteWeb/push/vendor/autoload.php'); //Librerias Rabbit
use PhpAmqpLib\Connection\AMQPConnection;//Libreria de conexión
use PhpAmqpLib\Message\AMQPMessage; //Libreria para obtener los mensajes

class BoxController extends \BaseController {
protected $layout = 'layouts.default';

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

		$this->layout->titulo = '';
		$this->layout->nest(
			'content',
			'Box.index', //Página principal
			array()
		);
	}

	//Función para que pase a procesar
	public function procesando()
	{

        //$file = Input::get('audio');
        //Datos que obtenemos de la vista
		$partes = Input::get('partes');
		$minutos = Input::get('minutos');
        $file = Input::file('audio');

        //Validaciones
       if($file == null){
           return Redirect::to('/');
       }

       //Se saca la extención con esa propiedad, y file que es una propiedad de Symfony
        $extension =$file->getClientOriginalExtension();

       // $path = Input::file('audio')->getRealPath();
       // var_dump($path);

       // $rest = substr("$filename", -4);
        $rest = $extension;
       //list($file ,$ext) = split("[.]",$file);

        //Validación para verificar la extención
		if(($rest != "mp3")&&($rest != "midi")&&($rest != "ogg")&&($rest != "wav")&&($rest != "mpeg")&&($rest != "amr")&&($rest != "ac3")&&($rest != "aac")&&($rest != "wma"))
		{
			//echo "<p class='error_message'>Formato de Archivo invalido, vuelva a intentar</p>";
            return Redirect::to('/');
		}

		if(($partes == "")&&($minutos == "")){
            return Redirect::to('/');
		}
		
		//Dirección donde se va a crear la carpeta del audio
       $destinationPath = 'audios/'.str_random(8);
         
       // var_dump($destinationPath);

       //Esta propiedad trae el nombre del archivo que subieron
        $filename = $file->getClientOriginalName();

       //Al nombre del archivo se le quita cualquiera de esos caracteres si los trae, se cambia por nada 
        $filename = str_replace(
        array("\\", "¨", "º", "-", "~",
             "#", "@", "|", "!", "\"",
             "·", "$", "%", "&", "/",
             "(", ")", "?", "'", "¡",
             "¿", "[", "^", "`", "]",
             "+", "}", "{", "¨", "´",
             ">", "< ", ";", ",", ":",
             ".", " ","_",$extension),'',$filename);

        $filename = $filename.".".$extension;
        // $filename = str_replace(' ', '', $filename);
        // var_dump($filename);

        //Propiedad también del archivo Filesystem de Symfony, además mueve el archivo a la carpeta
       $uploadSuccess = $file->move($destinationPath, $filename);
         if($uploadSuccess) //Verifica si fue exitoso, si se pudo mover el archivo
         {
         	    $archivo = new Archivo();
		        $archivo->file = '../ProyectoParteWeb/'.$destinationPath.'/'.$filename;
		        $archivo->parts = $partes;
		        $archivo->time = $minutos;
		        $archivo->save(); //Lo guardamos en la BD
                
                //Consulta a la Base de Datos para traer el último id                       
			   $result = DB::select("SELECT * FROM archivo  ORDER BY id DESC LIMIT 1");
			  //var_dump($results[0]->id);
		        $id = $result[0]->id;
		        $file = $result[0]->file;
		        $partes=$result[0]->parts;
		        $minutos=$result[0]->time;
		        $minutos=$minutos.' minutos';
		        $json = array('id' => "$id",'file' => "$file",'parts' => "$partes",'time_per_chunk' => "$minutos");
		        $mensaje= json_encode($json); //Esta propiedad vuelve el array list en json y se guarda en la variable mensaje
		      //var_dump($mensaje);    
		     // $mensaje1=json_decode($mensaje);
		     // var_dump($mensaje1);

		        //Conexión a Rabbit, gracias a las Librerias
                $connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
                $channel = $connection->channel();
                $channel->queue_declare("audio", false, false, false, false); //Declara el canal
                $msg = new AMQPMessage("$mensaje");//Declara el mensaje
                $channel->basic_publish($msg, '', "audio");//Pública el mensaje y el canal
                $channel->close();
                $connection->close();
              
         }
         
		$this->layout->titulo = '';
		$this->layout->nest(
			'content',
			'Box.procesando',
			array(

				)
		);
	}
}