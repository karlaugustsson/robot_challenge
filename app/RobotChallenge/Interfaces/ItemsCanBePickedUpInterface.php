<?php namespace App\RobotChallenge\Interfaces ;

interface ItemsCanBePickedUpInterface
{

    public function isPassableItemFoundOnPosition($position);
    public function passOverItem($position);
}
