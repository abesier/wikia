<?php

$messages = array();

$messages['en'] = array(
	'cloakcheck' => 'IRC cloak eligibility check',
	'cloakcheck-desc' => 'Provides an all-in-one interface to verify requirements for an IRC cloak',
	'right-cloakcheck' => 'Can check IRC cloak related information on other users',

	#form
	##only seen by flagged users
	'cloakcheck-form-username' => 'Username:',
	'cloakcheck-form-check' => 'Check username',
	##only seen by non-flagged
	'cloakcheck-form-check-self' => 'Check IRC cloak eligibility',

	#process
	'cloakcheck-process-empty' => 'Username must not be empty.',
	'cloakcheck-process-notexist' => 'Username does not exist.',

	'cloakcheck-process-username' => 'Username: $1',

	'cloakcheck-process-accountage-yes' => 'Account is old enough.',
	'cloakcheck-process-accountage-no' => 'Account is too new.',

	'cloakcheck-process-emailconf-yes' => 'E-mail address confirmed.',
	'cloakcheck-process-emailconf-no' => 'E-mail address not confirmed.',

	'cloakcheck-process-edits-yes' => 'User has enough edits.',
	'cloakcheck-process-edits-no' => 'User does not have enough edits.',
);

/** Message documentation (Message documentation)
 * @author Siebrand
 */
$messages['qqq'] = array(
	'cloakcheck' => "An \"''IRC cloak''\" is a technical measure on an IRC network to hide the IP address of a user.",
	'cloakcheck-desc' => "An \"''IRC cloak''\" is a technical measure on an IRC network to hide the IP address of a user.",
	'cloakcheck-form-check-self' => "An \"''IRC cloak''\" is a technical measure on an IRC network to hide the IP address of a user.",
);

/** Arabic (العربية)
 * @author Meno25
 * @author Mutarjem horr
 * @author OsamaK
 */
$messages['ar'] = array(
	'cloakcheck-form-username' => 'اسم المستخدم:',
	'cloakcheck-form-check' => 'تحقق من اسم المستخدم',
	'cloakcheck-process-empty' => 'يجب أن لا يكون إاسم المستخدم فارغًا.',
	'cloakcheck-process-notexist' => 'اسم المستخدم غير موجود.',
	'cloakcheck-process-username' => 'اسم المستخدم: $1',
	'cloakcheck-process-accountage-yes' => 'الحساب قديم كفاية.',
	'cloakcheck-process-accountage-no' => 'الحساب حديث جداّ.',
	'cloakcheck-process-emailconf-yes' => 'عنوان البريد الإلكتروني مؤكد.',
	'cloakcheck-process-emailconf-no' => 'عنوان البريد الإلكتروني غير مؤكد.',
	'cloakcheck-process-edits-yes' => 'لدى المستخدم ما يكفي من التغييرات.',
	'cloakcheck-process-edits-no' => 'ليس لدى المستخدم ما يكفي من التغييرات.',
);

/** Azerbaijani (Azərbaycanca)
 * @author Cekli829
 */
$messages['az'] = array(
	'cloakcheck-form-username' => 'İstifadəçi adı:',
	'cloakcheck-process-username' => 'İstifadəçi adı: $1',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'cloakcheck-form-username' => 'Потребителско име:',
	'cloakcheck-process-empty' => 'Полето за потребителско име не може да бъде празно.',
	'cloakcheck-process-notexist' => 'Това потребителско име не съществува.',
	'cloakcheck-process-username' => 'Потребителско име: $1',
	'cloakcheck-process-emailconf-yes' => 'Адресът за електронна поща е потвърден.',
	'cloakcheck-process-emailconf-no' => 'Адресът за електронна поща не е потвърден.',
	'cloakcheck-process-edits-yes' => 'Потребителят има достатъчно редакции.',
	'cloakcheck-process-edits-no' => 'Потребителят няма достатъчно редакции.',
);

/** Breton (Brezhoneg)
 * @author Y-M D
 */
$messages['br'] = array(
	'cloakcheck-form-username' => 'Anv implijer :',
	'cloakcheck-form-check' => 'Gwiriekaat an anv-implijer',
	'cloakcheck-process-empty' => "Goullo ne c'hall ket bezañ an anv implijer.",
	'cloakcheck-process-notexist' => "N'eus ket eus an anv-implijer.",
	'cloakcheck-process-username' => 'Anv implijer : $1',
	'cloakcheck-process-accountage-yes' => "Kozh a-walc'h eo ar gont-mañ.",
	'cloakcheck-process-accountage-no' => 'Re nevez eo ar gont.',
	'cloakcheck-process-emailconf-yes' => "Kadarnaet eo ar chomlec'h postel.",
	'cloakcheck-process-emailconf-no' => "N'eo ket kadarnaet ar chomlec'h postel.",
	'cloakcheck-process-edits-yes' => "Trawalc'h a zegasadennoù a zo gant an implijer.",
	'cloakcheck-process-edits-no' => "N'eus ket trawalc'h a zegasadennoù gant an implijer-mañ.",
);

