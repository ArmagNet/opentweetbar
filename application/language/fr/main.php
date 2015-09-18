<?php /*
	Copyright 2014-2015 Cédric Levieux, Jérémy Collot, ArmagNet

	This file is part of OpenTweetBar.

    OpenTweetBar is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    OpenTweetBar is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with OpenTweetBar.  If not, see <http://www.gnu.org/licenses/>.
*/

$lang["date_format"] = "d/m/Y";
$lang["time_format"] = "H:i";
$lang["datetime_format"] = "le {date} à {time}";

$lang["common_validate"] = "Valider";
$lang["common_delete"] = "Supprimer";

$lang["language_fr"] = "Français";
$lang["language_en"] = "Anglais";
$lang["language_de"] = "Allemand";

$lang["opentweetbar_title"] = "OpenTweetBar - Le tweet communautaire";

$lang["menu_language"] = "Langue : {language}";
$lang["menu_tweet"] = "Tweet";
$lang["menu_history"] = "Historique";
$lang["menu_validation"] = "Validation";
$lang["menu_timelines"] = "Timelines";
$lang["menu_mytweets"] = "Mes tweets";
$lang["menu_myrights"] = "Mes droits";
$lang["menu_mypreferences"] = "Mes préférences";
$lang["menu_myaccounts"] = "Mes comptes";
$lang["menu_logout"] = "Se déconnecter";

$lang["login_title"] = "Identifiez vous";
$lang["login_loginInput"] = "Identifiant";
$lang["login_passwordInput"] = "Mot de passe";
$lang["login_button"] = "Me connecter";
$lang["login_rememberMe"] = "Se souvenir de moi";
$lang["register_link"] = "ou m'enregistrer";
$lang["forgotten_link"] = "j'ai oublié mon mot de passe";

$lang["breadcrumb_index"] = "Accueil";
$lang["breadcrumb_validation"] = "Validation";
$lang["breadcrumb_seeTweetValidation"] = "Validation en cours";
$lang["breadcrumb_history"] = "Historique";
$lang["breadcrumb_timelines"] = "Timelines";
$lang["breadcrumb_mypreferences"] = "Mes préférences";
$lang["breadcrumb_myaccounts"] = "Mes comptes";
$lang["breadcrumb_myrights"] = "Mes droits";
$lang["breadcrumb_mypage"] = "Ma page";
$lang["breadcrumb_register"] = "Enregistrement";
$lang["breadcrumb_activation"] = "Activation";
$lang["breadcrumb_forgotten"] = "J'ai oublié mon mot de passe";
$lang["breadcrumb_about"] = "À Propos";

$lang["index_guide"] = "OpenTweetBar est une application vous permettant de partager un compte tweeter avec un groupe d'utilisateurs. Les tweets peuvent ou doivent être validés
par d'autres utilisateurs avant d'être publiés.";
$lang["index_accounts"] = "Comptes";
$lang["index_tweetPlaceholder"] = "tweet...";
$lang["index_tweetButton"] = "Tweeter";
$lang["index_options_mediaInput"] = "Media";
$lang["index_options_cronDateInput"] = "Départ différé";
$lang["index_options_cronDatePlaceholder"] = "aaaa-mm-jj hh:mm";
$lang["index_options_cronDateGuide"] = "Laisser vide si départ juste après validation";
$lang["index_options_validationDurationInput"] = "Durée de validation maximale";
$lang["index_options_secondaryAccounts"] = "Envoyer aussi pour";
// $lang["index_options_validationDurationPlaceholder"] = "yyyy-mm-dd hh:mm";
// $lang["index_options_validationDurationGuide"] = "Laisser vide si départ juste après validation";
$lang["anonymous_form_nicknameInput"] = "Surnom";
$lang["anonymous_form_mailInput"] = "Adresse mail (pour suivi)";
$lang["anonymous_form_passwordInput"] = "Mot de passe";
$lang["anonymous_form_iamabot"] = "Je suis un robot et je ne sais pas décocher une case";
$lang["anonymous_form_legend"] = "Informations";

$lang["add_tweet_mail_subject"] = "[OTB] Validation d'un tweet demandé";
$lang["add_tweet_mail_content"] = "Bonjour {login},

