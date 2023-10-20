# Mediatek Formation

Mediatek Formation est un catalogue de formations sur la programmation 

### [Le site](https://www.mediatekformation.site/) 
### [Présentation du projet](https://r4ndomfriday.github.io/PortFolio/pages/mediatekFormation.html) 
### [Le projet de départ](https://github.com/CNED-SLAM/mediatekformation) 
  
## Les pages du back-office 

### Page 1 : les formations
![img1](https://github.com/r4ndomfriday/PortFolio/blob/87964cffd8baa685c5d3902bd5b8143a0dcbcaff/ressources/readme/formationsAdmin.png?raw=true)
Page d'accueil du back-office, avec la possibilité de filtrer comme dans la partie front. Trois boutons ont été ajoutés. Les boutons "Modifier" et "Ajouter" permettent d'accéder au formulaire.
### Page 2 : le formulaire des formations
![img2](https://github.com/r4ndomfriday/PortFolio/blob/87964cffd8baa685c5d3902bd5b8143a0dcbcaff/ressources/readme/formationAdmin.png?raw=true)
Dans ce formulaire, seul le titre est obligatoire. Une formation peut être rattachée à plusieurs catégories, à une seule playlist.
### Page 3 : les playlists
![img3](https://github.com/r4ndomfriday/PortFolio/blob/87964cffd8baa685c5d3902bd5b8143a0dcbcaff/ressources/readme/playlists.png?raw=true)  
Page des playlists du back-office, avec la possibilité de filtrer comme dans la partie front. Trois boutons ont été ajoutés. Les boutons "Modifier" et "Ajouter" permettent d'accéder au formulaire.
### Page 4 : le formulaire des playlists
![img4](https://github.com/r4ndomfriday/PortFolio/blob/87964cffd8baa685c5d3902bd5b8143a0dcbcaff/ressources/readme/playlist.png?raw=true)
Dans ce formulaire, seul le nom est obligatoire. Si "Playlist : .. " est rattaché au nom d'une formation, cela signifie que la formation est déjà associée à une playlist. Une formation ne peut être associée qu'à une seule playlist. 
### Page 5 : les catégories
![img5](https://github.com/r4ndomfriday/PortFolio/blob/87964cffd8baa685c5d3902bd5b8143a0dcbcaff/ressources/readme/categories.png?raw=true)
Liste des catégories avec possibilité de trier et de filtrer. Vous pouvez ajouter, modifier ou supprimer une catégorie.
### Page 6 : le formulaire des catégories
![img6](https://github.com/r4ndomfriday/PortFolio/blob/87964cffd8baa685c5d3902bd5b8143a0dcbcaff/ressources/readme/categorie.png?raw=true)
Dans ce formulaire, seul le nom est obligatoire. Une Catégorie peu être rattaché a plusieurs formations et inversement.
## Installation en local :

installer  [Symfony CLI](https://symfony.com/download).  
Telecharger la branche prod de l'application
 
```bash
symfony composer install #a la racine du projet
```     
### Installer OpenJDK :  
   [https://openjdk.java.net/ ](https://openjdk.java.net/ )  
Dans la nouvelle page, paragraphe “Download”, cliquer sur le lien “jdk.java.nt/18”.
Une fois le fichier téléchargé, aller dans Donwloads, clic droit sur le fichier, “Extract all” et choisir comme emplacement “C:\” puis “Extract”.
En racine de C, le dossier “jdk-18.×.x” a été créé.
Dans les variables d’environnement système :
- Dans la variable path, ajouter le chemin vers le dossier bin du jdk (du genre “C:\jdk-18.×.x\bin”).
- Créer la variable “JAVA_HOME” et mettre le chemin vers le dossier du jdk (le dossier racine, donc du genre “C:\jdk-18.×.x”).  
Enregistrer.

### Installer Keycloak:  
Telecharger  [keycloak-19.0.1](https://repo1.maven.org/maven2/org/keycloak/keycloak-quarkus-dist/19.0.1/)

```bash
bin/kc.bat start-dev
```

Configurer Keycloak en local (localhost:8080).  
Créer un royaume "Myapplis"  
Dans Clients, cliquer sur “Create client”.  
Ajouter un client "mediatek86"  (Client ID) qui correspondra à l’application qui sollicite Keycloak  puis “Save”.  
Vous pouvez saisir la même valeur pour le Name.  
Lors des différentes étapes de création du client, contrôlez les paramètres suivants :
- Client type : OpenID Connect
- Always display in console : Off
- Client authentication : On
- Authorisation : Off
- Standard flow : On
- Implicit flow : On
- Direct access grants : On
- Service accounts roles : Off
- OAuth 2.0 Device Authorization Grant : Off
- OIDC CIBA Grant : Off
Pensez à enregistrer (Save en bas). Une fois le client créé, vous êtes normalement dans l’onglet “Settings”. Faites les contrôles et modifications suivantes :
- Enabled : On (en haut à droite)
- Valid redirect URIs (dans Access settings) : *
- Consent required (dans Login settings) : On
- Display client on screen : On
- Front channel logout (dans logout settings) : Off
- Backchannel logout session required : On
- Backchannel logout revoke offline sessions : Off
Pensez à enregistrer (Save en bas)
Toujours sur ce client, allez dans l’onglet “Credentials”.
Puisque vous avez défini un accès “confidential”, vous avez un code secret (c’est le “client secret”).
Copiez ce code secret et gardez-le : il va être inséré dans un fichier du projet.
Créer un utilisateur

Dans “Users”, cliquer sur “Create new user”.
Donner le nom de l’utilisateur qui doit avoir les droits pour accéder à certaines parties de l’application (par exemple la partie “admin”).
Donner un email (peu importe), Enabled (ON), Email Verified (OFF), puis “Create”.
Aller dans “Credentials” pour définir un mot de passe (sécurisé), sans oublier de mettre
“Tempory” à OFF.
Ne pas enregistrer les cookies

Pour que la demande de déconnexion se fasse correctement, mettre les cookies à “Disabled” (Authentication > Flows > Browser > Cookie)


### Importer la base de données
importer mediatekformation.sql qui ce trouve a la racine du projet dans un SGBD Mysql/Mariadb.  


### Configuration de l'application
Pour que l'authentification fonctionne vous devez rajouter au .env a la racine du projet  

```python
DATABASE_URL="mysql://utilisateur:motdepasse@127.0.0.1:3306/database"   # seul une DATABASE_URL doit être des-commenté
KEYCLOAK_SECRET="client secret" # renseigné dans l'onglet “Credentials”
KEYCLOAK_CLIENTID=mediatek86
KEYCLOAK_APP_URL=http://localhost:8080
```


### Lancer l'application

à la racine de l'application :  
```bash
symfony server:start
```
