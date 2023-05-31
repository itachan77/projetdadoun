# projetdadoun
 Ce projet Symfony a été créé par Chantal.

## Description

Ce projet a été développé avec le framework Symfony version 5. Un joueur saisit son email dans un formulaire et à la validation de celui-ci il est invité à participer 
à un tirage au sort avec des probabilités spécifiques pour gagner chaque lot.

## Installation

1. Clonez ce dépôt sur votre machine locale :
   
   git clone https://github.com/itachan77.projetdadoun.git
   
2. Accéder au répertoire du projet 
    
   cd projetdadoun
   
3. Installer les dépendances en utilisant composer : 

    composer install
    
4. Créez la base de données et exécutez les migrations :

    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    
5. Lancez le serveur de développement de Symfony 
    
    symfony server:start
