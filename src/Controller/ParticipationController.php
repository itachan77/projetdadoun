<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\JoueurType;
use App\Service\TirageService;
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


                return $this->redirect($this->generateUrl('app_voir', [
                    'email' => $emailparticipant
                ]));

            }

        }
        

        return $this->render('participation/index.html.twig', [
            'form' => $form->createView(),
        ]);

    }




    #[Route('/voir', name: 'app_voir')]
    /**
     * La route permet au participant de participer au tirage au sort après succès de "l'inscription"
     *
     * @return Response
     */
    public function show(Request $request): Response
    {

        return $this->render('participation/show.html.twig', [
            'email' => $request->query->get('email'),
        ]);
    }




    #[Route('/tirer', name: 'app_tirer')]
    /**
     * La fonction suivante appelle le service TirageService qui contient 3 méthodes. Une méthode permettant de recevoir 
     * le résultat aléatoire du gain de lot à l'aide de la méthode getImage (getLotGagnant), et une méthode permettant 
     * de persister le lot en base de donnée pour l'utilisateur en question (persisterLot($lot,$joueur)) et le rendu visuel se fait
     * sur la page "result.html.twig"
     *
     * @return Response
     */
    public function tirer(TirageService $tirageService, UserRepository $userRepo, Request $request): Response
    {

        $lotGagnant = $tirageService->getLotGagant();
        $joueur = $userRepo->findOneBy(['email' => $request->query->get('email')]);
        

        $lot = array_keys($lotGagnant);
        $affichageConfirmation = $tirageService->persisterLot($lot[0], $joueur);

        // $affichageConfirmation = $tirageService->persisterLot();

        return $this->render('participation/result.html.twig', [
            'resultatImgTirage' => $lotGagnant, 
            'affichageConfirmation' => $affichageConfirmation
        ]);
    }



}
