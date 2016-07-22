<?php namespace App\RobotChallenge;

class Position
{
    private $yPosition = null;
    private $xPosition = null;
    public function __construct($x, $y)
    {
        $this->setPosition($x, $y);
    }
    public function getPosition()
    {
        if ($this->yPosition === null || $this->xPosition === null) {
            return false;
        }
        return array($this->xPosition,$this->yPosition);
    }
    public function setPosition($x, $y)
    {
        $this->yPosition = $y;
        $this->xPosition = $x;
    }
    public function getYPosition()
    {
        return $this->yPosition;
    }
    public function getXPosition()
    {
        return $this->xPosition;
    }
}
