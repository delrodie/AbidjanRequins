<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Programme;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Programme controller.
 *
 * @Route("programme")
 */
class ProgrammeController extends Controller
{
    /**
     * Lists all programme entities.
     *
     * @Route("/", name="programme_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        // Affectation de l'user en fonction de son statut
        $roles[] = $user->getRoles();

        // Sauvegarde du log de consultation
        $notification = $this->get('monolog.logger.notification');
        $notification->notice($user.' a consulté la liste des programmes provisoires .\n');

        //Affection de l'utilisation selon son departement
        if (($roles[0][0] === 'ROLE_DISTRICT') || ($roles[0][0] === 'ROLE_EREGIONALE')) {
          //Recherche du district concerné
          $gestionnaire = $em->getRepository('AppBundle:Gestionnaire')->findOneBy(array('user' => $user));
          $departement = $gestionnaire->getDepartement()->getId();

          $programmes = $em->getRepository('AppBundle:Programme')->findDepartementTypeProgramme($departement, $flag = 'traite');

          return $this->render('programme/index.html.twig', array(
              'programmes' => $programmes,
          ));

        }

        //$programmes = $em->getRepository('AppBundle:Programme')->findAll();
        $programmes = $em->getRepository('AppBundle:Programme')->findTypeProgramme($flag = 'traite');;

        return $this->render('programme/index.html.twig', array(
            'programmes' => $programmes,
        ));
    }

    /**
     * Creates a new programme entity.
     *
     * @Route("/new", name="programme_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $programme = new Programme();
        $user = $this->getUser();

        // Affectation de l'user en fonction de son statut
        $roles[] = $user->getRoles();

        // Sauvegarde du log de consultation
        $notification = $this->get('monolog.logger.notification');
        $notification->notice($user.' a consulté le formulaire enregistrement de programmes .\n');

        //Affection de l'utilisation selon son departement
        if (($roles[0][0] === 'ROLE_DISTRICT') || ($roles[0][0] === 'ROLE_EREGIONALE')) {
          //Recherche du district concerné
          $gestionnaire = $em->getRepository('AppBundle:Gestionnaire')->findOneBy(array('user' => $user));
          $departement = $gestionnaire->getDepartement()->getId();

          $programmes = $em->getRepository('AppBundle:Programme')->findDepartementProgramme($departement);

          $form = $this->createForm('AppBundle\Form\ProgrammeUserType', $programme, array('user' => $user));

          $form->handleRequest($request);

          if ($form->isSubmitted() && $form->isValid()) {
              $em = $this->getDoctrine()->getManager();
              $programme->setStatut(true);
              $programme->setFlag('A traiter');//dump($programme);die();

              //Verification de non cohincidence d'un programme regional
              // Si la date ne coincide pas alors porter les modification sinon rejeter
              $verif = $em->getRepository('AppBundle:Programme')
                            ->verifProgrammeFinal(
                                $type="region", 
                                $flag ="valid", 
                                $programme->getDatedeb(),
                                $programme->getDatefin()
                            );
              //dump($verif);die();
              if (!$verif){
                $em->flush();
              } else{
                
                $this->addFlash('notice', "Attention la région a une activité dans la même période du ".$programme->getDatedeb()." au ".$programme->getDatefin()." donc veuiller choisir une autre date!");
                $editForm = $this->createForm('AppBundle\Form\ProgrammeUserType', $programme, array('user' => $user));
                $editForm->handleRequest($request);

                return $this->render('programme/editUser.html.twig', array(
                    'programme' => $programme,
                    'edit_form' => $editForm->createView(),
                ));
              }
              
              $em->persist($programme);
              $em->flush();

              return $this->redirectToRoute('programme_index');
          }

          return $this->render('programme/newUser.html.twig', array(
              'programme' => $programme,
              'form' => $form->createView(),
          ));

        } else {
          $form = $this->createForm('AppBundle\Form\ProgrammeType', $programme);
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $programme->setStatut(true);
            $programme->setFlag('A traiter');//dump($programme);die();
            $em->persist($programme);
            $em->flush();

            return $this->redirectToRoute('programme_index');
        }

        return $this->render('programme/new.html.twig', array(
            'programme' => $programme,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a programme entity.
     *
     * @Route("/{slug}", name="programme_show")
     * @Method("GET")
     */
    public function showAction(Programme $programme)
    {
        $deleteForm = $this->createDeleteForm($programme);

        return $this->render('programme/show.html.twig', array(
            'programme' => $programme,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing programme entity.
     *
     * @Route("/{id}/edit", name="programme_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Programme $programme)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        // Affectation de l'user en fonction de son statut
        $roles[] = $user->getRoles();
        $deleteForm = $this->createDeleteForm($programme);

        if  (($roles[0][0] === 'ROLE_DISTRICT') || ($roles[0][0] === 'ROLE_EREGIONALE'))  {
          //Recherche du district concerné
          $gestionnaire = $em->getRepository('AppBundle:Gestionnaire')->findOneBy(array('user' => $user));
          $departement = $gestionnaire->getDepartement()->getId();

          $programmes = $em->getRepository('AppBundle:Programme')->findDepartementProgramme($departement);

          $editForm = $this->createForm('AppBundle\Form\ProgrammeUserType', $programme, array('user' => $user));
          $editForm->handleRequest($request);

          if ($editForm->isSubmitted() && $editForm->isValid()) {
              
              $em = $this->getDoctrine()->getManager();

              //Verification de non cohincidence d'un programme regional
              // Si la date ne coincide pas alors porter les modification sinon rejeter
              $verif = $em->getRepository('AppBundle:Programme')
                            ->verifProgrammeFinal(
                                $type="region", 
                                $flag ="valid", 
                                $programme->getDatedeb(),
                                $programme->getDatefin()
                            );
              //dump($verif);die();
              if (!$verif){
                $em->flush();
              } else{
                
                $this->addFlash('notice', "Attention la région a une activité dans la même période du ".$programme->getDatedeb()." au ".$programme->getDatefin()." donc veuiller choisir une autre date!");

                return $this->render('programme/editUser.html.twig', array(
                    'programme' => $programme,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
              }
              

              return $this->redirectToRoute('programme_index');
          }

          return $this->render('programme/editUser.html.twig', array(
              'programme' => $programme,
              'edit_form' => $editForm->createView(),
              'delete_form' => $deleteForm->createView(),
          ));

        }else {
          $editForm = $this->createForm('AppBundle\Form\ProgrammeType', $programme);
          $editForm->handleRequest($request);

          if ($editForm->isSubmitted() && $editForm->isValid()) {
              $this->getDoctrine()->getManager()->flush();

              return $this->redirectToRoute('programme_index');
          }
        }

        return $this->render('programme/edit.html.twig', array(
            'programme' => $programme,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a programme entity.
     *
     * @Route("/{id}", name="programme_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Programme $programme)
    {
        $form = $this->createDeleteForm($programme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($programme);
            $em->flush();
        }

        return $this->redirectToRoute('programme_index');
    }

    /**
     * Creates a form to delete a programme entity.
     *
     * @param Programme $programme The programme entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Programme $programme)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('programme_delete', array('id' => $programme->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
