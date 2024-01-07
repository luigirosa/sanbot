<?php
/**
 *
 * Santo bot
 *
 * Santo bot is free software: se funziona, funziona, altrimenti PECCATO!
 *
 */

// leggo i dati di configurazione da un file INI
$aSetup = parse_ini_file('sanbot.ini', true);

// leggo il json del santo del giorno e lo ficco in un array
// uso curl al posto di file_get_content() per maggior configurabilita'
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // cosi' va anche se gli scade il cert
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $aSetup['Santi']['JSONurl']);
$CurlResult = curl_exec($ch);
curl_close($ch);
$aSanti = json_decode($CurlResult, true);
// metto subito tutti i santi in una bella stringa 
$TuttiSanti = '';
// e in un array per la selezione casuale
$aSantoSingolo = array();
foreach($aSanti as $aSanto) {
        $TuttiSanti .= "$aSanto[nome] " . lcfirst($aSanto['tipologia']) . "\n";
        $aSantoSingolo[] = "$aSanto[nome] " . lcfirst($aSanto['tipologia']);
}
// array per la Santa Password
$aSanPass = array();
$tok = strtok($TuttiSanti, " ()\r\n',.");
while ($tok !== false) {
        $b = $tok;
        if (ctype_upper($b[0])) {
                if (strlen($b) > 3) {
                        if ('San' != substr($b, 0, 3)) {
                        	if ('Beat' != substr($b, 0, 4)) {
                                	$aSanPass[] = $b;
				}
                                
                        }
                }
        }
    $tok = strtok(" ()\r\n',.");
}
$aSanPass = array_unique($aSanPass, SORT_STRING);
$aSanPassOut = array();
for ($i = 1; $i < mt_rand(3, 5); $i++ ) $aSanPassOut[] = PassGetWord($aSanPass) . mt_rand(10, 99);

// se mi hanno chiamato per annunciare i santi, procedo e poi esco
if (isset($argv[1]) and 'annuncio' == $argv[1]) {
        $santibuffer = "I santi da invocare oggi:\n" . $TuttiSanti . "\nLa Santa Password del giorno: " . implode('-', $aSanPassOut) ."\nPer i comandi disponibili /help";
        file_get_contents($aSetup['Telegram']['APIurl'] . "/sendmessage?chat_id=" . $aSetup['Telegram']['ChatID'] ."&text=" . urlencode($santibuffer));        
        exit();
}

// mi hanno invocato
$aUpdate = json_decode(file_get_contents("php://input"), TRUE);
file_put_contents('update', print_r($aUpdate, true));   // per debug, ma lo lascio, che non si sa mai
$comando = str_ireplace('@' . $aSetup['Telegram']['BotUser'], '', $aUpdate['message']['text']);
// aiuto
if ('/help' == $comando) {
        $santibuffer = "/mannaggia mannaggia ad un santo del giorno\n/mannaggiatutti mannaggia a tutti i santi del giorno\n/password generatore di password\n/santapassword la santa password del giorno\n/dottore la password del Dottore";
        //...e pubblico
        file_get_contents($aSetup['Telegram']['APIurl'] . "/sendmessage?chat_id=" . $aUpdate['message']['chat']['id'] ."&text=" . urlencode($santibuffer));        
}
// invoco un solo santo
if ('/mannaggia' == $comando) {
        shuffle($aSantoSingolo);
        $santibuffer = "Mannaggia a " . $aSantoSingolo[0];
        //...e pubblico
        file_get_contents($aSetup['Telegram']['APIurl'] . "/sendmessage?chat_id=" . $aUpdate['message']['chat']['id'] ."&text=" . urlencode($santibuffer));        
}
// invoco tutti i santi
if ('/mannaggiatutti' == $comando) {
        $santibuffer = "Mannaggia a:\n" . $TuttiSanti;
        //...e pubblico
        file_get_contents($aSetup['Telegram']['APIurl'] . "/sendmessage?chat_id=" . $aUpdate['message']['chat']['id'] ."&text=" . urlencode($santibuffer));        
}

// la password del Dottore
if ('/dottore' == $comando) {
        $santibuffer = "Westerly Pelican Dreams Tornado Clifftops Andante Grief Fingerprint Susurration Sparrow Dance Mexico Binary Binary Binary";
        //...e pubblico
        file_get_contents($aSetup['Telegram']['APIurl'] . "/sendmessage?chat_id=" . $aUpdate['message']['chat']['id'] ."&text=" . urlencode($santibuffer));        
}

// generatore di password
if ('/password' == $comando) {
        $aDictionary = file('dictionary.txt');
        $aOut = Array();
        for ($i = 1; $i < mt_rand(5, 8); $i++ ) $aOut[] = PassGetWord($aDictionary) . mt_rand(10, 99);
        $santibuffer =  implode('-', $aOut);
        //...e pubblico
        file_get_contents($aSetup['Telegram']['APIurl'] . "/sendmessage?chat_id=" . $aUpdate['message']['chat']['id'] ."&text=" . urlencode($santibuffer));        
}

// generatore di password sante
if ('/santapassword' == $comando) {
        $aOut = Array();
        for ($i = 1; $i < mt_rand(4, 5); $i++ ) $aOut[] = PassGetWord($aSanPass) . mt_rand(10, 99);
        $santibuffer =  implode('-', $aOut);
        //...e pubblico
        file_get_contents($aSetup['Telegram']['APIurl'] . "/sendmessage?chat_id=" . $aUpdate['message']['chat']['id'] ."&text=" . urlencode($santibuffer));        
}

function PassGetWord($dict) {
	shuffle($dict);
	return ucfirst(trim($dict[0]));
}

### END OF FILE ###
