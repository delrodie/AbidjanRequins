<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Gestionnaire;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Gestionnaire controller.
 *
 * @Route("admin/gestionnaire")
 */
class GestionnaireController extends Controller
{
    /**
     * Lists all gestionnaire entities.
     *
     * @Route("/", name="admin_gestionnaire_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $gestionnaires = $em->getRepository('AppBundle:Gestionnaire')->findAll();

        return $this->render('gestionnaire/index.html.twig', array(
            'gestionnaires' => $gestionnaires,
        ));
    }

    /**
     * Creates a new gestionnaire entity.
     *
     * @Route("/new", name="admin_gestionnaire_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $gestionnaire = new Gestionnaire();
        $form = $this->createForm('AppBundle\Form\GestionnaireType', $gestionnaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($gestionnaire);
            $em->flush();

            return $this->redirectToRoute('admin_gestionnaire_index');
        }

        return $this->render('gestionnaire/new.html.twig', array(
            'gestionnaire' => $gestionnaire,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a gestionnaire entity.
     *
     * @Route("/{id}", name="admin_gestionnaire_show")
     * @Method("GET")
     */
    public function showAction(Gestionnaire $gestionnaire)
    {
        $deleteForm = $this->createDeleteForm($gestionnaire);

        return $this->render('gestionnaire/show.html.twig', array(
            'gestionnaire' => $gestionnaire,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing gestionnaire entity.
     *
     * @Route("/{id}/edit", name="admin_gestionnaire_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Gestionnaire $gestionnaire)
    {
        $deleteForm = $this->createDeleteForm($gestionnaire);
        $editForm = $this->createForm('AppBundle\Form\GestionnaireType', $gestionnaire);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_gestionnaire_index');
        }

        return $this->render('gestionnaire/edit.html.twig', array(
            'gestionnaire' => $gestionnaire,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a gestionnaire entity.
     *
     * @Route("/{id}", name="admin_gestionnaire_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Gestionnaire $gestionnaire)
    {
        $form = $this->createDeleteForm($gestionnaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($gestionnaire);
            $em->flush();
        }

        return $this->redirectToRoute('admin_gestionnaire_index');
    }

    /**
     * Creates a form to delete a gestionnaire entity.
     *
     * @param Gestionnaire $gestionnaire The gestionnaire entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Gestionnaire $gestionnaire)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_gestionnaire_delete', array('id' => $gestionnaire->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
