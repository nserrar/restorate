**Etape installation :** 

Clonez le projet : 

    git clone git@github.com:nserrar/restorate.git

Installer les dependances :

    composer install
 
Creation de la BDD : 
    
    php bin/console doctrine:database:create
    
Lancez les migrations:

    php bin/console doctrine:migrations:migrate
    
Lancez les fixtures:

    php bin/console doctrine:fixtures:load
    

Lancez le server : (verifiez le retour de symfony serv:start l'url y est)
    
    symfony serv:start
   
 
 Les différentes routes du projet : 
 
    php bin/console debug:router

