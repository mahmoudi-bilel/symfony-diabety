<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Publication;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormError;



#[Route('/commentaire')]
class CommentaireController extends AbstractController
{
    
    #[Route('/read', name: 'read_commentaire')]
    public function read_publication(EntityManagerInterface $entityManager): Response
    {
        $commentaire = $entityManager->getRepository(Commentaire::class)->findAll();


        return $this->render('commentaire/index.html.twig', ['commentaire' => $commentaire]);
    }

    #[Route('/new', name: 'app_commentaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CommentaireRepository $commentaireRepository): Response
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $createdAt = new \DateTimeImmutable();
            $commentaire->setCreatedAt($createdAt);

            // Check for bad words in the comment
            $badWords = array('mot1', 'mot2', 'mot3');
            $commentContent = $commentaire->getTextcommentaire();
            $containsBadWord = false;
            foreach ($badWords as $word) {
                if (stripos($commentContent, $word) !== false) {
                    $form->addError(new FormError('Votre commentaire contient des mots inappropriés.'));
                    return $this->renderForm('commentaire/new.html.twig', [
                        'commentaire' => $commentaire,
                        'form' => $form,
                    ]);
                }
            }
            if ($containsBadWord) {
                $this->addFlash('error', 'Your comment contains inappropriate language.');
                return $this->redirectToRoute('app_commentaire_new');
            }

            $commentaireRepository->save($commentaire, true);
            $this->addFlash('notice', 'comment added Successufully!!');
            return $this->redirectToRoute('read_commentaire', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commentaire/new.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commentaire_show', methods: ['GET'])]
    public function show(Commentaire $commentaire): Response
    {
        return $this->render('commentaire/show.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commentaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commentaire $commentaire, CommentaireRepository $commentaireRepository): Response
    {
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $updatedAt = new \DateTimeImmutable();
            $commentaire->setUpdatedAt($updatedAt);
            $commentaireRepository->save($commentaire, true);
            return $this->redirectToRoute('read_commentaire', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('commentaire/edit.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: 'app_commentaire_delete', methods: ['POST'])]
    public function delete(Request $request, Commentaire $commentaire, CommentaireRepository $commentaireRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commentaire->getId(), $request->request->get('_token'))) {
            $commentaireRepository->remove($commentaire, true);
        }

        return $this->redirectToRoute('read_commentaire', [], Response::HTTP_SEE_OTHER);
    }







}
