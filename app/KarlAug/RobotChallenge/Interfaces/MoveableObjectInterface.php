<?php namespace KarlAug\RobotChallenge\Interfaces ;

interface MoveableObjectInterface
{

    public function executeWalkCommand($Walkcommands);

    public function validWalkCommand($Walkcommand);
    public function validFacingDirection($faceingDirection);
    public function setCanMove($bool = null);
    public function canMove();


    public function moveForward();
    public function changeDirectionLeft();
    public function changeDirectionRight();
    public function moveBackwards();
}
