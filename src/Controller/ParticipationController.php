<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\JoueurType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class ParticipationController extends AbstractController
{

    private $em;
    private $addFlash;

    public function __construct(EntityManagerInterface $em) {

        $this->em = $em;

    }


    #[Route('/participation', name: 'app_participation')]
    /**
     * Affichage de la page d'accueil affichant le formulaire de participation
     * Lorsque le formulaire est soumis, s'il est valide et que le joueur n'a pas encore participé, il est dirigé
     * vers la page de tirage au sort. 
     * Sinon, il recoit un flashbag l'informant qu'il a déjà participé et il reste sur sa page
     *
     * @param Request $request
     * @param UserRepository $userRepo
     * @param SessionInterface $session
     * @return Response
     */
    public function index(Request $request, UserRepository $userRepo, SessionInterface $session): Response
    {

        $session = $request->getSession();

        $joueur = new User;

        $form = $this->createForm(JoueurType::class, $joueur);

        $formData = $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            // Vérifier si l'email est déjà enregistré
            $emailparticipant = $formData['email']->getData();
            $searchJoueur = $userRepo->findBy(['email' => $emailparticipant]);
            
            if(count($searchJoueur) > 0){

                $session->getFlashBag()->add('message', 'Vous avez déjà participé à ce jeux !');
                $session->set('statut', 'warning');

                return $this->redirect($this->generateUrl('app_participation', [
                    'session' => $session,
                ]));


            } else {

                $this->em->persist($joueur);
                $this->em->flush();

                return $this->redirectToRoute('app_show', [
                    'email' => $formData->get('email')
                ]);

            }

        }
        

        return $this->render('participation/index.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    #[Route('/show', name: 'app_show')]
    /**
     * La route permet au participant de participer au tirage au sort après succès de "l'inscription"
     *
     * @return Response
     */
    public function show(): Response
    {

        return $this->render('participation/show.html.twig', [

        ]);



    }
}