/** German (Deutsch)
 * @author Diebuche
 * @author George Animal
 * @author LWChris
 * @author SVG
 */
$messages['de'] = array(
	'cloakcheck' => 'Prüfung der Anspruchsberechtigung auf einen IRC Cloak',
	'cloakcheck-desc' => 'Stellt eine ganzheitliche Schnittstelle zur Verfügung, die der Prüfung der Voraussetzungen für einen IRC Cloak dient',
	'right-cloakcheck' => 'Kann Informationen zum IRC Cloak anderer Benutzer überprüfen',
	'cloakcheck-form-username' => 'Benutzername:',
	'cloakcheck-form-check' => 'Benutzernamen überprüfen',
	'cloakcheck-form-check-self' => 'IRC-Cloak-Berechtigung überprüfen',
	'cloakcheck-process-empty' => 'Bitte einen Benutzernamen angeben.',
	'cloakcheck-process-notexist' => 'Benutzername existiert nicht.',
	'cloakcheck-process-username' => 'Benutzername: $1',
	'cloakcheck-process-accountage-yes' => 'Konto ist alt genug.',
	'cloakcheck-process-accountage-no' => 'Konto ist zu neu.',
	'cloakcheck-process-emailconf-yes' => 'E-Mail-Adresse bestätigt.',
	'cloakcheck-process-emailconf-no' => 'E-Mail-Adresse nicht bestätigt.',
	'cloakcheck-process-edits-yes' => 'Benutzer hat genug Bearbeitungen.',
	'cloakcheck-process-edits-no' => 'Benutzer hat nicht genügend Bearbeitungen.',
);

/** Zazaki (Zazaki)
 * @author Erdemaslancan
 */
$messages['diq'] = array(
	'cloakcheck-form-username' => 'Namey Qarberi',
);

/** Spanish (Español)
 * @author Absay
 * @author Locos epraix
 * @author VegaDark
 */
$messages['es'] = array(
	'cloakcheck' => 'Verificador de elegibilidad de cloak de IRC',
	'cloakcheck-desc' => 'Proporciona una interfaz todo-en-uno y verifica los requisitos para una cloak de IRC',
	'right-cloakcheck' => 'Puede comprobar la información relacionada al cloak de IRC de otros usuarios',
	'cloakcheck-form-username' => 'Nombre de usuario:',
	'cloakcheck-form-check' => 'Verificar el nombre de usuario',
	'cloakcheck-form-check-self' => 'Verificar elegibilidad de cloak de IRC',
	'cloakcheck-process-empty' => 'El nombre de usuario no debe estar vacío.',
	'cloakcheck-process-notexist' => 'El nombre de usuario no existe.',
	'cloakcheck-process-username' => 'Nombre de usuario: $1',
	'cloakcheck-process-accountage-yes' => 'La cuenta tiene la edad suficiente.',
	'cloakcheck-process-accountage-no' => 'La cuenta es demasiado nueva.',
	'cloakcheck-process-emailconf-yes' => 'Dirección de correo electrónico confirmada.',
	'cloakcheck-process-emailconf-no' => 'Dirección de correo electrónico no confirmada.',
	'cloakcheck-process-edits-yes' => 'El usuario tiene suficiente ediciones.',
	'cloakcheck-process-edits-no' => 'El usuario no tiene suficiente ediciones.',
);

/** Basque (Euskara)
 * @author An13sa
 */
$messages['eu'] = array(
	'cloakcheck-form-username' => 'Erabiltzaile izena',
);

/** Persian (فارسی)
 * @author BlueDevil
 * @author Mjbmr
 */
$messages['fa'] = array(
	'cloakcheck' => 'بررسی واجد شرایط بودن پنهان‌ساز آی‌آر‌سی (IRC cloak)',
	'cloakcheck-form-username' => 'نام کاربری:',
	'cloakcheck-form-check' => 'بررسی نام کاربری',
	'cloakcheck-form-check-self' => 'بررسی واجد شرایط بودن پنهان‌ساز آی‌آر‌سی (IRC cloak)',
	'cloakcheck-process-empty' => 'نام کاربری نباید خالی باشد.',
	'cloakcheck-process-notexist' => 'نام کاربری وجود ندارد.',
	'cloakcheck-process-username' => 'نام کاربری: $1',
	'cloakcheck-process-accountage-yes' => 'حساب کاربری به اندازهٔ کافی قدیمی است.',
	'cloakcheck-process-accountage-no' => 'حساب کاربری بیش از حد جدید است.',
	'cloakcheck-process-emailconf-yes' => 'نشانی پست الکترونیکی تایید شد.',
	'cloakcheck-process-emailconf-no' => 'نشانی پست الکترونیکی تایید نشده است.',
	'cloakcheck-process-edits-yes' => 'کاربر به اندازهٔ کافی ویرایش دارد.',
	'cloakcheck-process-edits-no' => 'کاربر به اندازهٔ کافی ویرایش ندارد.',
);

