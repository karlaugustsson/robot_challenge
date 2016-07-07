<?php namespace KarlAug\RobotChallenge\Interfaces ;

interface GridWarpPointInterface
{
    public function positionHasWarpPoint($position);
    public function getWarpPointPosition($position);
}
