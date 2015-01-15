<?php
class Link extends Eloquent{
	protected $table = 'links';
	protected $fillelable = array('url','hash');
	public $timestamps = false;
}