/** Finnish (Suomi)
 * @author Nike
 * @author Tofu II
 */
$messages['fi'] = array(
	'cloakcheck-process-username' => 'Käyttäjätunnus: $1',
	'cloakcheck-process-accountage-yes' => 'Käyttäjätunnus on tarpeeksi vanha.',
	'cloakcheck-process-accountage-no' => 'Käyttäjätunnus on liian uusi.',
	'cloakcheck-process-emailconf-yes' => 'Sähköpostiosoite on vahvistettu.',
	'cloakcheck-process-emailconf-no' => 'Sähköpostiosoitetta ei ole vahvistettu.',
);

/** French (Français)
 * @author Peter17
 * @author Wyz
 */
$messages['fr'] = array(
	'cloakcheck' => 'Vérification de l’éligibilité au cloak IRC',
	'cloakcheck-desc' => 'Fournit une interface intégrée pour vérifier les prérequis nécessaires pour un cloak IRC',
	'right-cloakcheck' => 'peut vérifier les informations en rapport avec le cloak IRC pour d’autres utilisateurs',
	'cloakcheck-form-username' => 'Nom d’utilisateur :',
	'cloakcheck-form-check' => 'Vérifier le nom d’utilisateur',
	'cloakcheck-form-check-self' => 'Vérifier l’éligibilité au cloak IRC',
	'cloakcheck-process-empty' => "Le nom d'utilisateur ne doit pas être vide.",
	'cloakcheck-process-notexist' => 'Le nom d’utilisateur n’existe pas.',
	'cloakcheck-process-username' => 'Nom d’utilisateur : $1',
	'cloakcheck-process-accountage-yes' => 'Le compte est suffisamment ancien.',
	'cloakcheck-process-accountage-no' => 'Le compte est trop récent.',
	'cloakcheck-process-emailconf-yes' => 'L’adresse électronique a été confirmée.',
	'cloakcheck-process-emailconf-no' => 'L’adresse électronique n’a pas été confirmée.',
	'cloakcheck-process-edits-yes' => 'L’utilisateur a suffisamment de modifications.',
	'cloakcheck-process-edits-no' => 'L’utilisateur n’a pas suffisamment de modifications.',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'cloakcheck' => 'Comprobación da elixibilidade do enmascaramento do IRC',
	'cloakcheck-desc' => 'Proporciona unha interface integrada para comprobar os requirimentos do enmascaramento do IRC',
	'right-cloakcheck' => 'pode comprobar a información relacionada co enmascaramento do IRC noutros usuarios',
	'cloakcheck-form-username' => 'Nome de usuario:',
	'cloakcheck-form-check' => 'Comprobar o nome de usuario',
	'cloakcheck-form-check-self' => 'Comprobar a elixibilidade do enmascaramento do IRC',
	'cloakcheck-process-empty' => 'O nome de usuario non pode estar baleiro.',
	'cloakcheck-process-notexist' => 'O usuario non existe.',
	'cloakcheck-process-username' => 'Nome de usuario: $1',
	'cloakcheck-process-accountage-yes' => 'A conta é o suficientemente vella.',
	'cloakcheck-process-accountage-no' => 'A conta é nova de máis.',
	'cloakcheck-process-emailconf-yes' => 'O enderezo de correo electrónico está confirmado.',
	'cloakcheck-process-emailconf-no' => 'O enderezo de correo electrónico non está confirmado.',
	'cloakcheck-process-edits-yes' => 'O usuario ten suficientes edicións.',
	'cloakcheck-process-edits-no' => 'O usuario non ten suficientes edicións.',
);

/** Hungarian (Magyar)
 * @author Dani
 */
$messages['hu'] = array(
	'cloakcheck-form-username' => 'Felhasználónév:',
	'cloakcheck-form-check' => 'Felhasználónév ellenőrzése',
	'cloakcheck-process-username' => 'Felhasználónév: $1',
	'cloakcheck-process-accountage-yes' => 'A fiók elég idős.',
	'cloakcheck-process-accountage-no' => 'A fiók túlságosan új.',
	'cloakcheck-process-emailconf-yes' => 'E-mail cím megerősítve.',
	'cloakcheck-process-emailconf-no' => 'E-mail cím nincs megerősítve.',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'cloakcheck' => 'Verification de eligibilitate pro camouflage IRC',
	'cloakcheck-desc' => 'Forni un interfacie unificate pro verificar le requisitos pro un camouflage in IRC',
	'right-cloakcheck' => 'pote inspectar le information super le mascara IRC ("cloak") de altere usatores',
	'cloakcheck-form-username' => 'Nomine de usator:',
	'cloakcheck-form-check' => 'Verificar nomine de usator',
	'cloakcheck-form-check-self' => 'Verificar eligibilitate pro camouflage IRC',
	'cloakcheck-process-empty' => 'Le nomine de usator non pote esser vacue.',
	'cloakcheck-process-notexist' => 'Le nomine de usator non existe.',
	'cloakcheck-process-username' => 'Nomine de usator: $1',
	'cloakcheck-process-accountage-yes' => 'Le conto es satis vetere.',
	'cloakcheck-process-accountage-no' => 'Le conto es troppo nove.',
	'cloakcheck-process-emailconf-yes' => 'Adresse de e-mail confirmate.',
	'cloakcheck-process-emailconf-no' => 'Adresse de e-mail non confirmate.',
	'cloakcheck-process-edits-yes' => 'Le usator ha facite satis de modificationes.',
	'cloakcheck-process-edits-no' => 'Le usator non ha facite satis de modificationes.',
);