Vous êtes dans une liste de validateurs du compte {account}, et, un tweet vous attend sur OpenTweetBar dont voici le contenu :

{tweet}

Vous pouvez directement valider ce tweet en cliquant sur le lien ci-dessous :
{validationLink}

L'équipe #OpenTweetBar";
$lang["add_tweet_mail_only_a_retweet"] = "Proposition d'un retweet de :";

$lang["history_guide"] = "Liste des tweets qui ont été validés.";
$lang["history_button_validators"] = "Validateurs";
$lang["history_account_title"] = "Historique des Tweets <strong><em>{account}</em></strong>";
$lang["history_cron_datetime_format"] = "Ne sera pas émis avant le {date} à {time}";
$lang["history_retweet_proposition"] = "Ceci est une proposition de retweet de :";

$lang["validation_guide"] = "Liste des tweets en attente de validation.";
$lang["validation_account_title"] = "Tweets <strong><em>{account}</em></strong> en attente";
$lang["validation_anonymous"] = "(anonyme)";
$lang["validation_tooltip_author_validation"] = "Validation de l'auteur";
$lang["validation_tooltip_mine_validation"] = "Ma validation";
$lang["validation_tooltip_other_validation"] = "Validation des autres utilisateurs";
$lang["validation_cron_datetime_format"] = "Ne sera pas émis avant le {date} à {time}";
$lang["validation_duration_remaining"] = "Temps restant avant expiration : {duration}";
$lang["validation_retweet_proposition"] = "Ceci est une proposition de retweet de :";

$lang["do_validation_error"] = "Votre validation a échoué (déjà effectuée, tweet déjà envoyé ou effacé)";
$lang["do_validation_ok"] = "Votre validation a bien été prise en compte";

$lang["timelines_guide"] = "Vos différentes timelines";
$lang["timelines_account_title"] = "Tweets de <strong><em>{account}</em></strong>";
$lang["timelines_search_header"] = "Recherche de tweet";
$lang["timelines_search_label"] = "Tweet";
$lang["timelines_search_placeholder"] = "Identifiant du tweet ou son url";
$lang["timelines_waiting_tweets"] = "Voir \${numberOfTweets} nouveaux Tweets";
$lang["timelines_waiting_tweet"] = "Voir 1 nouveau Tweet";
$lang["property_retweet_by"] = "RT par \${tweet_user_name} @\${tweet_user_screen_name}";

$lang["mypreferences_guide"] = "Changer mes préférences.";
$lang["mypreferences_form_legend"] = "Configuration de vos accès";
$lang["mypreferences_form_passwordInput"] = "Mot de passe";
$lang["mypreferences_form_passwordPlaceholder"] = "le mot de passe de connexion";
$lang["mypreferences_form_languageInput"] = "Langage";
$lang["mypreferences_form_notificationInput"] = "Notification pour validation";
$lang["mypreferences_form_notification_none"] = "Aucune";
$lang["mypreferences_form_notification_mail"] = "Par mail";
$lang["mypreferences_form_notification_simpledm"] = "Par simple DM";
$lang["mypreferences_form_notification_dm"] = "DM multiple";
$lang["mypreferences_validation_mail_empty"] = "Le champ mail ne peut être vide";
$lang["mypreferences_validation_mail_not_valid"] = "Cette adresse mail n'est pas une adresse valide";
$lang["mypreferences_validation_mail_already_taken"] = "Cette adresse mail est déjà prise";
$lang["mypreferences_form_mailInput"] = "Adresse mail";
$lang["mypreferences_save"] = "Sauver mes préférences";

