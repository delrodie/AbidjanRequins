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
        $em = $this->getDoctrine()->getManager();
        //Liste de 15 activités
        $activites = $em->getRepository('AppBundle:Programme')->findActiviteLatest($offset = 0, $limit = 10);

        // Statistiques acticvités
        $activiteTotale = $em->getRepository('AppBundle:Programme')->countActiviteTotal();
        $activiteJeune = $em->getRepository('AppBundle:Programme')->countActiviteJeune();
        $activiteChef = $activiteTotale - $activiteJeune;

        return $this->render('default/dashbord.html.twig', [
              'activites' => $activites,
              'activiteTotale' => $activiteTotale,
              'activiteJeune' => $activiteJeune,
              'activiteChef' => $activiteChef,
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
