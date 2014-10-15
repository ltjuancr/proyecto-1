<?php

class Archivo extends Eloquent
{
	protected $table = 'archivo';
	protected $fillable = array('file', 'parts','time');
	protected $guarded  = array('id');
	public    $timestamps = false;
}