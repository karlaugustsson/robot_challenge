<?php namespace App\MyClasses\Classes;

use App\MyClasses\Classes\MoveableObject as MoveableObject;

use App\MyClasses\Classes\Grid as Grid;

class Robot extends MoveableObject{

	public function __construct($facingDirection , Grid $gridObj){

		parent::__construct($facingDirection,"Robot" , $gridObj);

	}

}