/** Italian (Italiano)
 * @author Lexaeus 94
 * @author Minerva Titani
 */
$messages['it'] = array(
	'cloakcheck-form-username' => 'Nome utente:',
	'cloakcheck-form-check' => 'Verifica utente',
	'cloakcheck-process-accountage-yes' => "L'account è abbastanza vecchio.",
	'cloakcheck-process-emailconf-yes' => 'Indirizzo e-mail confermato.',
	'cloakcheck-process-edits-yes' => "L'utente ha compiuto abbastanza modifiche.",
);

/** Japanese (日本語)
 * @author Tommy6
 */
$messages['ja'] = array(
	'cloakcheck' => 'IRCクローク付与適格性検査',
	'cloakcheck-desc' => 'IRCクロークの付与に必要な要件を満たしているかどうかの検査を一括して行うインタフェースを提供する',
	'right-cloakcheck' => '他の利用者の IRC クロークに関する情報の閲覧',
	'cloakcheck-form-username' => '利用者名：',
	'cloakcheck-form-check' => '利用者名をチェック',
	'cloakcheck-form-check-self' => 'IRCクロークの付与適格性をチェックする',
	'cloakcheck-process-empty' => '利用者名は空欄にできません。',
	'cloakcheck-process-notexist' => '利用者名が存在しません。',
	'cloakcheck-process-username' => '利用者名: $1',
	'cloakcheck-process-accountage-yes' => '十分な時間が経過したアカウントです。',
	'cloakcheck-process-accountage-no' => 'アカウントが新しすぎます。',
	'cloakcheck-process-emailconf-yes' => 'Eメールアドレスが認証されています。',
	'cloakcheck-process-emailconf-no' => 'Eメールアドレスが認証されていません。',
	'cloakcheck-process-edits-yes' => '十分な回数編集しています。',
	'cloakcheck-process-edits-no' => '編集回数が不足しています。',
);

/** Kurdish (Latin script) (‪Kurdî (latînî)‬)
 * @author George Animal
 */
$messages['ku-latn'] = array(
	'cloakcheck-form-username' => 'Navê bikarhêner:',
	'cloakcheck-process-username' => 'Navê bikarhêner: $1',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'cloakcheck-form-username' => 'Benotzernumm:',
	'cloakcheck-form-check' => 'Benotzernumm nokucken',
	'cloakcheck-process-empty' => 'De Benotzernumm däerf net eidel sinn.',
	'cloakcheck-process-username' => 'Benotzernumm: $1',
	'cloakcheck-process-accountage-yes' => 'De Benotzerkont ass al genuch.',
	'cloakcheck-process-accountage-no' => 'De Benotzerkont ass ze nei.',
	'cloakcheck-process-edits-yes' => 'De Benotzer huet genuch Ännerungen.',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'cloakcheck' => 'Проверка на подобност за IRC-маска',
	'cloakcheck-desc' => 'Дава сеопфатен посредник за проверка на задоволување на условите за IRC-маска',
	'right-cloakcheck' => 'може да проверува информации за други корисници што се однесуваат на прикривање од IRC',
	'cloakcheck-form-username' => 'Корисничко име:',
	'cloakcheck-form-check' => 'Проверка на корисничко име',
	'cloakcheck-form-check-self' => 'Проверка на подобност за IRC-маска',
	'cloakcheck-process-empty' => 'Корисничкото име не смее да стои празно.',
	'cloakcheck-process-notexist' => 'Тоа корисничко име не постои.',
	'cloakcheck-process-username' => 'Корисничко име: $1',
	'cloakcheck-process-accountage-yes' => 'Сметката е доволно стара.',
	'cloakcheck-process-accountage-no' => 'Сметката е премногу нова.',
	'cloakcheck-process-emailconf-yes' => 'Е-поштенската адреса е потврдена.',
	'cloakcheck-process-emailconf-no' => 'Е-поштенската адреса не е потврдена.',
	'cloakcheck-process-edits-yes' => 'Корисникот има доволен број уредувања.',
	'cloakcheck-process-edits-no' => 'Корисникот нема доволен број уредувања.',
);

/** Malayalam (മലയാളം)
 * @author Praveenp
 */
