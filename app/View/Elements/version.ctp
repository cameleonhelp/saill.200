<?php 
    $version = '2.0';
    $build = 'b037';
    echo $version.'.'.$build;
    /** 
     * changelog :
     * 
     * b037 Alerte si action ou livrable en retard au niveau de l'échéance ajout d'une class td-error sur les cellules
     * b036 Corrections de quelques bugs mineur en fonctions des profils et droits ouverts
     * b035 A facturer mise en place d'une checkbox et d'une validation en masse
     * b034 IHM du détail du plan de charge dans add.ctp et edit.ctp recopier le contenu de index.ctp et faire la boucle sur les bons objets retournés
     * b033 Modification du menu pour les rapports
     *      creation des methodes rapport pour les actions, activitesreelles, facturations
     *      Correction utilisateur pour l'affichage des outils utiliser dans le détail de l'utilisateur
     *      Ajout et édition du plan de charge, mise à jour du modéle pour restitution des dates au format FR
     * b032 planification à faire -> ajout de la function pour calculer le nombre de jours ouvrés à partir d'une date
     *      bake plancharges et detailplancharges
     *      controllers plancharges et detailplancharges index, add, edit
     *      models plancharges et detailplancharges
     * b031 fin de la facturation et migration vers cakephp 2.3.2 => bascule en production
     * b030 IHM de la liste des facturations faites
     * b029 Travail sur l'apect de la feuille de facturation et la sauvegarde de la facturation
     *      modification schéma de la base de donénes pour la facturation
     * b028 facturation add travail sur l'ajout de ligne dynamiquement, suppression
     *      suppression du composant select, correction effet de bord sur code jquery pour désactiver select
     * b027 correction de bugs + facturations
     * b026 optimisation des requetes
     * b020 utilisation régulière pour le suivi de la logistique et mon usage personnel
     * 
     **/
?>