<?php namespace App\MyClasses\Interfaces ; 

interface MoveableObjectInterface{

	public function executeWalkCommand($Walkcommands);
	
	public function validWalkCommand($Walkcommand);
	public function validFacingDirection($faceingDirection);
	
	public function ChangeFacingDirection($turningDirection);
	public function determineNextGridPosition($direction);
	public function setCanMove($bool = null);


}