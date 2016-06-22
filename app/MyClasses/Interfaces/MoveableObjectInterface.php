<?php namespace App\MyClasses\Interfaces ; 

interface MoveableObjectInterface{
	public function executeWalkCommand($Walkcommands);
	public function changeFacingDirection($facingDirection);
	public function validWalkCommands($Walkcommands);


}