$messages['ml'] = array(
	'cloakcheck-form-username' => 'ഉപയോക്തൃനാമം:',
	'cloakcheck-form-check' => 'ഉപയോക്തൃനാമം പരിശോധിക്കുക',
	'cloakcheck-process-empty' => 'ഉപയോക്തൃനാമം ശൂന്യമായിരിക്കാൻ പാടില്ല.',
	'cloakcheck-process-notexist' => 'ഉപയോക്തൃനാമം നിലവിലില്ല.',
	'cloakcheck-process-username' => 'ഉപയോക്തൃനാമം: $1',
	'cloakcheck-process-emailconf-yes' => 'ഇമെയിൽ വിലാസം സ്ഥിരീകരിച്ചിട്ടുണ്ട്.',
	'cloakcheck-process-emailconf-no' => 'ഇമെയിൽ വിലാസം സ്ഥിരീകരിച്ചിട്ടില്ല.',
);

/** Malay (Bahasa Melayu)
 * @author Anakmalaysia
 */
$messages['ms'] = array(
	'cloakcheck' => 'Semak kelayakan samaran IRC',
	'cloakcheck-desc' => 'Menyediakan antara muka semua dalam satu untuk mengesahkan keperluan menerima samaran IRC',
	'right-cloakcheck' => 'boleh menyemak maklumat berkaitan samaran IRC pada pengguna lain',
	'cloakcheck-form-username' => 'Nama pengguna:',
	'cloakcheck-form-check' => 'Semak nama pengguna',
	'cloakcheck-form-check-self' => 'Semak kelayakan menerima samaran IRC',
	'cloakcheck-process-empty' => 'Nama pengguna tidak boleh dibiarkan kosong.',
	'cloakcheck-process-notexist' => 'Nama pengguna tidak wujud.',
	'cloakcheck-process-username' => 'Nama pengguna: $1',
	'cloakcheck-process-accountage-yes' => 'Akaun cukup lama.',
	'cloakcheck-process-accountage-no' => 'Akaun masih baru lagi.',
	'cloakcheck-process-emailconf-yes' => 'Alamat e-mel disahkan.',
	'cloakcheck-process-emailconf-no' => 'Alamat e-mel tidak disahkan.',
	'cloakcheck-process-edits-yes' => 'Jumlah suntingan pengguna memadai.',
	'cloakcheck-process-edits-no' => 'Jumlah suntingan pengguna tidak memadai.',
);

/** Norwegian Bokmål (‪Norsk (bokmål)‬)
 * @author Audun
 * @author Nghtwlkr
 */
$messages['nb'] = array(
	'cloakcheck' => 'Valgbarhetssjekk for IRC-kappe',
	'cloakcheck-desc' => 'Gir et alt-i-ett grensesnitt for å verifisere krav til en IRC-kappe',
	'right-cloakcheck' => 'kan sjekke IRC-cloak-relatert informasjon for andre brukere',
	'cloakcheck-form-username' => 'Brukernavn:',
	'cloakcheck-form-check' => 'Sjekk brukernavn',
	'cloakcheck-form-check-self' => 'Sjekk valgbarhet for IRC-kappe',
	'cloakcheck-process-empty' => 'Brukernavn kan ikke være tomt.',
	'cloakcheck-process-notexist' => 'Brukernavn eksisterer ikke.',
	'cloakcheck-process-username' => 'Brukernavn: $1',
	'cloakcheck-process-accountage-yes' => 'Konto er gammel nok.',
	'cloakcheck-process-accountage-no' => 'Konto er for ny.',
	'cloakcheck-process-emailconf-yes' => 'E-postadresse bekreftet.',
	'cloakcheck-process-emailconf-no' => 'E-postadresse ikke bekreftet.',
	'cloakcheck-process-edits-yes' => 'Bruker har nok redigeringer.',
	'cloakcheck-process-edits-no' => 'Bruker har ikke redigeringer nok.',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'cloakcheck' => 'Controle in aanmerking komen voor IRC cloak',
	'cloakcheck-desc' => 'Biedt een interface voor het controleren op de voorwaarden voor een IRC-cloak',
	'right-cloakcheck' => 'kan IRC-cloak en gerelateerde gegevens van andere gebruikers bekijken',
	'cloakcheck-form-username' => 'Gebruikersnaam:',
	'cloakcheck-form-check' => 'Gebruikersnaam controleren',
	'cloakcheck-form-check-self' => 'Controleren of ik in aanmerking kom voor een IRC-cloak',
	'cloakcheck-process-empty' => 'Gebruikersnaam kan niet leeg zijn.',
	'cloakcheck-process-notexist' => 'De gebruikersnaam bestaat niet.',
	'cloakcheck-process-username' => 'Gebruikersnaam: $1',
	'cloakcheck-process-accountage-yes' => 'De gebruiker bestaat al lang genoeg.',
	'cloakcheck-process-accountage-no' => 'De gebruiker bestaat nog niet lang genoeg.',
	'cloakcheck-process-emailconf-yes' => 'Het e-mailadres is bevestigd.',
	'cloakcheck-process-emailconf-no' => 'Het e-mailadres is niet bevestigd.',
	'cloakcheck-process-edits-yes' => 'De gebruiker heeft voldoende bewerkingen gemaakt.',
	'cloakcheck-process-edits-no' => 'De gebruiker heeft onvoldoende bewerkingen gemaakt.',
);

