<?php

define('_MD_FNMA_NOTREGISTERED',   'noch nicht registriert?  Klicken Sie <a href="register.php">hier</a>.');
define('_MD_FNMA_LOSTPASSWORD',    'Passwort vergessen?');
define('_MD_FNMA_NOPROBLEM',       'Kein Problem. Geben Sie hier Ihre bei der Registrierung benutzte eMail Adresse ein.');
define('_MD_FNMA_YOUREMAIL',       'eMail Adresse: ');
define('_MD_FNMA_SENDPASSWORD',    'Passwort senden');
define('_MD_FNMA_LOGGEDOUT',       'Sie wurden abgemeldet');
define('_MD_FNMA_THANKYOUFORVISIT','Dank für Ihren Besuch!');
define('_MD_FNMA_ERROR_FIELD_EMPTY',  'Benutzername und / oder Passwort Feld leer!');
define('_MD_FNMA_LOGGINGU',        'Danke dass Sie sich einloggen, %s.');
define('_MD_FNMA_NOACTTPADM',      'Der gewählte Benutzer ist deaktiv, oder hat sein Konto noch nicht aktiviert.<br />Bitte kontaktieren Sie den Administrator für nähere Details hierzu');
define('_MD_FNMA_LOGIN', 'Login');
define('_MD_FNMA_USERNAME', 'Benutzername');
define('_MD_FNMA_PASSWORD', 'Passwort');
define('_MD_FNMA_REMEMBERME', 'Logindaten merken');


// regsiter.php
define('_MD_FNMA_REGISTER', 'Account erstellen');
define('_MD_FNMA_PASS_CONFIRM', 'Passwort wiederholen: ');
define('_MD_FNMA_EMAIL', 'eMail Addresse: ');
define('_MD_FNMA_ACCOUNT_TYPE', 'Spielversion: ');
define('_MD_FNMA_WOTLK', 'Wrath of the Lich King');
define('_MD_FNMA_TBC', 'The Burning Crusade');
define('_MD_FNMA_CLASSIC', 'Classic');
define('_MD_FNMA_CAPTCHA_INFO', 'Bitte geben Sie hier die Sicherheitsabfrage ein (6 Zeichen)');
define('_MD_FNMA_HAVE_ACCOUNT', 'Vielleicht bereits registriert?  Klicken Sie <a href="user.php">hier</a> für den Login.');
define('_MD_FNMA_REG_NO_PROBLEM', 'Solltest Du bereits über einen gültigen Account verfügen, melde dich mit deinen Logindaten an.');
define('_MD_FNMA_NOT_REGSITER', 'Das erstellen der Accounts; Das nuzten der Server;<br />sowie alle anderen Dienstleistungen dieser Webseite, sind und bleiben föllig Kostenlos!<br /> Solltet Ihr Werbemails, Werbelink per eMail erhalten, <br />die auf kostenflichtige Seiten Linken, handelt es sich um Fakes :)');

define('_MD_FNMA_REG_SECRET', 'Welches ländliche Getränk entstammt der Milchstrasse?');

define('_MD_FNMA_REG_FAILD', 'Registrierung ist mit Grund: %s fehlgeschlagen!');
define('_MD_FNMA_REG_SUCCESS', 'Dein Konto wurde erfolgreich angelegt.<br /> Du kannst dich nun auf dem Spielserver und auch im<br /><b>fnMangosAdmin</b> anmelden.');
define('_MD_FNMA_UNKNOWN', '[Fehler]10102: Unbekannter Fehler');
define('_MD_FNMA_REG_IP_IS_BANNED', '[Fehler]10101: Dein Spielerkonto ist geblockt.');
define('_MD_FNMA_REG_MAIL_EMPTY', '[Fehler]10201: Es wurde kein eMail-Addresse erkannt.');
define('_MD_FNMA_REG_PASSWD_NOTMATCH', '[Fehler]10203: Die Passwörter sind nicht gleich.');
define('_MD_FNMA_REG_PARAM_USERNAME_EMPTY', '[Fehler]10503: Username in PARAM ist leer oder gleich NULL!');
define('_MD_FNMA_REG_SOME_PARAMS_EMPTY', '[Fehler]10501: PARAM Problem, Wert ist leer oder gleich NULL!');
define('_MD_FNMA_REG_SECRET_WRONG', '[Fehler]10204: Das Sicherheitsparam wurde falsch oder gleich 0 übertragen.');
define('_MD_FNMA_REG_USER_PASSWORD_FAIL', '[Fehler]10205: Passowrt ist leer oder Ungleich.');
define('_MD_FNMA_REG_USERNAME_EXISTS', '[Fehler]10102: Dieser Name ist bereits in der DB vorhanden.');
define('_MD_FNMA_REG_IP_EXISTS', '[Fehler]10103: Diese IP-Adresse ist bereits in der DB vorhanden.');
define('_MD_FNMA_REG_EMAIL_EXISTS', '[Fehler]10104: Diese eMail ist bereits in der DB vorhanden.');

// %s is your site name
define('_MD_FNMA_NEWPWDREQ',       'Neue Passwortanforderung auf %s');
define('_MD_FNMA_YOURACCOUNT',     'Ihr Benutzerkonto auf %s');
define('_MD_FNMA_MAILPWDNG',       'eMail Passwort: Ihre Einstellungen konnten nicht aktualisiert werden! Bitte kontaktieren Sie bitte den Administrator.');

