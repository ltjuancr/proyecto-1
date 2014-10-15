<?php

class Url extends Eloquent
{
	protected $table = 'url';
	protected $fillable = array('file', 'id_archivo');
	protected $guarded  = array('id');
	public    $timestamps = false;
}