/** Polish (Polski)
 * @author Sovq
 */
$messages['pl'] = array(
	'cloakcheck' => 'Test dostępności maski IRC',
	'cloakcheck-desc' => 'Udostępnia interfejs do weryfikacji warunków koniecznych do otrzymania maski IRC',
	'right-cloakcheck' => 'Może sprawdzić informacje powiązane z maską IRC dotyczące innych użytkowników',
	'cloakcheck-form-username' => 'Nazwa użytkownika',
	'cloakcheck-form-check' => 'Sprawdź nazwę użytkownika',
	'cloakcheck-form-check-self' => 'Sprawdź dostępność maski IRC',
	'cloakcheck-process-empty' => 'Nazwa użytkownika nie może być pusta.',
	'cloakcheck-process-notexist' => 'Użytkownik o tej nazwie nie istnieje.',
	'cloakcheck-process-username' => 'Nazwa użytkownika: $1',
	'cloakcheck-process-accountage-yes' => 'Konto jest aktywne dostatecznie długo.',
	'cloakcheck-process-accountage-no' => 'Konto nie jest aktywne dostatecznie długo.',
	'cloakcheck-process-emailconf-yes' => 'Adres e-mail potwierdzony.',
	'cloakcheck-process-emailconf-no' => 'Adres e-mail nie potwierdzony.',
	'cloakcheck-process-edits-yes' => 'Użytkownik wykonał wystarczającą liczbę zmian.',
	'cloakcheck-process-edits-no' => 'Użytkownik nie wykonał wystarczającej liczby zmian.',
);

/** Piedmontese (Piemontèis)
 * @author Borichèt
 * @author Dragonòt
 */
$messages['pms'] = array(
	'cloakcheck' => "Contròl d'amissibilità për la covertura IRC",
	'cloakcheck-desc' => "A dà n'antërfacia antëgrà për verifiché j'arceste për na covertura IRC",
	'right-cloakcheck' => "A peul controlé l'anformassion relativa d'un mantel IRC su d'àutri utent",
	'cloakcheck-form-username' => 'Nòm utent:',
	'cloakcheck-form-check' => "Controlé lë stranòm d'utent",
	'cloakcheck-form-check-self' => "Contròla l'amissibilità ëd covertura IRC",
	'cloakcheck-process-empty' => "Lë stranòm d'utent a peul pa esse veuid.",
	'cloakcheck-process-notexist' => "Lë stranòm d'utent a esist pa.",
	'cloakcheck-process-username' => "Stranòm d'utent: $1",
	'cloakcheck-process-accountage-yes' => "Ël cont a l'é vej a basta.",
	'cloakcheck-process-accountage-no' => "Ël cont a l'é tròp neuv.",
	'cloakcheck-process-emailconf-yes' => 'Adrëssa eletrònica confirmà.',
	'cloakcheck-process-emailconf-no' => 'Adrëssa eletrònica nen confirmà.',
	'cloakcheck-process-edits-yes' => "L'utent a l'ha a basta ëd modìfiche.",
	'cloakcheck-process-edits-no' => "L'utent a l'ha pa a basta ëd modìfiche.",
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'cloakcheck-form-username' => 'کارن-نوم:',
	'cloakcheck-process-username' => 'کارن-نوم: $1',
);

/** Portuguese (Português)
 * @author Hamilton Abreu
 * @author Waldir
 */
$messages['pt'] = array(
	'cloakcheck' => 'Verificação de eligibilidade para mascarar o IP no IRC',
	'cloakcheck-desc' => 'Fornece uma interface única de verificação dos requisitos para mascarar o IP no IRC',
	'right-cloakcheck' => 'pode verificar informações sobre outros utilizadores relacionadas com a máscara do IP no IRC',
	'cloakcheck-form-username' => 'Nome de utilizador:',
	'cloakcheck-form-check' => 'Verificar nome de utilizador',
	'cloakcheck-form-check-self' => 'Verificar a eligibilidade para mascarar o IP no IRC',
	'cloakcheck-process-empty' => 'O nome do utilizador não pode estar vazio.',
	'cloakcheck-process-notexist' => 'O nome de utilizador não existe.',
	'cloakcheck-process-username' => 'Nome de utilizador: $1',
	'cloakcheck-process-accountage-yes' => 'A conta é suficientemente antiga.',
	'cloakcheck-process-accountage-no' => 'A conta é demasiado recente.',
	'cloakcheck-process-emailconf-yes' => 'O endereço de correio electrónico foi confirmado.',
	'cloakcheck-process-emailconf-no' => 'O endereço de correio electrónico não foi confirmado.',
	'cloakcheck-process-edits-yes' => 'O utilizador tem edições suficientes.',
	'cloakcheck-process-edits-no' => 'O utilizador não tem edições suficientes.',
);

