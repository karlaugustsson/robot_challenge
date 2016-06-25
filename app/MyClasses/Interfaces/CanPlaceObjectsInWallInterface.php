<?php namespace App\MyClasses\Interfaces ; 

interface CanPlaceObjectsInWallInterface{


	public function canPlaceObjectOnPosition($position ,GridObjectInterface $object = null);

	public function gridPositionExistsIncludeWalls($x,$y);

	public function placeObjectOnGrid(GridObjectInterface $object , $position );

}