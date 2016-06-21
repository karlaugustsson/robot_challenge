<?php namespace App\MyClasses\Classes;
use App\MyClasses\Classes\GridObject as GridObject;
use App\MyClasses\Classes\Grid as Grid;

class Obstacle extends GridObject{
	public function __construct(Grid $GridObject){
		parent::__construct("Obstacle" , $GridObject);
	}

}