/** Brazilian Portuguese (Português do Brasil)
 * @author Aristóbulo
 */
$messages['pt-br'] = array(
	'cloakcheck' => 'Verificação de eligibilidade para um cloak IRC',
	'cloakcheck-desc' => 'Fornece uma interface única para verificação dos requisitos de um cloak IRC',
	'cloakcheck-form-username' => 'Nome de usuário:',
	'cloakcheck-form-check' => 'Verificar nome de usuário',
	'cloakcheck-form-check-self' => 'Verificar a eligibilidade para um cloak IRC',
	'cloakcheck-process-empty' => 'O nome do usuário não pode estar vazio.',
	'cloakcheck-process-notexist' => 'O nome de usuário não existe.',
	'cloakcheck-process-username' => 'Nome de usuário: $1',
	'cloakcheck-process-accountage-yes' => 'A conta é suficientemente antiga.',
	'cloakcheck-process-accountage-no' => 'A conta é recente.',
	'cloakcheck-process-emailconf-yes' => 'Endereço de e-mail confirmado.',
	'cloakcheck-process-emailconf-no' => 'Endereço de e-mail não confirmado.',
	'cloakcheck-process-edits-yes' => 'O utilizador tem edições suficientes.',
	'cloakcheck-process-edits-no' => 'O utilizador não tem edições suficientes.',
);

/** Romanian (Română)
 * @author Stelistcristi
 */
$messages['ro'] = array(
	'cloakcheck-form-username' => 'Nume de utilizator:',
	'cloakcheck-form-check' => 'Verifică numele de utilizator',
	'cloakcheck-process-empty' => 'Numele de utilizator trebuie să nu fie gol.',
	'cloakcheck-process-notexist' => 'Numele de utilizator nu există.',
	'cloakcheck-process-username' => 'Numele de utilizator: $1',
	'cloakcheck-process-accountage-yes' => 'Contul este suficient de vechi.',
	'cloakcheck-process-accountage-no' => 'Contul este prea nou.',
	'cloakcheck-process-emailconf-yes' => 'Adresa de e-mail confirmată.',
	'cloakcheck-process-emailconf-no' => 'Adresa de e-mail neconfirmată.',
	'cloakcheck-process-edits-yes' => 'Utilizatorul are suficiente modificări.',
);

/** Russian (Русский)
 * @author Eleferen
 * @author Kuzura
 */
$messages['ru'] = array(
	'cloakcheck-form-username' => 'Имя участника:',
	'cloakcheck-form-check' => 'Проверить имя пользователя',
	'cloakcheck-process-empty' => 'Поле с именем участника не должно быть пустым',
	'cloakcheck-process-notexist' => 'Участника с таким именем не существует.',
	'cloakcheck-process-username' => 'Имя участника: $1',
	'cloakcheck-process-accountage-yes' => 'Учетная запись достаточно старая.',
	'cloakcheck-process-accountage-no' => 'Учетная запись слишком новая.',
	'cloakcheck-process-emailconf-yes' => 'Адрес электронной почты подтверждён.',
	'cloakcheck-process-emailconf-no' => 'Адрес электронной почты не подтверждён.',
	'cloakcheck-process-edits-yes' => 'Участником сделано необходимое число правок.',
	'cloakcheck-process-edits-no' => 'Участником не сделано необходимого числа правок.',
);

/** Serbian (Cyrillic script) (‪Српски (ћирилица)‬)
 * @author Rancher
 */
$messages['sr-ec'] = array(
	'cloakcheck-form-username' => 'Корисничко име:',
	'cloakcheck-form-check' => 'Провери корисничко име',
	'cloakcheck-process-empty' => 'Корисничко име не може остати празно.',
	'cloakcheck-process-notexist' => 'Корисничко име не постоји.',
	'cloakcheck-process-username' => 'Корисничко име: $1',
	'cloakcheck-process-accountage-yes' => 'Налог је довољно стар.',
	'cloakcheck-process-accountage-no' => 'Налог је превише нов.',
	'cloakcheck-process-emailconf-yes' => 'Е-адреса је потврђена.',
	'cloakcheck-process-emailconf-no' => 'Е-адреса није потврђена.',
	'cloakcheck-process-edits-yes' => 'Корисник има довољно измена.',
	'cloakcheck-process-edits-no' => 'Корисник нема довољно измена.',
);

/** Swedish (Svenska)
 * @author Tobulos1
 */
$messages['sv'] = array(
	'cloakcheck-form-username' => 'Användarnamn:',
	'cloakcheck-form-check' => 'Kontrollera användarnamn',
	'cloakcheck-process-empty' => 'Användarnamnet får inte vara tomt.',
	'cloakcheck-process-notexist' => 'Användarnamnet finns inte.',
	'cloakcheck-process-username' => 'Användarnamn: $1',
	'cloakcheck-process-accountage-yes' => 'Kontot är gammal nog.',
	'cloakcheck-process-accountage-no' => 'Kontot är för nytt.',
	'cloakcheck-process-emailconf-yes' => 'E-postadressen bekräftades.',
	'cloakcheck-process-emailconf-no' => 'E-postadressen bekräftades inte.',
	'cloakcheck-process-edits-yes' => 'Användaren har gjort nog med redigeringar.',
	'cloakcheck-process-edits-no' => 'Användaren har inte gjort nog med redigeringar.',
);

