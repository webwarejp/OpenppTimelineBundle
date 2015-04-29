<?php

namespace Openpp\TimelineBundle\Spread;

use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Spread\SpreadInterface;
use Spy\Timeline\Spread\Entry\EntryCollection;
use Spy\Timeline\Spread\Entry\Entry;
use Spy\Timeline\Spread\Entry\EntryUnaware;

class Spread implements SpreadInterface
{
    CONST USER_CLASS = 'Application\Sonata\UserBundle\Entity\User';

    public function supports(ActionInterface $action)
    {
        $supported = !in_array($action->getVerb(), $this->getUnsuportedVerbs());
        return $supported;
    }

    public function process(ActionInterface $action, EntryCollection $coll)
    {
        // can define an Entry with a ComponentInterface as argument
        $coll->add(new Entry($action->getComponent('subject')));

        $coll->add(new EntryUnaware(self::USER_CLASS, 5));
    }

    protected function getUnsuportedVerbs()
    {
        return array();
    }
}