// %s is a username
define('_MD_FNMA_PWDMAILED',       'Passwort für %s versendet.');
define('_MD_FNMA_CONFMAIL',        'BestätigungseMail für %s versendet.');
define('_MD_FNMA_ACTVMAILNG',      'FEHLER beim Versenden der AktivierungseMail an %s');
define('_MD_FNMA_ACTVMAILOK',      'AktivierungseMail an %s verschickt.');

// %s is a username
define('_MD_FNMA_HASJUSTREG',      '%s ist bereits registriert!');
define('_MD_FNMA_INVALIDMAIL',     'FEHLER: Ungültige eMail Adresse');
define('_MD_FNMA_EMAILNOSPACES',   'FEHLER: eMail Adresse darf keine Leerzeichen enthalten.');
define('_MD_FNMA_INVALIDNICKNAME', 'FEHLER: Ungültiger Benutzername');
define('_MD_FNMA_NICKNAMETOOLONG', 'Benutzername ist zu lang. Bitte weniger als %s Zeichen verwenden.');
define('_MD_FNMA_NICKNAMETOOSHORT','Benutzername ist zu kurz. Bitte mehr als %s Zeichen verwenden.');
define('_MD_FNMA_NAMERESERVED',    'FEHLER: Dieser Benutzername ist bereits reserviert.');
define('_MD_FNMA_NICKNAMENOSPACES','Es dürfen keine Leerzeichen im Benutzername sein.');
define('_MD_FNMA_NICKNAMETAKEN',   'FEHLER: Benutzername bereits vergeben.');
define('_MD_FNMA_EMAILTAKEN',      'FEHLER: Die angegebene eMail Adresse ist bereits in unserer Datenbank registriert.');
define('_MD_FNMA_ENTERPWD',        'FEHLER: Sie müssen ein Passwort angeben.');
define('_MD_FNMA_SORRYNOTFOUND',   'Es wurde keine entsprechenden Benutzerinformationen gefunden!');

// userinfo.php
define('_MD_FNMA_SELECTNG',        'Es wurde kein Benutzer ausgewählt, bitte gehen Sie zurück und versuchen es erneut.');
define('_MD_FNMA_ALL_ABOUT', 'Alles über %s anzeigen');
define('_MD_FNMA_AVATAR', 'Avatar');
define('_MD_FNMA_EDIT_PROFILE','Profieldaten bearbeiten');
define('_MD_FNMA_INBOX','Posteingang');
define('_MD_FNMA_DELETE_ACCOUNT','Account löschen');
define('_MD_FNMA_NAME','Account Name: ');
define('_MD_FNMA_EXPENSION','Erweiterung: ');
define('_MD_FNMA_PRIVMSG','Private Nachricht');
define('_MD_FNMA_ACCOUNT_FACTS','Profiledaten:');
define('_MD_FNMA_STATISTIK','Statistiken');
define('_MD_FNMA_LAST_LOGIN','Letzter Besuch am: ');
define('_MD_FNMA_SIGNATURE','Siganture');
define('_MD_FNMA_RANK_LEVEL','Account Level: ');
define('_MD_FNMA_MEMBER_RANK','Account Rank: ');
define('_MD_FNMA_MEMBER_SINCE','Account seit: ');
define('_MD_FNMA_LOCALE','Sprache: ');
define('_MD_FNMA_HOME','Home');
define('_MD_FNMA_ON_REALM','Reg. auf Realm: ');


// edituser.php
define('_MD_FNMA_ALLOW_VIEW_EMAIL', 'erlaube anderen Dir über diese eMail nachrichten zu schicken');
define('_MD_FNMA_ACCOUNT_NAME','Accountname der Webseite');
define('_MD_FNMA_USECOOKIE','Benutzer Cookies');
define('_MD_FNMA_PASSWD_RETYPE','Password wiederholen');
define('_MD_FNMA_MAIL_OK','Möchten Sie, dass GameMaster<br /> oder Moderatoren, Ihnen eMail - <br />Nachrichten senden können?');
define('_MD_FNMA_SAVECHANGES','Änderungen Speichern');
define('_MD_FNMA_PROFILE','Acdcount Verwaltung');
define('_MD_FNMA_NOEDIT_RIGHT','Du hast keine Berechtigung diese Seite zu betreten');
define('_MD_FNMA_PROF_UPDATED','Dein Profil wurde aktualisiert');
define('_MD_FNMA_PASSWDS_NOT_SAME','Passwört sind nicht gleich');
define('_MD_FNMA_PWDTOO_SHORT','Das eingegebene Passwort ist zu kurz');
define('_MD_FNMA_INVALID_MAIL','Ungültige eMail Adresse');
//define('', '');


// member.php
define('_MD_FNMA_WELCOME_MEMBER', '<h1>Willkommen im fnMangosServer Utility!</h1><br /> <br />ihr habt euch erfolgreich angemeldet und befindet euch in eurem Member-Bereich!');
define('', '');
define('', '');
define('', '');
define('', '');
define('', '');
define('', '');
define('', '');
define('', '');
define('', '');
define('', '');
define('', '');
define('', '');
define('', '');
define('', '');
define('', '');

?>