/** Tamil (தமிழ்)
 * @author TRYPPN
 */
$messages['ta'] = array(
	'cloakcheck-form-username' => 'பயனர் பெயர்:',
	'cloakcheck-form-check' => 'பயனர் பெயரை சரிபார்க்கவும்',
	'cloakcheck-process-username' => 'பயனர் பெயர்: $1',
	'cloakcheck-process-accountage-no' => 'கணக்கு மிகவும் புதியது.',
);

/** Telugu (తెలుగు)
 * @author Praveen Illa
 */
$messages['te'] = array(
	'cloakcheck-form-username' => 'వాడుకరిపేరు:',
	'cloakcheck-process-username' => 'వాడుకరిపేరు: $1',
);

/** Tagalog (Tagalog)
 * @author AnakngAraw
 */
$messages['tl'] = array(
	'cloakcheck' => 'Pagsusuri ng pagkamaaari ng balabal na IRC',
	'cloakcheck-desc' => 'Nagbibigay ng lahat-nasa-isang ugnayang-mukha upang matiyak ang mga kailangan para sa isang balabal na IRC',
	'cloakcheck-form-username' => 'Pangalan ng tagagamit:',
	'cloakcheck-form-check' => 'Suriin ang pangalan ng tagagamit',
	'cloakcheck-form-check-self' => 'Suriin ang pagkamaaari ng balabal na IRC',
	'cloakcheck-process-empty' => 'Hindi dapat walang laman ang pangalan ng tagagamit.',
	'cloakcheck-process-notexist' => 'Hindi umiiral ang pangalan ng tagagamit.',
	'cloakcheck-process-username' => 'Pangalan ng tagagamit: $1',
	'cloakcheck-process-accountage-yes' => 'Nasa sapat na gulang na ang akawnt.',
	'cloakcheck-process-accountage-no' => 'Napakabago pa ng akawnt.',
	'cloakcheck-process-emailconf-yes' => 'Natiyak ang tirahan ng e-liham.',
	'cloakcheck-process-emailconf-no' => 'Hindi natiyak ang tirahan ng e-liham.',
	'cloakcheck-process-edits-yes' => 'May sapat na pamamatnugot ang tagagamit.',
	'cloakcheck-process-edits-no' => 'Walang sapat na mga pamamatnugot ang tagagamit.',
);

/** Turkish (Türkçe)
 * @author Suelnur
 */
$messages['tr'] = array(
	'cloakcheck-form-username' => 'Kullanıcı adı:',
	'cloakcheck-process-username' => 'Kullanıcı adı: $1',
);

/** Tatar (Cyrillic script) (Татарча)
 * @author Ajdar
 */
$messages['tt-cyrl'] = array(
	'cloakcheck-form-username' => 'Кулланучы исеме',
	'cloakcheck-form-check' => 'Кулланучы исемен тикшерергә',
	'cloakcheck-process-empty' => 'Кулланучы исеме буш кала алмый.',
	'cloakcheck-process-notexist' => 'Мондый исемле кулланучы юк.',
	'cloakcheck-process-username' => 'Кулланучы исеме: $1',
	'cloakcheck-process-accountage-yes' => 'Хисап язмасы бик иске.',
	'cloakcheck-process-accountage-no' => 'Хисап язмасы артык яңа.',
	'cloakcheck-process-emailconf-yes' => 'Электрон почта адресы расланган.',
	'cloakcheck-process-emailconf-no' => 'Элеткрон почта адресы расланмаган.',
	'cloakcheck-process-edits-yes' => 'Кулланучы тарафыннан тиешле күләмдә төзәтмәләр эшләнгән.',
	'cloakcheck-process-edits-no' => 'Кулланучы тарафыннан тиешле күләмдә төзәтмәләр эшләнмәгән.',
);

/** Simplified Chinese (‪中文(简体)‬)
 * @author Dimension
 * @author Hydra
 */
$messages['zh-hans'] = array(
	'cloakcheck-form-username' => '用户名：',
	'cloakcheck-form-check' => '检查用户名',
	'cloakcheck-process-empty' => '用户名不能为空。',
	'cloakcheck-process-notexist' => '用户名不存在',
	'cloakcheck-process-username' => '用户名：$1',
	'cloakcheck-process-accountage-yes' => '用户够旧',
	'cloakcheck-process-accountage-no' => '用户太新',
	'cloakcheck-process-emailconf-yes' => '电子邮件地址已确认。',
	'cloakcheck-process-edits-no' => '用户编辑数不足。',
);