$lang["myaccounts_guide"] = "Paramétrer mes comptes.";
$lang["myaccounts_newaccount_form_legend"] = "Configuration d'un nouveau compte";
$lang["myaccounts_existingaccount_form_legend"] = "Configuration du compte <em>{account}</em>";
$lang["myaccounts_account_form_nameInput"] = "Nom du compte";
$lang["myaccounts_account_form_anonymousPermitted"] = "Proposition anonyme de tweet autorisée";
$lang["myaccounts_account_form_anonymousPasswordInput"] = "Mot de passe pour les anonymes";
$lang["myaccounts_account_form_validationScoreInput"] = "Score de validation d'un tweet";
$lang["myaccounts_twitter_form_legend"] = "Configuration Twitter";
$lang["myaccounts_twitter_form_apiKeyInput"] = "API Key";
$lang["myaccounts_twitter_form_apiSecretInput"] = "API Secret";
$lang["myaccounts_twitter_form_accessTokenInput"] = "Access Token";
$lang["myaccounts_twitter_form_accessTokenSecretInput"] = "Access Token Secret";
$lang["myaccounts_administrators_form_legend"] = "Gestion des administrateurs";
$lang["myaccounts_administrators_form_addUserInput"] = "Utilisateur";
$lang["myaccounts_validators_form_legend"] = "Gestion des validateurs";
$lang["myaccounts_validators_form_groupNameInput"] = "Nom du groupe";
$lang["myaccounts_validators_form_groupScoreInput"] = "Score";
$lang["myaccounts_validators_form_addUserInput"] = "Utilisateur";
$lang["myaccounts_validators_form_deleteGroupInput"] = "Supprimer groupe";
$lang["myaccounts_validators_form_addGroupInput"] = "Ajouter groupe";
$lang["myaccount_button_testTwitter"] = "Tester";
$lang["myaccount_add"] = "Ajouter ce compte";
$lang["myaccount_save"] = "Sauver les paramètres";

$lang["myrights_guide"] = "Une revue de vos droits.";
$lang["myrights_scores_legend"] = "Mes validations possibles";
$lang["myrights_scores_no_score"] = "Vous n'avez aucun pouvoir de validation";
$lang["myrights_scores_my_score"] = "Votre pouvoir de validation";
$lang["myrights_scores_validation_score"] = "Les points requis pour valider";
$lang["myrights_administration_legend"] = "Mes comptes administrés";
$lang["myrights_scores_no_adminstation"] = "Vous n'avez aucun droit d'administration";

$lang["mypage_guide"] = "Ceci est une page compilant vos statistiques";
$lang["mypage_tweets_legend"] = "Mes tweets";
$lang["mypage_validations_legend"] = "Mes validations";
$lang["mypage_scores_legend"] = "Mes scores";
$lang["mypage_tweet_and_validations_chart_legend"] = "Mes tweets et validations dans le temps";
$lang["mypage_tweet_and_validations_chart_axisY"] = "Quantité";
$lang["mypage_score_chart_axisY"] = "Score";
$lang["mypage_tweet_and_validations_chart_axisX"] = "Date";
$lang["mypage_tweet_and_validations_chart_formatDate"] = "DD/MM/YYYY";
$lang["mypage_tweet_and_validations_chart_jsFormatDate"] = "(date.getDate() < 10 ? '0' : '') + date.getDate() + '/' + (date.getMonth() < 11 ? '0' : '') + (date.getMonth() + 1) + '/' + date.getFullYear()";

$lang["property_tweet"] = "Tweet";
$lang["property_author"] = "Auteur";
$lang["property_date"] = "Date";
$lang["property_validators"] = "Validateurs";
$lang["property_validation"] = "Validation";
$lang["property_actions"] = "Actions";

$lang["register_guide"] = "Bienvenue sur la page d'enregistrement d'OpenTweetBar";
$lang["register_form_legend"] = "Configuration de votre accès";
$lang["register_form_loginInput"] = "Identifiant";
$lang["register_form_loginHelp"] = "Utilisez de préférence votre identifiant Twitter si vous voulez recevoir des notifications sur Twitter";
$lang["register_form_mailInput"] = "Adresse mail";
$lang["register_form_passwordInput"] = "Mot de passe";
$lang["register_form_passwordHelp"] = "Votre mot de passe ne doit pas forcement contenir de caractères bizarres, mais doit de préférence être long et mémorisable";
$lang["register_form_confirmationInput"] = "Confirmation du mot de passe";
$lang["register_form_languageInput"] = "Langage";
$lang["register_form_iamabot"] = "Je suis un robot et je ne sais pas décocher une case";
$lang["register_form_notificationInput"] = "Notification pour validation";
$lang["register_form_notification_none"] = "Aucune";
$lang["register_form_notification_mail"] = "Par mail";
$lang["register_form_notification_simpledm"] = "Par simple DM";
$lang["register_form_notification_dm"] = "DM multiple";
$lang["register_success_title"] = "Enregistrement réussi";
$lang["register_success_information"] = "Votre enregistrement a réussi.
<br>Vous allez recevoir un mail avec un lien à cliquer permettant l'activation de votre compte.";
$lang["register_mail_subject"] = "[OTB] Mail d'enregistrement";
$lang["register_mail_content"] = "Bonjour {login},

