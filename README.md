OSACT 2
=======

OSACT est un site pour suivre l'activité d'une équipe de développeurs, ainsi que le suivi logistique du matériel mis à disposition.

**Nouvelle version car modification de la conception pour les actions pour un suivi de l'activité réelle et de la facturation séparé**

OSACT_MCD.mwb => Schéma de base de données [MySQLWorkbench 5.2.47](http://www.mysql.fr/products/workbench/)

## Cette version s'appuie sur :

* [CakePhp 2.3.1] (http://cakephp.org) - Framework

* [BootStrap 2.3.1] (http://twitter.github.com/bootstrap/) - Style et javascript

* [JQuery 1.9.1] (http://jquery.com) - La dernière version de JQuery

* [Datepicker pour BootStrap] (https://github.com/vitalets/bootstrap-datepicker) - Pour changer du datepicker de JQuery-UI

* [Highcharts 3.0.0 et Highstock 1.3.0](http://www.highcharts.com) - bonne documentation (http://docs.highcharts.com)

## A venir l'utilisation de :

* [CakePDF] (https://github.com/ceeram/CakePdf) - Pour exporter au format PDF pour les rapports

## A venir jusqu'à fin avril :

* Facturation à faire

* Limitation du périmètre de vision en fonction de l'utilisateur (test sur profil ou est hiérarchique ?)

* Incorporation des données référentielles

* Incorporation des données (Utilisateurs, Matériels informatique)

* Utilisation limité de l'outil

## Bugs connus :

* Pour le moment un j'ai rescencé un bug sur l'utilisation de accordion 
(https://groups.google.com/forum/?fromgroups=#!topic/twitter-bootstrap/DhDWN1sGfTM) 
voici un test en ligne si vous avez une solution (http://jsfiddle.net/cameleonhelp/a5xxs/10/)
https://github.com/twitter/bootstrap/issues/6874

* [Select pour BootStrap] (http://caseyjhol.github.com/bootstrap-select/) - Pour modifier l'aspect des select, 
supprimé car impossible de sélectionner une valeur lors d'un ajout dynamique du contrôle