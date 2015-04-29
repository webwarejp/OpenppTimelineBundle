<?php

namespace Openpp\TimelineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    public function timelineAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $targetUser = $this->get('fos_user.user_manager')
            ->findUserBy(array('id' => $request->get('subject', $user->getId())));

        if (!is_object($targetUser)) {
            throw $this->createNotFoundException('Target user is not found.');
        }

        $page = $request->get('page', 1);

        $actionManager   = $this->get('spy_timeline.action_manager');
        $timelineManager = $this->get('spy_timeline.timeline_manager');
        $subject         = $actionManager->findOrCreateComponent($targetUser);
        $timeline        = $timelineManager->getTimeline($subject, array('page' => $page, 'max_per_page' => 20, 'paginate' => true));

        if ($request->isXmlHttpRequest()) {
            $data = array();
            $data['lastPage'] = $timeline->getLastPage();
            $data['nbResults'] = $timeline->getNbResults();
            $data['results'] = array();
            foreach ($timeline as $action) {
                $data['results'][] = $this->renderView('OpenppTimelineBundle:Default:timeline_entry.html.twig', array(
                    'action' => $action));
            }
            return new Response(json_encode($data), 200, array('Content-Type'=>'application/json'));
        }
        else {
            return $this->render('OpenppTimelineBundle:Default:timeline.html.twig', array(
                'user'     => $targetUser,
                'timeline' => $timeline,
            ));
        }
    }
}
