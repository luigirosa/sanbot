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

// se mi hanno chiamato per annunciare i santi, procedo e poi esco
if (isset($argv[1]) and 'annuncio' == $argv[1]) {
        $santibuffer = "I santi da invocare oggi:\n" . $TuttiSanti . "E puoi sempre richiamarli con /mannaggiatutti oppure solamente uno a caso con /mannaggia";
        file_get_contents($aSetup['Telegram']['APIurl'] . "/sendmessage?chat_id=" . $aSetup['Telegram']['ChatID'] ."&text=" . urlencode($santibuffer));        
        exit();
}

// mi hanno invocato
$aUpdate = json_decode(file_get_contents("php://input"), TRUE);
file_put_contents('update', print_r($aUpdate, true));   // per debug, ma lo lascio, che non si sa mai
$comando = str_ireplace('@' . $aSetup['Telegram']['BotUser'], '', $aUpdate['message']['text']);
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

### END OF FILE ###