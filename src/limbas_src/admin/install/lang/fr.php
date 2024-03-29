<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
global $lang;
$lang = [
    'Welcome'=>'Bienvenue',
    'Dependencies'=>'Dépendances',
    'Database configuration'=>'Configuration de la base de données',
    'Example values'=>'Exemple de données',
    'Summary'=>'Résumé',
    'Installation'=>'Installation',
    'Limbas is already installed.'=>'Limbas est déjà installé.',
    'For security reasons, the installation can only be restarted by deleting the configuration file.'=>'Pour des raisons de sécurité, l\'installation ne peut être redémarrée qu\'en supprimant le fichier de configuration.',
    'Welcome to'=>'Bienvenue à',
    'The configuration file could not be deleted.'=>'Le fichier de configuration n\'a pas pu être supprimé.',
    'You have to delete it manually.'=>'Vous devez le supprimer manuellement.',
    'Remove existing config and start'=>'Supprimez la configuration existante et démarrez',
    'Start'=>'Début',
    'needed'=>'nécessaire',
    '...mbstring present, but func_overload > 0 (must be set to 0)'=>'...mbstring présent, mais func_overload > 0 (doit être mis à 0)',
    'mbstring extension must be enabled to use utf8'=>'L\'extension mbstring doit être activée pour utiliser utf8',
    '...must be enabled to activate file uploads'=>'...doit être activé pour activer les téléchargements de fichiers',
    'Must be set! (e.g. Europe/Berlin)'=>'Doit être réglé! (par exemple Europe/Berlin)',
    'yes'=>'oui',
    'no'=>'non',
    '... you should set readonly after installation!'=>'... vous devez définir readonly après l\'installation !',
    '... you should set readonly!'=>'... vous devez définir readonly!',
    'file can be created'=>'le fichier peut être créé',
    'file does not exist .. try to create FAILED'=>'le fichier n\'existe pas .. essayez de créer FAILED',
    'apache needs recursive write permissions'=>'apache a besoin d\'autorisations d\'écriture récursives',
    'apache might need recursive write permissions'=>'apache peut avoir besoin d\'autorisations d\'écriture récursives',
    'write permissions (recursive)'=>'droits d\'écriture (récursif)',
    'Not found .. in some distros you have to install'=>'Introuvable .. dans certaines distributions, vous devez installer',
    'You can use ODBC for database connection. Available databases are:'=>'Vous pouvez utiliser ODBC pour la connexion à la base de données. Les bases de données disponibles sont:',
    'You can use PDO for database connection.<br>PDO support is only for <b>mysql</b> or <b>PostgreSQL</b>. For other databases use ODBC'=>'Vous pouvez utiliser PDO pour la connexion à la base de données.<br>La prise en charge de PDO est uniquement pour <b>mysql</b> ou <b>PostgreSQL</b>. Pour les autres bases de données, utilisez ODBC',
    'You can use ODBC or PDO for database connection. If you want to use PDO you have to deinstall ODBC '=>'Vous pouvez utiliser ODBC ou PDO pour la connexion à la base de données. Si vous voulez utiliser PDO, vous devez désinstaller ODBC',
    'Functions'=>'Les fonctions',
    'All required functions must work before continuing!'=>'Toutes les fonctions requises doivent fonctionner avant de continuer!',
    'Select how you want to connect to the database'=>'Sélectionnez comment vous souhaitez vous connecter à la base de données',
    'Connect using PDO'=>'Connectez-vous à l\'aide de PDO',
    'Connect using the ODBC-driver'=>'Connectez-vous à l\'aide du pilote ODBC',
    'Connect using a pre-specified ODBC-resource'=>'Se connecter à l\'aide d\'une ressource ODBC prédéfinie',
    'Database settings'=>'Paramètres de la base de données',
    'Database Password'=>'Mot de passe de la base de données',
    'SQL Driver'=>'Pilote SQL',
    'Database Version'=>'Version de la base de données',
    'Database Encoding'=>'Codage de la base de données',
    'Database test'=>'Test de base de données',
    'Information'=>'Information',
    'LIMBAS need ODBC cursor support for ODBC connections. In some cases versions of mysql, mariadb, php or combinations of them does not support cursors. You can try to adjust the connection string in funtion <b>dbq_0</b> in file <b>/lib/db/db_mysql.lib</b>'=>'LIMBAS a besoin de la prise en charge du curseur ODBC pour les connexions ODBC. Dans certains cas, les versions de mysql, mariadb, php ou leurs combinaisons ne prennent pas en charge les curseurs. Vous pouvez essayer d\'ajuster la chaîne de connexion dans la fonction <b>dbq_0</b> dans le fichier <b>/lib/db/db_mysql.lib</b> ',
    'you can improve performance by setting <b>syncronous_commit off</b> in postgresql.conf. Be aware and read'=>'vous pouvez améliorer les performances en désactivant <b>syncronous_commit</b> dans postgresql.conf. Soyez conscient et lisez',
    'You can set it to <b>off</b> for installation and set it to <b>on</b> after installation.'=>'Vous pouvez le régler sur <b>désactivé</b> pour l\'installation et le régler sur <b>activé</b> après l\'installation.',
    // Fehler "meens" -> "means"
    'you can adjust security in <b>pg_hba.conf</b>. <u>Trust</u> meens you trust all local users. <u>password</u> meens you need user and password to connect. For more information read'=>'vous pouvez régler la sécurité dans <b>pg_hba.conf</b>. <u>Confiance</u> signifie que vous faites confiance à tous les utilisateurs locaux. <u>mot de passe</u> signifie que vous avez besoin d\'un utilisateur et d\'un mot de passe pour vous connecter. Pour plus d\'informations lire ',
    'postgresql documentation'=>'documentation postgresql',
    // Fehler "ist" -> "its"
    'do not forget to create your cluster or database with <b>initdb --locale=C</b>. Otherwise ist results in a wrong dateformat! Read'=>'n\'oubliez pas de créer votre cluster ou base de données avec <b>initdb --locale=C</b>. Sinon, il en résulte un mauvais format de date ! Lire ',
    'limbas documentation'=>'documentation Limbas',
    'If you want to use <b>UTF-8</b> you have to create the database with <b>WITH ENCODING \'UTF8\'</b>. Read'=>'Si vous souhaitez utiliser <b>UTF-8</b>, vous devez créer la base de données avec <b>WITH ENCODING \'UTF8\'</b>. Lire ',
    // Fehler "ist" -> "its"
    'do not forget to configure mysql with <b>lower_case_table_names = 1</b> in /etc/my.cnf. Otherwise ist results in installation error! Read'=>'n\'oubliez pas de configurer mysql avec <b>lower_case_table_names = 1</b> dans /etc/my.cnf. Sinon, cela entraîne une erreur d\'installation ! Lire ',
    'you can improve performance by using <b>MYISAM</b> instead of <b>InnoDB</b> but be aware of losing transactions and foreign keys. Be aware and read'=>'vous pouvez améliorer les performances en utilisant <b>MYISAM</b> au lieu de <b>InnoDB</b> mais soyez conscient de la perte de transactions et de clés étrangères. Soyez conscient et lisez ',
    'If you want to use <b>UTF-8</b> you have to configure mysql with <b>default-character-set=utf8</b>.'=>'Si vous voulez utiliser <b>UTF-8</b> vous devez configurer mysql avec <b>default-character-set=utf8</b>.',
    'mysql documentation'=>'Documentation MySQL',
    'Example'=>'Exemple',
    'You can use <b>pdo_pgsql</b> or <b>pdo_mysql</b> with PDO. For other databases please use <b>ODBC</b>.'=>'Vous pouvez utiliser <b>pdo_pgsql</b> ou <b>pdo_mysql</b> avec PDO. Pour les autres bases de données, veuillez utiliser <b>ODBC</b>.',
    'Check'=>'Vérifier',
    'Settings'=>'Paramètres',
    'Language'=>'Langue',
    'Dateformat'=>'Format de date',
    'Charset'=>'Jeu de caractères',
    'Color Scheme'=>'Schéma de couleur',
    'Company'=>'Entreprise',
    'Install limbas without example values (ready for your application)'=>'Installez LIMBAS sans exemples de valeurs (prêts pour votre application)',
    'No clean install archive found! You must install one of the following example values:'=>'Aucune archive d\'installation propre n\'a été trouvée ! Vous devez installer l\'un des exemples de valeurs suivants :',
    'Optionally select example values to install'=>'Sélectionnez éventuellement des exemples de valeurs à installer',
    'No example install archive found!'=>'Aucun exemple d\'archive d\'installation trouvé!',
    'Next step'=>'L\'étape suivante',
    'Reload'=>'Actualiser',
    'Pre-Installation summary'=>'Résumé de pré-installation',
    'Installation Path'=>'Chemin d\'installation',
    'Database Vendor'=>'Fournisseur de base de données',
    'Database Name'=>'Nom de la base de données',
    'Database User'=>'Utilisateur de la base de données',
    'Database Host'=>'Hôte de la base de données',
    'Database Schema'=>'Schéma de base de données',
    'Database Port'=>'Port de la base de données',
    'Installation package'=>'Forfait d\'installation',
    'Back'=>'Retourner',
    'Install Limbas now!'=>'Installez Limbas maintenant!',
    'If you want extension files for demonstration purposes, you can extract the file'=>'Si vous voulez des fichiers d\'extension à des fins de démonstration, vous pouvez extraire le fichier ',
    'OK' => 'd\'accord',
    'OK, but will be better to change' => 'D\'accord, mais vaudrait mieux changer.',
    'Necessary. You can not continue until this function works!' => 'Nécessaire. Vous ne pouvez pas continuer jusqu\'à ce que cette fonction fonctionne!',
    'Function or tool does not work or exist, you can install later' => 'La fonction ou l\'outil ne fonctionne pas ou n\'existe pas, vous pouvez l\'installer plus tard.',
    'Limbas has been successfully installed' => 'Limbas a été installé avec succès',
    'start Limbas now!' => 'commencez Limbas maintenant!'
];
