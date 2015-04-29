<?php

namespace Openpp\TimelineBundle\Entity;

use Spy\TimelineBundle\Entity\Timeline;

class BaseTimeline extends Timeline
{
    protected $action;
    protected $subject;
}