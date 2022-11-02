# MyVOD

Détails :

Ce projet permet de répertorier les films comme Plex. Il permet d'aller chercher les informations issues d'Allociné ou DVDFr.
Avec l'interface réseau de votre ordinateur, il permet de visualiser les vidéos sur une tablette, autre PC... sans ré-encodage.
MyVOD est compatible avec PHP5 et PHP7. Testé avec un environnement Windows. Pas testé sous Linux. 

    Android : Je vous conseille d'installer BSPlayer FREE. Il permet de lire beaucoup de formats et gère les chemins réseau. Voir sur Google Play...

    Android : Depuis une certaine mise à jour, Chrome sous Android ne propose plus BSPlayer pour lire les fichier m3u. Avec Firefox sous Android, il n'y a pas ce soucis. Vous pouvez toujours utiliser Chrome en choisissant Firefox comme lecteur puis depuis Firefox, prendre BSPlayer. Voir sur Google Play...

Installation :


Ce projet est un projet php. Son installation est comme pour un site php. 
Le point d'entrée est index.php à la racine.
Au 1er lancement, MyVOD demande de se connecter. Le mot de passe est 1234. Vous pourrez le changer après. 
Une fois le mot de passe validé, la page principale de paramétrage va s'afficher.
Veuillez remplir les différents champs. Enjoy!!!