Il semblerait que vous vous soyez enregistré sur OpenTweetBar. Pour confirmer votre enregistrement, veuillez cliquer sur le lien ci-dessous :
{activationUrl}

L'équipe #OpenTweetBar";
$lang["register_save"] = "S'enregistrer";
$lang["register_validation_user_empty"] = "Le champ utilisateur ne peut être vide";
$lang["register_validation_user_already_taken"] = "Cet utilisateur est déjà pris";
$lang["register_validation_mail_empty"] = "Le champ mail ne peut être vide";
$lang["register_validation_mail_not_valid"] = "Cette adresse mail n'est pas une adresse valide";
$lang["register_validation_mail_already_taken"] = "Cette adresse mail est déjà prise";
$lang["register_validation_password_empty"] = "Le champ mot de passe ne peut être vide";

$lang["activation_guide"] = "Bienvenue sur l'écran d'activation de votre compte";
$lang["activation_title"] = "Statut de votre activation";
$lang["activation_information_success"] = "L'activation de votre compte utilisateur a réussi. Vous pouvez maintenant vous <a id=\"connectButton\" href=\"#\">identifier</a>.";
$lang["activation_information_danger"] = "L'activation de votre compte utilisateur a échoué.";

$lang["forgotten_guide"] = "Vous avez oublié votre mot de passe, bienvenue sur la page qui vour permettra de récuperer un accès";
$lang["forgotten_form_legend"] = "Récupération d'accès";
$lang["forgotten_form_mailInput"] = "Adresse mail";
$lang["forgotten_save"] = "Envoyez moi un mail !";
$lang["forgotten_success_title"] = "Récupération en cours";
$lang["forgotten_success_information"] = "Un mail vous a été envoyé.<br>Ce mail contient un nouveau mot de passe. Veillez à le changer aussitôt que possible.";
$lang["forgotten_mail_subject"] = "[OTB] J'ai oublié mon mot de passe";
$lang["forgotten_mail_content"] = "Bonjour,

Il semblerait que vous ayez oublié votre mot de passe sur OpenTweetBar. Votre nouveau mot de passe est {password} .
Veuillez le changer aussitôt que vous serez connecté.

L'équipe #OpenTweetBar";

$lang["okTweet"] = "Votre tweet est parti en validation";
$lang["koTweet"] = "Problème de traitement dans votre tweet";
$lang["okDeleteTweet"] = "Votre tweet a été supprimé";
$lang["okValidateTweet"] = "Votre validation du tweet a été prise en compte";
$lang["okFinalValidateTweet"] = "Votre validation du tweet a été prise en compte, et le tweet a été complètement validé";
$lang["error_cant_change_password"] = "Le changement de mot de passe a échoué";
$lang["ok_operation_success"] = "Opération réussie";
$lang["error_passwords_not_equal"] = "Votre mot de passe et sa confirmation sont différents";
$lang["error_cant_send_mail"] = "OpenTweetBar n'arrive pas à envoyer de mail à votre adresse mail";
$lang["error_cant_register"] = "OpenTweetBar n'arrive pas à traiter votre enregistrement";
$lang["error_cant_delete_files"] = "OpenTweetBar n'arrive pas à supprimer les fichiers d'installation";
$lang["error_cant_connect"] = "Impossible de se connecter à la base de données";
$lang["error_database_already_exists"] = "La base de données existe déjà";
$lang["error_database_dont_exist"] = "La base de données n'existe pas";
$lang["error_login_ban"] = "Votre IP a été bloquée pour 10mn.";
$lang["error_login_bad"] = "Vérifier vos identifiants, l'identification a échouée.";
$lang["ok_twitter_success"] = "La configuration Twitter fonctionne";
$lang["error_twitter_cant_authenticate"] = "La configuration Twitter ne fonctionne pas, vérifiez les différents paramètres de connexion";

