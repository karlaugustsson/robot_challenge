<?php namespace App\RobotChallenge\Interfaces ;

interface CanGrabObjectsInterface
{

    public function grabObject(GrabbableObjectInterface $object);
}
