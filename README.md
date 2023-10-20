# Mediatek Formation

Mediatek Formation est un catalogue de formations sur la programmation 

### [Le projet](https://www.mediatekformation.site/) 
### [Présentation du projet](https://r4ndomfriday.github.io/PortFolio/pages/mediatekFormation.html)  
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


### Importer la base de donnees
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
