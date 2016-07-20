<?php namespace App\RobotChallenge\Interfaces ;

interface CanGrabItemsInterface
{

    public function grabItem(CanBeGrabbedInterface $item);
}