Ce projet utilise :

    Ce projet est un projet php simple sans framework. Il est léger

    MediaInfo :
    MediaInfo fournit des informations techniques et les tags à propos de vos fichiers video et audio.
    Site web : https://mediaarea.net/fr/MediaInfo

    API Allociné Helper :
    L'API Allociné Helper permet d'utiliser plus simplement l'API d'[AlloCiné](http://www.allocine.fr/).
    Site web : https://github.com/etienne-gauvin/api-allocine-helper

    Highcharts :
    Highcharts est une bibliothèque de cartographie écrit en JavaScript pur, offrant un moyen facile d'ajouter des graphiques interactifs à votre site web ou une application web.
    Site web : http://www.highcharts.com

    Bootstrap :
    Bootstrap permet de rendre un site en responsive (indépendant de la taille de l'écran)
    Site web : http://getbootstrap.com

    Font Awesome :
    Font Awesome vous donne des icônes vectorielles évolutives qui peuvent instantanément être personnalisées - taille, couleur, ombre portée, et tout ce qui peut être fait avec la puissance de CSS.
    Site web : http://fontawesome.io





Historique des versions :

A FAIRE :
- api sens critique en php : on verra plus tard quand il y aura une API.
- api TMDB : idem.



========================================================================
    VERSION 2.4.x (Meilleures performances + support PHP7) - Version actuelle
========================================================================

version V.2.4.4, 2020/08/19
--------------------

x Fix : Erreur lors d'une recherche Allociné.
x Page maintenance-fichiers.php, quelques petits bugs d'affichage corrigés.
x Prise en compte du "directory separator" lors d'une recherche de fichiers.


version V.2.4.3, 2018/12/25 (Christmas edition)
--------------------

+ Ajout Filtrer uniquement les films pas encore visionnés.
+ Ajout lien de la page de MediaInfo dans la page de détails.
+ Ajout thème de noel si on est au mois de décembre (avec effet de neige qui tombe).
+ Page gerer-affiches.php : Optimisation de l'algo.
x Page maintenance-fichiers.php : Fix nom de fichier s'il contient un "&" dans le nom. Il était tronqué lors d'une recherche.
x CheckBox & Radios : Le texte associé est cliquable.
x Page plusieurs-fichiers.php : La recherche de doublons est case insensitive sur le titre du film (il pouvait y avoir des manques dans les résulats si la casse est <>).
x Page detail-modif.php : Fix : Impossible de changer l'affiche (url ou via l'upload) juste après une recherche Allociné ou DvdFr.


version V.2.4.2, 2018/11/17 (Bon anniv Papa)
--------------------

+ Ajout option de validation automatique si le nombre de resultat de recherche est de 1.
+ Quand on se connecte en mode admin, on revient maintenant sur la dernière page consultée.
+ Ajout ToolTip quand le champ a le focus.
+ Page detail.php, quand on supprime une fiche on est redirigé vers la page index.
+ Affichage Liste (index.php) : Ajout des réalisateurs & acteurs.
x Page detail-modif.php : affiche quand un champ est obligatoire (avant de valider la fiche).
x Menu admin sélectionné dans les pages d'admin.
x modifications du css (carré bleu enlevé sous Chrome).
x 1ere lettre en majuscule dans les bulles d'aide.
x Petite modification du css pour la galerie et les toolTips.
x Page index.php : 'onmouseover' & 'onmouseout' remplacé par 'onmouseenter' & 'onmouseleave' pour faire moins d'évenements js.


version V.2.4.1, 2018/10/22 (Bon anniv Maman)
--------------------

+ Ajout d'une option d'affichage en liste pour les tablettes et téléphones.
+ Ajout d'un message interne pour les fiches sur plusieurs lignes.
+ Ajout d'une option de tri par défaut dans les paramètres.
+ detail-modif.php : Ajout de la résolution à coté de la taille du fichier.
+ connexion.php : taille de la saisie un peu retravaillée.
+ detail.php : le titre est désormais en haut de l'affiche si écran petit.
+ index.php : factorisation des fonctions "afficher_resultat" et "afficher_resultat_liste".
+ detail-modif-change-nom-fichier.php : Ajout option pour "updater" la date heure de création de la fiche.
+ detail.php : Ajout de la date de création et modification au survol de la souris sur le titre.
+ index.php & affichage liste : Les affiches sont centrés verticalement.
+ ToolTips customisés.
+ Bandes annonces plus grosses pour les écrans de moyenne taille.
x Fix : bugs d'affichage sur le 1er élément des derniers ajouts et visu en mode liste.
x Fix : mise à jour de la date heure de modification pour les fiches (n'était pas actualisé).
x statistiques.php : fix bug d'affichage : les données était sous forme de float.
x index.php : Modification dans les regexp (les espaces sont considérés comme des jockers).


version V.2.4.0, 2018/10/09 (Bon anniv Charline (4 ans))
--------------------

+ Meuilleure compatibilité avec PHP7.
+ Ajout d'index dans les liaisons.
+ Vues (dans la base de données) 'Detail', 'DetailSansDoublon' et 'DetailSansDoublonFiltre' réécrites pour de meilleurs performances (gain x3 en tant qu'admin sur la page d'index).
+ Ajout d'un paramètre pour des mots clés supplémentaires lors des recherches google.
+ Affiche la version de PHP en mode admin dans le bas de la page.
+ maintenance-fichiers.php : quand on clique sur "rechercher sur le disque", le 1er onglet est par défaut actif (pour mettre en évidance les nouveaux fichiers).
+ detail-modif.php : La fiche est enregistrée automatiquement quand on manipule l'affiche.
+ detail.php & detail-modif.php : quand appuie sur la touche "Esc" et affiche en gros plan on ferme ce gros plan (voir code de l'alert).
+ index.php : accélération ancre #last_visionnes, #last_added_movies et #last_added_movies_suite (ajout code js pour atteindre avant la fin de chargement de la page).
+ index.php : Dans le panel "derniers films ajoutés", la liste est portée à 320 éléments.
+ index.php : La vue "liste" a été refaite et des éléments sont mis en commun avec la vue "galerie".
+ index.php : Quand on change l'ordre du tri (à gauche) et qu'il n'y avais pas de recherche, on ne lance plus de nouvelle recherche.
+ Quelques engolivements au niveau de l'interface.
x Fix : Erreurs "require_once" en PHP7.
x Fix : Gestion de cache avec les accents sur le nom de fichier en PHP7.




========================================================================
    VERSION 2.3.x (affichages d'onglets)
========================================================================



version V.2.3.3, 2018/09/17 (Bon anniv Alice (1 an))
--------------------

+ Page detail.php & index.php : Ajout du bouton "Rechercher sur Google".
+ Page : Ajout d'un bouton "Editer la fiche" (Bien utile quand il n'y a pas de résultat et qu'on veut éditer manuellement une fiche).
+ Ajout option de taille de fichier sur 64 bits. (Car elle est + lente en mode 64 qu'en 32b).
+ Page maintenance-fichiers.php : Mots "Black-lister" remplacé par "Ignorer" pour une meilleure compréhension.
+ Page recherche-web.php : Ajout d'un bouton pour réinitialiser les champs de recherche.
+ Ajout d'index sur les bandes annonces.
+ Dans la Gestion du cache : Prise en compte du changement de taille de fichier pour le changement de cache.
+ recherche-web.php : Amélioration de l'algo de remplacement de mots du dico.
x Fix : Quand une fiche contient pleins de bandes annonces et qu'on fait une recherche allociné ou dvdFR avec moins de ba, les anciennes ne sont pas purgés.
x Fix : Page plusieurs-fichiers.php : la resolution n'apparait pas forcément si on n'a pas consulté une première fois la fiche (aller ds la page detail.php).
x Fix : Affichage de la taille des fichiers > à 4Go.


version V.2.3.2, 2018/08/07
--------------------

+ Page index.php : Ajout filtre sur la taille de fichier lors d'une recherche de film.
+ Popups : Quand on clique que l'overlay, la popup se ferme.
+ Popups : Quand on fait "Eschap", la popup se ferme.
+ Page index.php : Ajout tri "Plus ancien au plus récent".
+ Pages index.php & details.php : Ajout du logo "VFF" pour dire qu'un fichier est en TrueFrench ou VFI(considéré comme VFF).
+ Pages index.php & details.php : Ajout du logo "MKV" pour dire qu'un fichier est en mkv.
x Page recherche-web.php : Fix erreur PHP quand il y avait seulement le mot "mi" ou "my" car Allociné envoyait des des éléments incomplets.
x Page detail.php : Fix onglet "Informations sur le media" inaxessible.


version V.2.3.1, 2018/07/06
--------------------

+ Ajout d'un sablier quand on charge une page (détection du changement de page).
+ Ajout possibilité de modifier une fiche depuis la premiere page (la page d'index).
+ Page detail-modif.php : Ajout message quand on supprime une bande annonce.
+ Page detail.php : préchargement miniature avant de charger la grosse image (astuce donné dans https://css.developpez.com/tutoriels/icone-chargement-pour-images/)
+ Pages maintenance-fichiers.php, plusieurs-fichiers.php, gerer-affiches.php : Ajout du nombre d'éléments concerné dans les onglets.
x Fix ancre Firefox : FireFox gère mal ses ancres (marche pas dans modif avec FireFox (#debut_liste_bande_annonce)).
x Page detail-modif.php : Fix : Impossible de supprimer les bandes annonces supplémentaires.
x Page detail.php : Affichage du texte "Pas de note" à la place de "0, 0".
x Page detail.php : Fix : N'arrivait pas à supprimer la fiche quand il avait un "'" dans le titre.
x Pages detail.php & detail-modif.php : Meilleure ancrage des bandes annonces supplémentaires.
x Page detail.php : Fix : Informations sur le média collé à l'affiche sur les grands écrans.
x Fix : Bug d'affichage pour les bandes annonces : des ascenseurs apparaissaient.
x Fix : ERR_BLOCKED_BY_XSS_AUDITOR à certains moment avec le moteur de Chrome.
x Fix : detail-modif.php : problèmes pour récupérer les affiches avec des noms contenant des caractères html (ex: "%20").
x Fix : maintenance-fichiers.php : Les doublons n'étaient pas détectés si déjà enregistrés dans la bdd.
x Fix : Remplacement de '::1' par '127.0.0.1' pour mieux gérér l'historique en localhost.


version V.2.3.0, 2018/06/05
--------------------

+ Pages maintenance-fichiers.php, plusieurs-fichiers.php,gerer-affiches.php, filtrage-parental.php, parametrages.php : Mis sous forme d'onglets pour mieux visualiser les résultats.
+ Page détails-modif.php : Ajout d'un message pour rappeler de valider la fiche suite à une recherche web.
+ Fix css dépassement en largeur sur les pages où il y a des tableaux en format téléphone.
x Page détails-modif.php : Fix : Quand on renseignait plusieurs fiches en même temps, il y en avait qu'une de bonne car le retour se faisait avec la session php (la reférence est retournée en POST).
x Page maintenance-fichiers.php : Fix : erreur filsize quand on supprime un fichier et qu'on ré actualise la page.
x Quelques pages du back-office passent mieux au format téléphone.




========================================================================
    VERSION 2.2.x ( Ajout de catégorie Nationalité + Ajout de l'historique...)
========================================================================



version V.2.2.8, 2018/05/25
--------------------

+ Passage de DVDFr en https pour son API.
x Page admin.php : Fix si le dossier des films est innexistant pour le calcul de la place restante.


version V.2.2.7, 2018/05/24
--------------------

+ Page admin.php : Quelques colorisations pour les extensions actives.
+ Page maintenance-fichiers.php : Les nouveaux fichiers sont triés par ordre alphabétique.
+ Page detail.php : Ajout liens de suppression si pas de fichier ou titre égal au nom de fichier (fiche incomplète).
+ Optimisations de traitements de la page maintenance-fichiers.php.
+ Page 'maintenance-fichiers.php' : Utilisation de la session du résultat de recherche de fichiers car met trop de temps du fait d'une recherche est effectuée à chaque fois.
+ Chrome Android : alout de la couleur personnalisée de la barre des titres.
+ Page recherche-web.php : mettre le nom du fichier origine en info (pour pouvoir copier coller).
- Page détails : Gestion glisser pour changer de fiche (cf librairie Hammer.js) car crée trop de soucis avec Android (29/09/2017)
+ Page détails modif : Possibilité de récupérer une affiche en https + affichage d'un message s'il y arrive pas.
+ Page détails modif : Upload directe quand on a choisit une image pour l'affiche.
+ Page détails : Gestion glisser pour changer de fiche (cf librairie Hammer.js).
x Page maintenance-fichiers.php lien supprimer fiche vers une erreur 404 ("trt_suppression_fiche.php").
x Page admin : Affichage de la taille en To si > 1To pour le camembert d'occupation des disques.
x Fix : Taille de fichier en négatif quand on faisait une recherche de fichier.
x Quelques champs renommés.
x Certains fichiers js sont intégrés à la fin de la page pour accélérer le chargement des pages.
x Fix : detail-modif.php : quand on est a moitié de l'écran (en full hd), le bt valider est caché par une div d'a coté (corrigé via un z-index).
x Fix : logo MyVod derrière un scream error.
x Fix : Page maintenance-fichiers.php : erreur SQL quand on enlève un fichier de la black liste.
x Fix : Page statistiques.php : Erreur js sur les graphiques.


version V.2.2.6, 2017/04/19
--------------------

+ Historisation des derniers lus en vu d'une future évolution (compter le nombre de visionnages,...).
+ Ajout d'un test de doublons d'historisation sur 1 heure.
+ Page gerer-affiches.php : Amélioration de l'affichage des résultats.
+ Ajout du nom du fichier quand on pose la question de suppression de fiche.
+ Dans plusieurs fichiers : On peut supprimer une fiche si fichier n'existe pas.
+ Ajouts des aperçus d'affiches des films précédents et suivant au survol de la souris.
+ Création automatique de fiche vierge si pas de résultat d'AlloCiné ou DvdFr.
+ Fenêtre de connexion : Ajout du bouton de connexion mode parent.
+ Ajout de la possibilité de passer de admin & parent (ajout d'un autre bouton de déconnexion sous forme de filtre).
x Fix : Problème de redirections quand on modifie le nom du fichier.
x Fix : Petites retouches au niveau de l'alert CSS (2 ascenseurs apparaissait quand l'écran n'était pas assez haut).
x Fix : Quand on fait annuler et quand on fait une recherche d'une nouvelle fiche et pas de résultat, il y avait une erreur SQL.
x fix : Type MIME changé pour les exports CSV.


version V.2.2.5, 2017/02/09
--------------------

+ Connexion mode parental : Le code admin déverrouille le mode parental.
+ Page parametrages.php : Réorganisation de l'emplacement du bouton "Enregistrer".


version V.2.2.4, 2016/12/02
--------------------

+ Ajout de l'heure de visionnage quand un film est déjà vu.
+ Dans la popup ajout d'un petit ombrage pour mieux ressortir l'affiche (via css)
+ Etoiles de notations moins visibles quand il n'y a pas de note
+ Meilleure visibilité dans les items du menu d'administration pour les très petits écrans (- de 384 px de largeur).
x Page filtrage-parental.php : Type de publique quand on autorise un type de publique vide, la laveur NULL était inséré dans la bdd au lieu d'une chaine vide.
x Fix css : dans le menu il y a un trait simple en dessous du menu dépliant (admin)
x Fix : page des paramètres : on dirait que les éléments ne sont pas alignés


version V.2.2.3, 2016/10/14
--------------------

+ Ajout d'une icône quand la vidéo a été lu au moins une fois.
+ Page gérer plusieurs fichiers (coté admin) : Ajout du lien "Voir la fiche"
+ Ajout du réalisateur et des acteurs dans affiche galerie (jouer avec css pour que ça apparait quand on clique avec popup)
+ Quand on fait une recherche sur allocine ou dvd fr on peut enlever dernier mot a chaque fois qu'on ne trouve pas(récursif et automatique)
+ Menu qui se déroule au survol de la souris
x Fix : Page detail modif : lors d'une récup d'une nouvelle fiche via le net, il y avait une erreur sql.


version V.2.2.2, 2016/09/23
--------------------

+ Possibilité d'exporter en csv un résultat de recherche ou la totalité de la base
+ Fix? : Page de connexion : L'onglet admin n'était pas visible.


version V.2.2.1, 2016/06/23
--------------------

+ Ajout d'un bouton pour réduire les panels Genres, Publiques, Années et Nationalités.
+ Ajouts d'index dans la bdd.
+ Page d'index (principale) : Le test de l'existence du fichier à chaque affiche a été supprimé pour améliorer les performances.
+ Page recherche web : les résultats de recherche sont en 2 ou 3 colonnes pour les grands écrans.
+ Changement du lien du bandeau et en bas de page.
+ Panel des mots clé du dico repliable dans la page recherche-web.
+ Ajout du champ résolution dans films en 2 fichiers (page 'plusieurs-fichiers.php').
+ Page détails : les informations sur média sont au dessus des bandes annonces.
x Fix : Quand on vient d'être admin ou parent, et qu'on fait F5, une recherche avec tous les éléments est lancé et pénalise le temps de chargement de la page.
x Fix : Récupération d'affiches : erreur quand l'url contient une requête (contient un '"?")


version V.2.2.0, 2016/05/30
--------------------

+ Ajout de catégorie Nationalité lors de la recherche (page index.php).
+ Ajout de la nationalité dans l'affichage des résultats (en mode galerie).
+ Page recherche : Repli automatique quand touts les items sont cochés (Genres, Publique, Années & Nationalité).
+ Ajout bulle pour le bouton initialiser tous les filtre (en forme de croix sur la page d'index) et ce bouton ré-initialise tous les champs de la page.
x Fix : Page index, quand on clique sur aucun publique, le nombre coché ne se rafraîchit pas.
x Quand on sélectionne un film dans la liste déroulante de recherche, les autres éléments n'étaient pas initialisés (Qualité, filtre sur titre...) et pouvait conduire à un résultat nul.
x Détail modif : dispositions et alignements des boutons de recherche.
x Quelque retouches ponctuelles faites au niveau de l'affichage.




========================================================================
    VERSION 2.1.x (contrôle parental)
========================================================================



version V.2.1.12, 2016/05/20
--------------------

+ A l'invite du mot de passe pour l'extinction, le mot de passe est caché sous forme d'étoiles.
+ Page de modifications de fiches : Ajouts des boutons recherche Allociné et DVDFr dans le panel des Valider / Annuler.
+ Page détails : on voit mieux le titre quand la fiche n'est pas renseignée.
+ Page de modification de film, ajout de l'année de sortie/production différente de la date de sortie.
+ Ajout de la possibilité d'afficher + de dernier films visionnés.
+ Ajout bouton depuis modif fiche : actualiser fiche depuis internet.
+ Boutons RAZ (du nombre de dernier ajoutés et lus) caché si le nombre est égale à celui par défaut.
+ Page d'index : les années de sorties est dans le sens inverse pour que les + récents apparaissent en 1er.
+ Chrome et Android : prise en compte du favicon dans la page d'accueil.
x Recherche Allociné : Quand la date de sortie n'est pas renseignée, il prend la date de sortie DVD.
x Image de fond mieux étirée sur les petits écran en mode portrait.
x Fix : Quand on filtre sur l'année, genre ou type de publique et qu'on choisit un film dans la recherche. Il n'apparaît pas car années/type de publique/genres ne sont initialisés.


version V.2.1.11, 2016/05/10
--------------------

+ Ajouts de suggestions pour les recherches.
+ Page index : quelques éléments sont mis en cache.
+ Page index : menus Genres et Publique ont été retouchés pour les moyens écrans.
+ Page Information du système (page admin) un peu retouchée.
+ Page gérer affiche : Ajout des miniatures des affiches dans la partie "X affiches peuvent être supprimées".
x Fix : Quand on passe en admin et qu'on fait "gérer de nouveaux fichiers" et qu'on revient sur l'index, tous les genres/public/années de sorties ne sont pas cochés et fausse la recherche.
x Fix : Erreur js (non visible pour l'utilisateur) lors d'une recherche.
x Fix : Qaund on faisait une recherche Allociné, la nationalité n'était pas toujours remontée.
x Fix : Page plusieurs fichiers : Si le film est composé de 3CD, on ne pouvait pas les lier (ils apparaissaient dans la liste des doublons).
x Fix : Conflit des ancres sur la page d'index avec les champs avec autofocus


version V.2.1.10, 2016/04/25
--------------------

+ Ajout du tri par ordre par durée dans la recherche.
+ Quelques modifications dans le thème (titres) coté back office.
+ Affichage du titre original dans la bulle dans les résultats.
x Popups : bug d'affichage avec une tablette en mode horizontal. Le bas était affiché et le titre de la popup était coupée.
x La flèche remonter est un peu coupée sur des petits écrans horizontaux.


version V.2.1.9, 2016/04/19
--------------------

+ Ajout recherche en ignorant les accents et ajout d'un joker
x Fix : Page détail-modif.php. Quand on faisait entré sur le champ "Rechercher les informations sur le web" valide la page au lieu de faire le recherche.


version V.2.1.8, 2016/04/08
--------------------

+ Ajout de la modification des affiches par upload.
x Fix : Recherche allociné avec des mots clés avec accents. Il ne retournait pas de résultat.
x Fix : Graphique d'occupation du disque : bug d'arrondi
x Fix : Index.php : La combo box 'afficher + de résultats' n'était pas accessible dans certains cas de figures


version V.2.1.7, 2016/04/07
--------------------

+ Menu + adapté aux très petits écrans (- de 320 px).
+ Focus automatique quand il y a une popup.
+ La taille du disque est en Go (pour le graphique occupation du disque)
x Fix : Page plusieurs fichiers : il y a plusieurs fichiers introuvables alors qu'ils existent.
x Fix : Menu : le bouton déconnexion et petit écrans : il peut y avoir un décalage.
x Quelques optimisations diverses


version V.2.1.6, 2016/04/06
--------------------

+ Ajout de l'option de recherche par genre principal ou secondaire
+ Ajout du tri par ordre par notation dans la recherche
+ Ajout de l'option de tri dans le résultat de recherche
+ Ajout du logo du contrôle parental dans la page détail.
+ Ajout de l'espace disque avec camembert dans la page information du système
+ Ajout d'un raccourcis dans le menu pour éteindre le serveur.
x Quelques retouches au niveau du popup détail pour éviter les ascenseurs.


version V.2.1.5, 2016/03/25
--------------------

+ Ajout d'un paramètre pour le nombre de derniers films ajoutés par défaut.
+ Ajout d'une option pour afficher dernier films visionnés après les ajout.
+ Affichage mettre l'affichage en 1 par ligne pour les petits écrans (<=320px).
+ Quelques retouches au niveau du popup détails.
+ Ajout d'un message quand on valide et que le embed est remplie mais pas son URL (de la bande annonce)
x Fix : Page 'detail-modif.php' quand on Chargeait une affiche, l'URL n'était pas mis à jour.


version V.2.1.4, 2016/03/23
--------------------

x Quelques retouches esthétiques au niveau des popups.
x Fix : Page recherche-web.php : impossible de lancer une recherche après avoir modifié le champ de saisie.


version V.2.1.3, 2016/03/22
--------------------

+ Ajout de la recherche sur le nom du fichier,
+ Ajout d'un popup quand on clique sur le texte (résumé),
+ Ajout de l'affichage de notation en étoiles (page d'index et détails).


version V.2.1.2, 2016/03/16
--------------------

+ Ajout d'un paramètre pour le nombre de derniers films visionnés,
+ Quelques modifications dans les messages quand un fichier n'est pas trouvé,
+ Quelques retouches dans le thème,
x Quelques bugs d'affichage corrigés.


version V.2.1.1, 2016/03/08
--------------------

+ Ajout du tri par type de publique lors de la recherche,
+ Ajout du tri par médias appartenant au contrôle parental,
+ Statistiques : Ajout liens de recherche.


version V.2.1.0, 2016/03/07
--------------------

+ Ajout du contrôle parental,
+ Ajout liste blanche des genres et par type publique.
+ Statistiques : ajout des nombres en + du pourcentage.
x Fix : bug d'affichage des affiches en gros plan.




========================================================================
    VERSION 2.0.x (ré écriture du code)
========================================================================



version V.2.0.7, 2016/02/29
--------------------

+ Page index : menu à gauche moins large pour faire apparaître les résultats de 4 à 6 items par ligne pour les grands écrans (<1400px).
x Fix : Valeur de la taille des fichiers de plus de 2Go était erronée.


version V.2.0.6, 2016/02/26
--------------------

+ Mise en cache de média info pour accélérer le temps de chargement de la page détail.
+ Ajout de la résolution du fichier dans la page détail.


version V.2.0.5, 2016/02/19
--------------------

x Mode smartphone : Bug d'affichage du menu qui passait par dessus les autres champs.
x Compatibilité IE11 : Les menus étaient mal affichés + le side bar.
x Thème : pré-chargement du bandeau (en version plus petit) car Opéra 12 l'affichait en dernier.


version V.2.0.4, 2016/02/18
--------------------

+ Ajout de l'export dans les statistiques.
+ Désactivation de l'ajout dans les "derniers lus" quand on est administrateur.
+ Les animations des statistiques ont été enlevés.


version V.2.0.3, 2016/02/17
--------------------

+ Ajout d'une page de statistiques (par genre, type de publique, année de production)


version V.2.0.2, 2016/02/16
--------------------

+ Le bouton 'Modifier la fiche' plus haut
+ Gestion des bandes annonces (celle principale de la fiche MyVOD) de Youtube
+ Ajout dans la fiche les paramètres HD720 et HD1080
+ Ajout de l'option hd dans le panneau de recherche
x Fix : Le 'D' de MyVOD bouffé (bug css Opéra 12 et italics)
x Fix : Les derniers ajoutés étaient effacés après une modification de fiche
x Fix : détail : bug d'affichage divers(fonctionne avec ff mais pas opéra (remonte_page_menu))


version V.2.0.1, 2016/02/14
--------------------

+ Enregistre les derniers films visionnés pour les proposer par la suite.


version V.2.0.0, 2016/02/12
--------------------

+ Ajout message quand on retire un mot du dictionnaire
+ Présentation de la liste des mots clé du dictionnaire améliorée
+ Optimisation de l'ordre de remplacement des mots clés du dictionnaire lors d'une recherche (les mots les + long sont traités en 1er)
+ Pour les grand écrans (hd) le panel de recherche passe à gauche pour optimiser l'espace (page index).
+ Ajout d'un panel à gauche pour les écrans larges et page détails/ détails modif.
+ Ajout de l'affichage en galerie
+ Gestion des bandes annonces
+ Affichage galerie, mettre les détails en hover
+ Ajouts des données "numfiche allocine", "genre2" , "nationalité", "url source img", "url bande annonce"
+ Ajout de la visualisation de la bande annonce dans la fiche du film
+ Ajout de la page de paramétrage
+ Ajouter dico de recherche à ignorer lors d’une recherche web (exemple avi, dvdrip )
+ Annulation (par case à cocher) du dico lors d'une recherche
+ Ajout d'un bouton "lire vidéo" en dessous de la bande annonce.
+ Page détails : ajout "ouverture du répertoire" quand on est en local
+ Ajout du tri par date de sortie
+ Depuis maintenance fichier, ajout d'un lien "modifier fiche"
+ Possibilité de modifier le nom du ficher dans la base de données
+ Le chargement des images était long, pour alléger les pages de recherche, un système de miniature a été crée.
+ Image par défaut quand l'affiche est inexistante
+ Redirection vers la page de config si éléments vides (= pas paramétré)
+ Dans la page détails, la gestion de l'ordre des boutons précédent et suivant est modifié si on a cliqué sur un dernier ajout.
x Bugs divers corrigés + remaniement du code
x Fix : quand pas de base -> bug boucle infinie




========================================================================
    VERSION 1.x (1ere version)
========================================================================



version V.1.6 final, 2015/01/18
--------------------

+ réorganisation de code pour la préparation de la version 2.0
x page détails modif : quand on frappe entré et qu'on est dans le champ "recherche" il y avait validation du formulaire au lieu d'envoyer la recherche allociné
x page détails modif : recherche par numéro de fiche, les numéros n'étaient pas pris en compte
x boutons précédent et suivant (dans la fiche détails): un voit encore les fichiers en plusieurs fichiers


version V.1.6, 2015/01/18
--------------------

+ l'onglet admin est transformé en menu (dropdown)
+ recherche sur allo cine ou dvdfr en mettant le numéro depuis détails
+ affiche (dans la page détails) plus petite pour écrans larges (TV par exemple)
+ depuis nouveaux fichier : faire création fiche et lancer recherche web et validation directe via le lien "Créer la fiche et rechercher les informations"
+ ajouter bouton supplémentaire pour lire la vidéo dans la fiche détail (plus haut dans la page)
+ détecter les affiches mortes (dans mycinemadata/img) : reste les doublons
x correction du nombre de film quand il était lié
x quand on va sur plusieurs fichiers.php, il y a des erreurs sur gestion-cache.php quand on vient de déclarer un nouveau fichier
x bug dit que numéro fiche est champ obligatoire
x quand on lance une recherche avec le texte modifié : tout cocher pour faciliter la recherche
x affichage détails : correction bug quand il avait un "'" dans le nom de fichier.
x correction : les films en 2CDs apparaissait en double dans "les derniers ajouts"
x Correction erreur sur une recherche AlloCiné quand il n'y avait pas de Synopsis (exemple si on sélectionne Jackass 3.5)
x Correction probleme de création de fiche avec un nom de fichier contenant le caractère "+"
x Quand on recherche (via recherche web) via nouveau ajout les infos ne sont pas crées et il y a un var_dump dans cette page (la 1ere fois)


version V.1.5, 2015/01/03
--------------------

+ recherche par année dans la page principale
+ ajout du raccourcis "admin" partout si utilisateur est connecté
+ ajout des liens pour supprimer les fiches orphelines (dans la page 'maintenance fichier')
+ page modif : regarder la redirection quand on fait sauver (voir quand on fait retour du navigateur)
+ gestion de plusieurs fichiers (ex films sur 2CDs) avec la possibilité de lier les fichiers
+ Gestion de plusieurs fichiers dans le m3u
+ enlever du résultat de recherche les fiches liées
+ affichage de l'affiche plus petite pour les grands écrans
x correction bug : quand on vient de créer une fiche, il y avait une erreur sql.
x correction bug : quand on vient de créer une fiche, le genre et l'année de sortie étaient vides.


version V.1.4, 2014/12/18
--------------------

+ ajout de la recherche des information sur http://www.dvdfr.com/
+ page de doublons : Les différents nombres sont affichés et totaux
+ page de doublons : Ajout d'un lien dossier pour "ouvrir répertoire" si on est en localhost et sous windows
+ page de doublons : Ajout de la détection des orphelins
+ ajout du lien pour télécharger le film dans la page détail.


version V.1.3, 2014/12/08
--------------------

+ création de nouvelles fiches
+ liste les fichiers en conflits (doublons sur le nom de fichier)
+ gestion des blacklists pour les nouvelles recherches de nouveaux fichiers.
+ gestion des répertoires video_ts et factoriser fonction de recherche de fichiers (en beta test)
+ fiche détails : "informations sur le media" : faire plier , déplier (par défaut, il est plié
+ flèche qui monte : rendre invisible en fonction de l'ascenseur (scroll)
x correction bug d'écrasement d'image si plusieurs films ont le même nom de poster.
x correction exception : problème de création de table dans cache.db quand il est supprimé


version V.1.2, 2014/12/03
--------------------

+ modification de fiche (avec un accès admin)
+ va chercher les infos sur allo ciné pour remplir les fiches
+ gestion de l'affiche par url
+ quand on clique sur le nombre à coté du genre, on fait la recherche avec le genre correspondant
+ factoriser code du menu
+ quand on fait une recherche sur le titre, il y a aussi sur le titre original.


version V.1.1, 2014/11/18
--------------------

+ Nouveau thème
+ ajout filtre sur les genres
+ utilisation de "média info" (en cli)
+ prise en charge des fichiers wpl
+ m3u compatibles avec Androids et Windows
+ 10 derniers films : mettre la durée + ordre dans l'autre sens (le +récent en haut)
+ modifier bandeau pour prendre la résolution native de la page (1140)
+ index.php : persistance de recherche (utile quand on fait un retour)
+ 10 derniers films : possibilité de voir ceux avant (+ de 10 résultats)
+ possibilité de modifier le tri (par ordre d'ajouts ou de titre) pour l'affichage des résultats
+ mettre l'année avec les résultats
+ voir mettre en local pour les m3u (si ip en local)


version V.1.0, 2014/11/13
--------------------

+ Ajout des petites jaquettes pour les ajouts des 10 derniers films (page principale)
+ Mise en cache des recherches de fichier (dans la page détails) (et suppression du cache si fichier supprimé)
+ Bouton fiche détaillé (ajout du nom du site "Allociné" par exemple)
+ passe par un fichier m3u et la partage Windows pour voir la vidéo
+ ajout d'un lien vers ce fichier (changelog)
+ mettre Allociné entre parenthèse pour la fiche détaillée
+ titre du pg dans l'image
+ affichage de la taille du fichier et du nom (page détail)
+ éteindre le pc depuis le site
+ ajout de la page "a propos"
+ ajout du titre sur chaque page
+ ajout de la session php et stocke l'ordre des résultats pour le suivant et précédent
+ ajouter précédent et suivant dans la page détails
+ affichage du numéro de version
x ordre changé si plusieurs fichiers portent le même titre
x correction bug : ne trouve pas le fichier s'il y a un accent
x image centrée si plus petite que l'écran (page détail)


version Beta, 2014/11/05
--------------------

+ version initiale du projet

Accueil

Code écrit par Laurent Chanson - 2020 - version 2.4.4 (du 19/08/2020) 
