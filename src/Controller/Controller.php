<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Controller extends AbstractController
{
    public function create(Request $request, $object, $formtype, $repository, $type): Response
    {
        $newObject = new $object();
        $form = $this->createForm($formtype, $newObject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($newObject, true);

            return $this->redirectToRoute('app_'. $type .'_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('content/'. $type .'/create.html.twig', [
            $type => $newObject,
            'form' => $form,
        ]);
    }
}
