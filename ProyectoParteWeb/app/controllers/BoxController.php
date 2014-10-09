<?php

class BoxController extends \BaseController {

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
			'Box.procesando',
			array()
		);
	}

}