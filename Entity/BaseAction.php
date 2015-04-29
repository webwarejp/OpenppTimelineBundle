<?php

namespace Openpp\TimelineBundle\Entity;

use Spy\TimelineBundle\Entity\Action;

class BaseAction extends Action
{
    protected $actionComponents;
    protected $timelines;
}