<?php namespace App\RobotChallenge\Interfaces ;

interface ItemCanMoveInterface
{

    public function executeWalkCommand($Walkcommands);
    public function validWalkCommand($Walkcommand);
    public function validDirection($direction);
    public function setCanMove($bool = null);
    public function canMove();
    public function moveForward();
    public function changeDirectionLeft();
    public function changeDirectionRight();
    public function moveBackwards();
}
