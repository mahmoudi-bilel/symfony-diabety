<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Publication;
use App\Form\PublicationType;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


#[Route('/publication')]

class PublicationController extends AbstractController
{
    #[Route('/app', name: 'app_publication')]
    public function index(): Response
    {
        return $this->render('publication/index.html.twig', [
            'controller_name' => 'PublicationController',
        ]);
    }
    #[Route('/add', name: 'add_publication')]
    public function add_offer(Request $request, EntityManagerInterface $entityManager): Response
    {
        $publication = new Publication();
        $form = $this->createForm(PublicationType::class,$publication);

        $form -> handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $publication->setCreatedAT(new \DateTime());
            $entityManager->persist($publication);//Add
            $entityManager->flush();

            return $this->redirectToRoute('read_publication');

        }
        return $this->render('publication/add.html.twig',['f'=>$form->createView()] );

    }
    #[Route('/read', name: 'read_publication')]
    public function read_publication(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $publications = $entityManager->getRepository(Publication::class)->findAll();

        $publications = $paginator->paginate(
            $publications,
            $request->query->getInt('page', 1),
            2 // Le nombre d'éléments par page
        );

        return $this->render('publication/read.html.twig', ['publications' => $publications]);
    }
    #[Route('/read_admin', name: 'read_admin_publication')]
    public function read_admin_publication(EntityManagerInterface $entityManager): Response
    {
        $publication = $entityManager->getRepository(Publication::class)->findAll();


        return $this->render('publication/readadmin.html.twig', ['publication' => $publication]);
    }
    #[Route('/edit/{id}', name: 'edit_publication')]
    public function edit_publication(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        $publication = $entityManager->getRepository(Publication::class)->find($id);

        $form = $this->createForm(PublicationType::class, $publication);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $publication->setUpdatedAT(new \DateTime());

            $entityManager->flush();
            return $this->redirectToRoute('read_publication');

        }

        return $this->render('publication/edit.html.twig', [
            'f' => $form->createView(),
        ]);
    }
    #[Route('/delete/{id}', name: 'delete_publication')]
    public function delete_publication(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $publication = $entityManager->getRepository(Publication::class)->find($id);

        if (!$publication) {
            throw $this->createNotFoundException('No publication found for id '.$id);
        }

        $entityManager->remove($publication);
        $entityManager->flush();
        $this->addFlash('success', 'The publication has been deleted successfully.');
        return $this->redirectToRoute('read_publication');
    }
    #[Route('/delete_admin/{id}', name: 'delete_admin_publication')]
    public function delete_admin_publication(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $publication = $entityManager->getRepository(Publication::class)->find($id);

        if (!$publication) {
            throw $this->createNotFoundException('No publication found for id '.$id);
        }

        $entityManager->remove($publication);
        $entityManager->flush();
        $this->addFlash('success', 'The publication has been deleted successfully.');
        return $this->redirectToRoute('read_admin_publication');
    }


    #[Route('/all', name: 'view_all_publications')]
    public function viewAllPublications(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        // Retrieve all publications and shared publications
        $query = $entityManager->getRepository(Publication::class)->createQueryBuilder('p')
            ->orderBy('p.createdAT', 'DESC')
            ->getQuery();

        $publications = $paginator->paginate($query, $request->query->getInt('page', 1), 2); // 10 publications per page

        // Loop through publications and get their associated visible commentaires
        foreach ($publications as $publication) {
            $commentaires = $entityManager->getRepository(Commentaire::class)->findBy(['pub' => $publication, 'visible' => 'visible']);
            $publication->commentaires = $commentaires;
        }

        return $this->render('publication/view_all.html.twig', [
            'publications' => $publications,
        ]);
    }




    #[Route('/like/{id}', name: 'like_publication')]
    public function likePublication(Publication $publication, EntityManagerInterface $entityManager): Response
    {
        $publication->setLikes($publication->getLikes() + 1);
        $entityManager->flush();

        return $this->redirectToRoute('read_publication');
    }
    #[Route('/dislike/{id}', name: 'dislike_publication')]
    public function dislikePublication(Publication $publication, EntityManagerInterface $entityManager): Response
    {
        $publication->setDislikes($publication->getDislikes() + 1);
        $entityManager->flush();

        return $this->redirectToRoute('read_publication');
    }
    #[Route('/ajouterjson',name:'mobileajouterposte', methods: ['GET','POST'])]
    public function addpostejson(Request $request,NormalizerInterface $Normalizer, EntityManagerInterface $doctrine)
    {
        $poste=new Publication();
        $poste->setTitre($request->get('titre'));
        $poste->setDescription($request->get('description'));



        $doctrine->persist($poste);
        $doctrine->flush();
        $jsonContent=$Normalizer->normalize($poste, 'json', ['groups'=>'publication']);
        return new Response(json_encode($jsonContent));


    }
    #[Route('/afficherjson',name:'mobileaaffiche')]
    public function allpublication( NormalizerInterface $normalizer, PublicationRepository $repo)
    {
        $publication=$repo->findAll();

        $jsonContent=$normalizer->normalize($publication, 'json', ['groups'=>'publication']);

        $json=json_encode($jsonContent);

        return new Response($json);
    }
    #[Route('/supprimerjson/{id}',name:"mobilesupprimer")]
    public function deletejson(Request $request,$id,NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $publication = $em->getRepository(Publication::class)->find($id);
        $em->remove($publication);
        $em->flush();
        $jsonContent=$Normalizer->normalize($publication, 'json', ['groups'=>'publication']);
        return new Response("Publication deleted successfully ".json_encode($jsonContent));
    }

    #[Route('/front/share/{id}', name: 'ssd')]
    public function share($id, Request $request, PublicationRepository $repo): Response
    {
        $publication = $repo->find($id);

        $hashtag = "#" . str_replace(' ', '_', $publication->getDescription());
        $homepageUrl = "https://gadget-deve.fr/collections/sante-et-beaute/diabete"; // Replace with the URL of your website's homepage or the URL of the page where the tournament is displayed
        $shareUrl = "https://www.facebook.com/dialog/share?app_id=160291406462337&display=popup&href=" . urlencode($homepageUrl);
        $shareUrl .= "&hashtag=" . urlencode($hashtag);

        return $this->redirect($shareUrl);
    }

    #[Route('/front/shareapi/{id}', name: 'sssd')]
    public function shareapi($id, Request $request, PublicationRepository $repo, NormalizerInterface $normalizer): Response
    {
        $publication = $repo->find($id);

        $hashtag = "#" . str_replace(' ', '_', $publication->getDescription());
        $homepageUrl = "https://gadget-deve.fr/collections/sante-et-beaute/diabete";
        $shareUrl = "https://www.facebook.com/dialog/share?app_id=160291406462337&display=popup&href=" . urlencode(str_replace('&', '&amp;', $homepageUrl));
        $shareUrl .= "&hashtag=" . urlencode($hashtag);

        $responseData = ['shareUrl' => $shareUrl];
        $jsonContent = $normalizer->normalize($responseData, 'json');

        return new JsonResponse($jsonContent['shareUrl']);
    }


    #[Route('/updatejson/{id}',name:'mobileupdate')]
    public function updatejson(Request $request,$id, NormalizerInterface $normalizer, PublicationRepository $repo, EntityManagerInterface $doctrine)
    {

        $em = $this->getDoctrine()->getManager();
        $data= $em->getRepository(Publication::class)->find($id);
        $data->setTitre($request->get('titre'));
        $data->setDescription($request->get('description'));
        $em->flush();
        $jsonContent=$normalizer->normalize($data, 'json', ['groups'=>'publication']);

        $json=json_encode($jsonContent);

        return new Response("publication updated".$json);
    }


}
