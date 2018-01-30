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
        $user = $this->getUser();

        // Affectation de l'user en fonction de son statut
        $roles[] = $user->getRoles();

        // Sauvegarde du log de consultation
        $notification = $this->get('monolog.logger.notification');
        $notification->notice($user.' a consulté le tableau de bord .\n');

        // Statistiques acticvités
        $activiteTotale = $em->getRepository('AppBundle:Programme')->countActiviteTotal();
        $activiteJeune = $em->getRepository('AppBundle:Programme')->countActiviteJeune();
        $activiteChef = $activiteTotale - $activiteJeune;        

        //Liste de 15 activités
        $activites = $em->getRepository('AppBundle:Programme')->findTypeProgrammeFiltre($flag = 'Valider', 0, 14);


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
        $user = $this->getUser();

        // Affectation de l'user en fonction de son statut
        $roles[] = $user->getRoles();

        if (($roles[0][0] === 'ROLE_DISTRICT') || ($roles[0][0] === 'ROLE_EREGIONALE')) {
          //Recherche du district concerné
          $gestionnaire = $em->getRepository('AppBundle:Gestionnaire')->findOneBy(array('user' => $user));
          $district = $gestionnaire->getDepartement()->getId();

          // Si region est différente de l'équipe nationale alors Accès non autorisé
          if (($gestionnaire === NULL)) {
            throw new AccessDeniedException();
          }

          $programmes = $em->getRepository('AppBundle:Programme')->findDepartementTypeProgramme($district, $fralg = 'A traiter');
          $miniprogrammes = $em->getRepository('AppBundle:Programme')->findDepartementTypeProgrammeFiltre($district, $flag = 'A traiter', 0, 10);

        }else {
          $programmes = $em->getRepository('AppBundle:Programme')->findTypeProgramme($flag = 'A traiter');
          $miniprogrammes = $em->getRepository('AppBundle:Programme')->findTypeProgrammeFiltre($flag = 'A traiter', 0, 10);

        }


        return $this->render('default/calendrier.html.twig', array(
              'programmes'  => $programmes,
              'miniprogrammes'  => $miniprogrammes,
        ));
    }

    /**
     * Calendrier non traiter
     *
     * @Route("/calendrier/", name="calendrier_valider")
     */
    public function calendrierValiderAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $programmes = $em->getRepository('AppBundle:Programme')->findTypeProgramme($fralg = 'Valider');
        $miniprogrammes = $em->getRepository('AppBundle:Programme')->findTypeProgrammeFiltre($flag = 'Valider', 0, 10);

        return $this->render('default/calendrier.html.twig', array(
              'programmes'  => $programmes,
              'miniprogrammes'  => $miniprogrammes,
        ));
    }



    /**
     * @Route("/log", name="log")
     */
     public function logAction()
     {
       return $this->render('default/log.html.twig');
     }

     /**
      * @Route("/programme/non-traiter", name="programme_non_traiter")
      */
    public function programmeNontraiterAction(Request $request)
    {
      $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        // Affectation de l'user en fonction de son statut
        $roles[] = $user->getRoles();

        if (($roles[0][0] === 'ROLE_DISTRICT') || ($roles[0][0] === 'ROLE_EREGIONALE')) {
          //Recherche du district concerné
          $gestionnaire = $em->getRepository('AppBundle:Gestionnaire')->findOneBy(array('user' => $user));
          $district = $gestionnaire->getDepartement()->getId();

          // Si region est différente de l'équipe nationale alors Accès non autorisé
          if (($gestionnaire === NULL)) {
            throw new AccessDeniedException();
          }

          $programmes = $em->getRepository('AppBundle:Programme')->findDepartementTypeProgramme($district, $fralg = 'A traiter');
          $miniprogrammes = $em->getRepository('AppBundle:Programme')->findDepartementTypeProgrammeFiltre($district, $flag = 'A traiter', 0, 10);

        }else {
          $programmes = $em->getRepository('AppBundle:Programme')->findTypeProgramme($flag = 'A traiter');
          $miniprogrammes = $em->getRepository('AppBundle:Programme')->findTypeProgrammeFiltre($flag = 'A traiter', 0, 10);

        }


        return $this->render('default/calendrier.html.twig', array(
              'programmes'  => $programmes,
              'miniprogrammes'  => $miniprogrammes,
        ));
    }

    /**
     * @Route("/programme/region", name="programme_region")
     */
    public function programeRegionAction(Request $request)
    {
      $em = $this->getDoctrine()->getManager();

      $programmes = $em->getRepository('AppBundle:Programme')->findProgrammeFinal($type="region", $flag="valide");

      return $this->render("default/programme_final.html.twig",[
        'programmes'  => $programmes,
      ]);
    }

    /**
     * @Route("/programme/district", name="programme_district")
     */
    public function programeDistrictAction(Request $request)
    {
      $em = $this->getDoctrine()->getManager();

      $programmes = $em->getRepository('AppBundle:Programme')->findProgrammeFinal($type="district", $flag="valide");

      return $this->render("default/programme_final.html.twig",[
        'programmes'  => $programmes,
      ]);
    }
}
