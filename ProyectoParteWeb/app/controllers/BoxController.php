<?php
require ('/home/betzy/Desktop/proyecto-1/ProyectoParteWeb/push/vendor/autoload.php');
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

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
			'Box.index',
			array()
		);
	}

	public function procesando()
	{

        //$file = Input::get('audio');
		$partes = Input::get('partes');
		$minutos = Input::get('minutos');
        $file = Input::file('audio');

       if($file == null){
           return Redirect::to('/');
       }

        $extension =$file->getClientOriginalExtension();

       // $path = Input::file('audio')->getRealPath();
       // var_dump($path);

       // $rest = substr("$filename", -4);
        $rest = $extension;
       //list($file ,$ext) = split("[.]",$file);
		if(($rest != "mp3")&&($rest != "midi")&&($rest != "ogg")&&($rest != "wav")&&($rest != "mpeg")&&($rest != "amr")&&($rest != "ac3")&&($rest != "aac")&&($rest != "wma"))
		{
			//echo "<p class='error_message'>Formato de Archivo invalido, vuelva a intentar</p>";
            return Redirect::to('/');
		}

		if(($partes == "")&&($minutos == "")){
            return Redirect::to('/');
		}
		

       $destinationPath = 'audios/'.str_random(8);
         
       // var_dump($destinationPath);

        $filename = $file->getClientOriginalName();


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
       $uploadSuccess = $file->move($destinationPath, $filename);
         if($uploadSuccess)
         {
         	    $archivo = new Archivo();
		        $archivo->file = '../ProyectoParteWeb/'.$destinationPath.'/'.$filename;
		        $archivo->parts = $partes;
		        $archivo->time = $minutos;
		        $archivo->save();
                                       
			   $result = DB::select("SELECT * FROM archivo  ORDER BY id DESC LIMIT 1");
			  //var_dump($results[0]->id);
		        $id = $result[0]->id;
		        $file = $result[0]->file;
		        $partes=$result[0]->parts;
		        $minutos=$result[0]->time;
		        $minutos=$minutos.' minutos';
		        $json = array('id' => "$id",'file' => "$file",'parts' => "$partes",'time_per_chunk' => "$minutos");
		        $mensaje= json_encode($json);
		      //var_dump($mensaje);    
		     // $mensaje1=json_decode($mensaje);
		     // var_dump($mensaje1);

                $connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
                $channel = $connection->channel();
                $channel->queue_declare("audio", false, false, false, false);
                $msg = new AMQPMessage("$mensaje");
                $channel->basic_publish($msg, '', "audio");
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