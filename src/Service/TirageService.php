<?php


namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;



class TirageService {


    private $em; 


    public function __construct (EntityManagerInterface $em) {
        $this->em = $em;
    }



    /**
     * Après calcul aléatoire en fonction de la probabilité de gagner un lot, 
     * la fonction fait appel à la méthode getImage pour retourner l'image correspondant au lot gagné
     *
     * @return array
     */
    public function getLotGagant () {

        // Probabilités d'apparition des lots
        $probabilites = [
            'Tesla' => 1,
            'Weekend à la montagne' => 9,
            'PS5' => 10,
            'PC Gamer' => 30,
            'Jeu de cartes' => 50,
        ];

        // Calcul de la somme des probabilités
        $totalProbabilite = array_sum($probabilites);

  
        
        // Génération d'un nombre aléatoire entre 1 et la somme des probabilités
        $randomNombre = random_int(1, $totalProbabilite);

        // Variables pour stocker le lot gagnant et sa probabilité
        $logGagne = '';
        $gainProbabilite = 0;

        // Parcours des probabilités pour trouver le lot gagnant
        foreach ($probabilites as $lot => $probabilite) {
            $gainProbabilite += $probabilite;
            if ($randomNombre <= $gainProbabilite) {
                $logGagne = $lot;
                break;
            }
        }

        return $this->getImage($logGagne);

    }

    /**
     * A partir du lot gagné ($logGagne), retourne l'image correspondante
     *
     * @param [type] $winningLot
     * @return void
     */
    public function getImage ($winningLot) {

        switch ($winningLot) {
            case 'Tesla' : 
                return ['Une Tesla !' => 'tesla.jpg'];
            break;
            case 'Weekend à la montagne' : 
                return ['Un séjour à la montagne !' => 'montagne.jpg'];
            break;
            case 'PS5' : 
                return ['Une PS5 !' => 'PS5.jpg'];
            break;
            case 'PC Gamer' : 
                return ['Un PC Gamer !' => 'pcgamer.jpg'];
            break;
            case 'Jeu de cartes' : 
                return ['Un jeu de cartes !' => 'cartes.jpg'];
            break;

            default:
                return 'Problème dans le tirage au sort';
        }

    }

    public function persisterLot ($logGagne, $joueur) {

        if ($logGagne && $joueur) {

                $joueur->setAParticipe(1);
                $this->em->persist($joueur);
                $this->em->flush();

            
            return 'Votre gain a bien été enregistré !';

        }else {

            return 'Un problème est survenu lors de la sauvegarde de votre gain !';
        }

        

    }


}

