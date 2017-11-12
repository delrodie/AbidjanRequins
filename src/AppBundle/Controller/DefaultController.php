<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * Calendrier non traiter
     *
     * @Route("/calendrier/non-traiter", name="calendrier_non_traiter")
     */
    public function calendrierNontraiterAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $programmes = $em->getRepository('AppBundle:Programme')->findProgrammeNonTraiter();
        $miniprogrammes = $em->getRepository('AppBundle:Programme')->findProgrammeNonTraiterFiltre(0, 10);

        return $this->render('default/calendrier.html.twig', array(
              'programmes'  => $programmes,
              'miniprogrammes'  => $miniprogrammes,
        ));
    }
}