$lang["install_guide"] = "Bienvenue sur la page d'installation d'OpenTweetBar.";
$lang["install_tabs_database"] = "Base de données";
$lang["install_tabs_mail"] = "Mail";
$lang["install_tabs_application"] = "Application";
$lang["install_tabs_final"] = "Finalisation";
$lang["install_tabs_license"] = "Licence";
$lang["install_database_form_legend"] = "Configuration des accès base de données";
$lang["install_database_hostInput"] = "Hôte";
$lang["install_database_hostPlaceholder"] = "l'adresse du serveur de base de données";
$lang["install_database_portInput"] = "Port";
$lang["install_database_portPlaceholder"] = "le port du serveur de base de données";
$lang["install_database_loginInput"] = "Identifiant";
$lang["install_database_loginPlaceholder"] = "l'identifiant de connexion";
$lang["install_database_loginHelp"] = "On évite l'utilisateur <em>root</em>";
$lang["install_database_passwordInput"] = "Mot de passe";
$lang["install_database_passwordPlaceholder"] = "le mot de passe de connexion";
$lang["install_database_databaseInput"] = "Base de données";
$lang["install_database_databasePlaceholder"] = "nom de la base de données";
$lang["install_database_operations"] = "Opérations";
$lang["install_database_saveButton"] = "Sauver la configuration";
$lang["install_database_pingButton"] = "Ping";
$lang["install_database_createButton"] = "Créer";
$lang["install_database_deployButton"] = "Déployer";
$lang["install_mail_form_legend"] = "Configuration des accès mail";
$lang["install_mail_hostInput"] = "Hôte";
$lang["install_mail_hostPlaceholder"] = "l'adresse du serveur de mail";
$lang["install_mail_portInput"] = "Port";
$lang["install_mail_portPlaceholder"] = "le port du serveur de mail";
$lang["install_mail_usernameInput"] = "Nom Utilisateur";
$lang["install_mail_usernamePlaceholder"] = "l'identifiant de connexion";
$lang["install_mail_passwordInput"] = "Mot de passe";
$lang["install_mail_passwordPlaceholder"] = "le mot de passe de connexion";
$lang["install_mail_fromMailInput"] = "Adresse émettrice";
$lang["install_mail_fromMailPlaceholder"] = "l'adresse d'émission";
$lang["install_mail_fromNameInput"] = "Nom émetteur";
$lang["install_mail_fromNamePlaceholder"] = "le nom de l'émetteur";
$lang["install_mail_testMailInput"] = "Adresse de test";
$lang["install_mail_testMailPlaceholder"] = "non sauvegardée";
$lang["install_mail_operation"] = "Opérations";
$lang["install_mail_saveButton"] = "Sauver la configuration";
$lang["install_mail_pingButton"] = "Ping";
$lang["install_application_form_legend"] = "Configuration de l'application";
$lang["install_application_baseUrlInput"] = "Url de base de l'application";
$lang["install_application_cronEnabledInput"] = "Autoriser l'envoi de tweet de manière différée";
$lang["install_application_cronEnabledHelp"] = "Veuillez rajouter dans votre table cron la commande <pre>* * * * * cd {path} && php do_cron.php</pre>";
$lang["install_application_saltInput"] = "Sel";
$lang["install_application_saltPlaceholder"] = "sel de l'application pour chiffrement et hachage";
$lang["install_application_defaultLanguageInput"] = "Langue par défaut";
$lang["install_application_operation"] = "Opérations";
$lang["install_application_saveButton"] = "Sauver la configuration";
$lang["install_autodestruct_guide"] = "Vous avez tout testé, tout configuré ? Alors un clic sur <em>autodestruction</em> pour supprimer cet installateur.";
$lang["install_autodestruct"] = "Autodestruction";

$lang["about_footer"] = "À Propos";
$lang["opentweetbar_footer"] = "<a href=\"https://www.opentweetbar.net/\" target=\"_blank\">OpenTweetBar</a> est une application fournie par <a href=\"https://www.armagnet.fr\" target=\"_blank\">ArmagNet</a>";
?>