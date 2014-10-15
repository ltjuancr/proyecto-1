<?php

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
            return Redirect::to('/');
		}

		if(($partes == "")&&($minutos == "")){
            return Redirect::to('/');
		}
		

       $destinationPath = 'canciones/'.str_random(8);
         
       // var_dump($destinationPath);

        $filename = $file->getClientOriginalName();
         
      //   var_dump($filename);
       $uploadSuccess = $file->move($destinationPath, $filename);
         if($uploadSuccess)
         {
         	      $archivo = new Archivo();
		          $archivo->file = '../ProyectoParteWeb/'.$destinationPath.'/'.$filename;
		          $archivo->parts = $partes;
		          $archivo->time = $minutos;
		          $archivo->save();
         }

		$this->layout->titulo = '';
		$this->layout->nest(
			'content',
			'Box.procesando',
			array()
		);
	}
}