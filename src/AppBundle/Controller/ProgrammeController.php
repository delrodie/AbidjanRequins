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

        //$programmes = $em->getRepository('AppBundle:Programme')->findAll();
        $programmes = $em->getRepository('AppBundle:Programme')->findProgrammeAdmin();

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
        $programme = new Programme();
        $form = $this->createForm('AppBundle\Form\ProgrammeType', $programme);
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
     * @Route("/{id}", name="programme_show")
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
        $deleteForm = $this->createDeleteForm($programme);
        $editForm = $this->createForm('AppBundle\Form\ProgrammeType', $programme);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('programme_index');
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
