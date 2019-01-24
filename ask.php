<?php
   $xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>";      
   //***********************************************************************************************************************
   // V1.44 : Ask Eedomus
   
	// recuperation des infos depuis la requete
    $action = getArg("action", true, 'statut');
	$type = getArg("type", true, 'poll');
	$delai = getArg("delai", false, 30);
	$canal = getArg("canal", false, 'email');
	$value = getArg("value", false, '');
	$apipb = getArg("apipb", false, '');
	$apiwh = getArg("apiwh", false, '');
	
	
    // API eedomus
	$api_user = getArg("apiu", false, '');
	$api_secret = getArg("apis", false, '');
	// API DU PERIPHERIQUE APPELANT LE SCRIPT
    $periph_id = getArg('eedomus_controller_module_id'); 
	
	
	$timestamp = mktime();
				
	$xml .="<ASK>";
	
	$tab_ask_init[1] = array ("question" => 0, "question_txt" => "", "expiration" => 0, "canal" => "", "reponse_api" => 0, "reponse" => 0, "reponse_txt" => "", "actionexpir_api" => 0, "actionexpir" => 0, "actionexpir_txt" => "", "date" => 0, "expirdate" => 0, "snoozedate" => 0);
	$tab_ask_init[2] = $tab_ask_init[1];
	
	//	chargement du tableau des demandes en cours
	$nb_ask = 0;
	$last_ask = 0;
	$next_ask = 1;
	$preload = loadVariable('ASK');
	if ($preload != '' && substr($preload,0,8) != "## ERROR") {
		// détermination du prochain emplacement pour la demande suivante (next_ask), et de la dernière demande (last_ask)
		$tab_ask = $preload;
		if ($tab_ask[1]["question"] != 0) {
			$nb_ask = 1;
			$last_ask = 1;
			$next_ask = 2;
		}
		if ($tab_ask[2]["question"] != 0) {
			$nb_ask += 1;
			if ($tab_ask[2]["date"] >= $tab_ask[1]["date"]) {
				$last_ask = 2;
			}
			if ($tab_ask[2]["expirdate"] >= $tab_ask[1]["expirdate"]) {
				$next_ask = 1;
			}
				
		}
	} else {
		// si tableau inexistante, initialisation à 0
		$tab_ask = $tab_ask_init;
	}
	
	if ($action == 'raz') {
		saveVariable('ASK', $tab_ask_init);
		saveVariable('ASK_DELAI', "");
		saveVariable('ASK_CANAL', "");
		saveVariable('ASK_CANAL_API', "");
		saveVariable('ASK_ACTIONEXPIR', "");
		saveVariable('ASK_ACTIONEXPIR_API', "");
		saveVariable('ASK_MSGREPONSE', "");
		saveVariable('ASK_MSGREPONSE_API', "");
		saveVariable('ASK_ACTION_API', "");
		saveVariable('ASK_REPONSE_API', "");
		die();
	}
	
	// Actionneur Question
	if ($action == 'question') {
		if ($type == 'ask' && $value > 0) {
			// une question est posée, enregistrement dans le tableau des demandes
			$tab_question = getPeriphValueList($periph_id);
			$tab_ask[$next_ask]["question"] = $value; // value de la question
			foreach($tab_question As $tab_question_value) {
				if ($tab_question_value["value"] == $value) {
					$tab_ask[$next_ask]["question_txt"] = $tab_question_value["state"]; // question textuelle
					break;
				}
			}
			$tab_ask[$next_ask]["date"] = $timestamp; // date de demande
			usleep(5000000);
			// expiration date
			$preload = loadVariable('ASK_DELAI');
			if ($preload != '' && substr($preload,0,8) != "## ERROR") {
				// un delai d'expiration a été positionné par la règle Ask
				$tab_ask[$next_ask]["expiration"] = $preload;
				saveVariable('ASK_DELAI', "");
			} else {
				// pas de délai d'expiration positionné par la règle, on prend la valeur par défaut (VAR2)
				$tab_ask[$next_ask]["expiration"] = $delai;
			}
			$tab_ask[$next_ask]["expirdate"] = $timestamp + $tab_ask[$next_ask]["expiration"] * 60;
			// canal
			$preload = loadVariable('ASK_CANAL');
			$preload2 = loadVariable('ASK_CANAL_API');
			if ($preload != '' && substr($preload,0,8) != "## ERROR" && $preload2 != '' && substr($preload2,0,8) != "## ERROR") {
				// un canal a été positionné par la règle Ask
				$tab_canal = getPeriphValueList($preload2);
				$value_canal = $preload;
				foreach($tab_canal As $tab_canal_value) {
					if ($tab_canal_value["value"] == $value_canal) {
						$tab_ask[$next_ask]["canal"] = $tab_canal_value["state"]; // intitulé du canal (email, sms..)
						break;
					}
				}
				saveVariable('ASK_CANAL', "");
				saveVariable('ASK_CANAL_API', "");
			} else {
				// pas de canal positionné par la règle, on prend la valeur par défaut (VAR1)
				$tab_ask[$next_ask]["canal"] = $canal;
			}
			// action à l'expiration
			$preload = loadVariable('ASK_ACTIONEXPIR');
			$preload2 = loadVariable('ASK_ACTIONEXPIR_API');
			if ($preload != '' && substr($preload,0,8) != "## ERROR" && $preload2 != '' && substr($preload2,0,8) != "## ERROR") {
				// une action à l'expiration a été positionnée par la règle Ask
				$actionexpir_api = $preload2;
				$tab_actionexpir = getPeriphValueList($actionexpir_api);
				$value_actionexpir = $preload;
				foreach($tab_actionexpir As $tab_actionexpir_value) {
					if ($tab_actionexpir_value["value"] == $value_actionexpir) {
						$tab_ask[$next_ask]["actionexpir_api"] = $actionexpir_api; 
						$tab_ask[$next_ask]["actionexpir"] = $value_actionexpir; // valeur de l'action à expiration
						$tab_ask[$next_ask]["actionexpir_txt"] = $tab_actionexpir_value["state"]; // intitulé de l'action à expiration
						break;
					}
				}
				saveVariable('ASK_ACTIONEXPIR', "");
				saveVariable('ASK_ACTIONEXPIR_API', "");
			} else {
				// pas d'action positionnée à l'expiration
				$tab_ask[$next_ask]["actionexpir"] = 0;
				$tab_ask[$next_ask]["actionexpir_txt"] = 0;
				$tab_ask[$next_ask]["actionexpir_api"] = 0;
			}
			// message reponse
			$preload = loadVariable('ASK_MSGREPONSE');
			$preload2 = loadVariable('ASK_MSGREPONSE_API');
			if ($preload != '' && substr($preload,0,8) != "## ERROR" && $preload2 != '' && substr($preload2,0,8) != "## ERROR") {
				// un message de réponse a été positionné par la règle
				$msgreponse_api = $preload2;
				$tab_msgreponse = getPeriphValueList($msgreponse_api);
				$value_msgreponse = $preload;
				foreach($tab_msgreponse As $tab_msgreponse_value) {
					if ($tab_msgreponse_value["value"] == $value_msgreponse) {
						$tab_ask[$next_ask]["reponse_api"] = $msgreponse_api; 
						$tab_ask[$next_ask]["reponse"] = $value_msgreponse; // valeur du message de réponse eedomus
						$tab_ask[$next_ask]["reponse_txt"] = $tab_msgreponse_value["state"]; // intitulé de la réponse eedomus
						break;
					}
				}
			} else {
				// pas de message de réponse positionné par la règle
				$tab_ask[$next_ask]["reponse_api"] = 0;
				$tab_ask[$next_ask]["reponse"] = 0;
				$tab_ask[$next_ask]["reponse_txt"] = "";
			}
			saveVariable('ASK_MSGREPONSE', "");
			saveVariable('ASK_MSGREPONSE_API', "");
			saveVariable('ASK', $tab_ask);
			
			// construction des adresses API de réponses O/N
			$value_reponse_oui = 1;
			$value_reponse_non = 2;
			$value_reponse_snooze = 3;
			$apinotif = loadVariable('ASK_NOTIF1_API');
			if ($next_ask == 2) {
				$value_reponse_oui = 4;
				$value_reponse_non = 5;
				$value_reponse_snooze = 6;
				$apinotif = loadVariable('ASK_NOTIF2_API');
			}
			$api_reponse = loadVariable('ASK_REPONSE_API');
			
			$urlOui =  "https://api.eedomus.com/set?action=periph.value&value=".$value_reponse_oui."&periph_id=".$api_reponse."&api_user=".$api_user."&api_secret=".$api_secret;
			$urlNon =  "https://api.eedomus.com/set?action=periph.value&value=".$value_reponse_non."&periph_id=".$api_reponse."&api_user=".$api_user."&api_secret=".$api_secret;
			$urlSnooze =  "https://api.eedomus.com/set?action=periph.value&value=".$value_reponse_snooze."&periph_id=".$api_reponse."&api_user=".$api_user."&api_secret=".$api_secret;
			
			// Poste la question sur le canal
			$envoi = sdk_envoiMessage($tab_ask[$next_ask]["question_txt"], $tab_ask[$next_ask]["canal"], $urlOui, $urlNon, $urlSnooze, $tab_ask[$next_ask]["expirdate"], $tab_ask[$next_ask]["actionexpir_txt"], $apinotif);
			setValue($periph_id, 0, true);
			
			die();
		}
		if ($type == 'poll') {
			$xml .= "<QUESTION>0</QUESTION>";
		}
	}
	
	// actionneur pour positionner le message de réponse
	if ($action == 'msgreponse') {
		if ($type == 'set'  && $value > 0) {
			// l'action choisit sera la réponse donnée par l'eedomus
			saveVariable('ASK_MSGREPONSE', $value);
			saveVariable('ASK_MSGREPONSE_API', $periph_id);
			setValue($periph_id, 0, true);
			die();
		}
		if ($type == 'poll') {
			$xml .= "<ACTION>0</ACTION>";
		}
	}
	
	// actionneur de réponse obtenue
	if ($action == 'reponse') {
		if ($type == 'set'  && $value > 0) {
			
			$init_ask = array ("question" => 0, "question_txt" => "", "expiration" => 0, "canal" => "", "reponse_api" => 0, "reponse" => 0, "reponse_txt" => "", "actionexpir_api" => 0, "actionexpir" => 0, "actionexpir_txt" => "", "date" => 0, "expirdate" => 0, "snoozedate" => 0);
			// réponse obtenue (Oui, Non ou Snooze)
			$msg_reponse = "";
			$canal_reponse = "";
			$msg_reponse_non = "";
			$msg_reponse_snooze = "";
			
			if ($value == 97) { // Oui depuis IFTTT-Telegram
				if ($last_ask == 1) { 
					$value = 1;
				} else {
					$value = 4;
				}
			}
			if ($value == 98) { // Non depuis IFTTT-Telegram
				if ($last_ask == 1) { 
					$value = 2;
				} else {
					$value = 5;
				}
			}
			if ($value == 99) { // Snooze depuis IFTTT-Telegram
				if ($last_ask == 1) { 
					$value = 3;
				} else {
					$value = 6;
				}
			}
			
			// lecture texte du périphérique "Réponse"
			if ($value == 1 || $value == 2 || $value == 3) {
				$tab_reponse = getPeriphValueList($tab_ask[1]["reponse_api"]);
				
			}
			if ($value == 4 || $value == 5 || $value == 6) {
				$tab_reponse = getPeriphValueList($tab_ask[2]["reponse_api"]);
				
			}
			foreach($tab_reponse As $tab_reponse_value) {
				if ($tab_reponse_value["value"] == 99) {
					$msg_reponse_non = $tab_reponse_value["state"]; // reponse textuelle si non
				}
				if ($tab_reponse_value["value"] == 999) {
					$msg_reponse_snooze = $tab_reponse_value["state"]; // reponse textuelle si snooze
				}
			}
			
			if ($value == 1) { // Oui 1ère ASK
				if ($tab_ask[1]["reponse_api"] != 0) {
					setValue($tab_ask[1]["reponse_api"], $tab_ask[1]["reponse"], true);
					$setaction = sdk_setAction($tab_ask[1]["reponse"]);
					$msg_reponse = $tab_ask[1]["reponse_txt"];
					$canal_reponse = $tab_ask[1]["canal"];
				}
				$tab_ask[1] = $init_ask;
			}
			if ($value == 4) { // Oui 2nde ASK
				if ($tab_ask[2]["reponse_api"] != 0) {
					setValue($tab_ask[2]["reponse_api"], $tab_ask[2]["reponse"], true);
					$setaction = sdk_setAction($tab_ask[2]["reponse"]);
					$msg_reponse = $tab_ask[2]["reponse_txt"];
					$canal_reponse = $tab_ask[2]["canal"];
				}
				$tab_ask[2] = $init_ask;
			}
			if ($value == 2) { // Non 1ère ASK
				if ($tab_ask[1]["reponse_api"] != 0) {
					setValue($tab_ask[1]["reponse_api"], 99, true);
					$msg_reponse = $msg_reponse_non;
					$canal_reponse = $tab_ask[1]["canal"];
				}
				$tab_ask[1] = $init_ask;
			}
			if ($value == 5) { // Non 2nde ASK
				if ($tab_ask[2]["reponse_api"] != 0) {
					setValue($tab_ask[2]["reponse_api"], 99, true);
					$msg_reponse = $msg_reponse_non;
					$canal_reponse = $tab_ask[2]["canal"];
				}
				$tab_ask[2] = $init_ask;
			}
			if ($value == 3) { // Snooze 1ère ASK
			   $tab_ask[1]["snoozedate"] = $timestamp + 1800;
			   $tab_ask[1]["expirdate"] = $timestamp + $tab_ask[1]["expiration"] * 60;
			   if ($tab_ask[1]["snoozedate"] >= $tab_ask[1]["expirdate"]) {
					$tab_ask[1]["expirdate"] = $tab_ask[1]["snoozedate"] + 600;
				}
			   $msg_reponse = $msg_reponse_snooze;
			   $canal_reponse = $tab_ask[1]["canal"];
			   if ($tab_ask[1]["reponse_api"] != 0) {
					setValue($tab_ask[1]["reponse_api"], 999, true);
			   }
			}
			if ($value == 6) { // Snooze 2nde ASK
				$tab_ask[2]["snoozedate"] = $timestamp + 1800;
				$tab_ask[2]["expirdate"] = $timestamp + $tab_ask[2]["expiration"] * 60;
				if ($tab_ask[2]["snoozedate"] >= $tab_ask[2]["expirdate"]) {
					$tab_ask[2]["expirdate"] = $tab_ask[2]["snoozedate"] + 600;
				}
				$msg_reponse = $msg_reponse_snooze;
			    $canal_reponse = $tab_ask[2]["canal"];
				if ($tab_ask[2]["reponse_api"] != 0) {
					setValue($tab_ask[2]["reponse_api"], 999, true);
			   }
			}
			
			if ($msg_reponse != "" && $canal_reponse != "") {
				// envoi de la réponse selon le canal
				$envoi = sdk_envoiMessage($msg_reponse, $canal_reponse, "", "", "", "", "", "");
			}
			saveVariable('ASK', $tab_ask);
			setValue(loadVariable('ASK_REPONSE_API'), 0, true);
			die();
		}
		if ($type == 'poll') {
			saveVariable('ASK_REPONSE_API', $periph_id);
			$xml .= "<REPONSE>0</REPONSE>";
		}
	}
	
	
	
	// enregistrement du canal donné en règle Ask
	if ($action == 'canal' && $value > 0) {
			saveVariable('ASK_CANAL', $value);
			saveVariable('ASK_CANAL_API', $periph_id);
			setValue($periph_id, 0, true);
			die();
		
	}
	
	// enregistrement de l'action à l'expiration
	if ($action == 'actionexpir' && $value > 0) {
			saveVariable('ASK_ACTIONEXPIR', $value);
			saveVariable('ASK_ACTIONEXPIR_API', $periph_id);
			setValue($periph_id, 0, true);
			die();
		
	}
	
	// enregistrement du délai d'expiration donné en règle Ask
	if ($action == 'expir' && $value > 0) {
		saveVariable('ASK_DELAI', $value);
		setValue($periph_id, 0, true);
		die();
	}
	
	// enregistrement de l'api du capteur action
	if ($action == 'action' && $type == 'poll') {
		saveVariable('ASK_ACTION_API', $periph_id);
		$xml .= "<ACTION>0</ACTION>";
	}
	
	// Capteur Statut et contrôle snooze/expiration date
	if ($action == 'statut') {
		
		
		$apinotif_arg = getArg("apinotif", $mandatory = false, $default = '');
		$tabNotifs = explode(",", $apinotif_arg);
		$i = 0;
		$apinotif1 = "";
		$apinotif2 = "";
		foreach($tabNotifs as $tabNotifs_api) {
			if ($i == 0) {
				$apinotif1 = $tabNotifs_api;
				setValue($apinotif1, "--");
			}
			if ($i == 1) {
				$apinotif2 = $tabNotifs_api;
				setValue($apinotif2, "--");
				
			}
			$i = $i + 1;		
		}
		saveVariable('ASK_NOTIF1_API', $apinotif1);
		saveVariable('ASK_NOTIF2_API', $apinotif2);
		
		// Affiche la dernière demande et sa date d'expiration
		$last_ask_expir = "";
		if ($last_ask != 0) {
			$last_ask_expir = date('d/m/Y H:i', $tab_ask[$last_ask]["expirdate"]);
			$last_ask_txt = "...".substr($tab_ask[$last_ask]["question_txt"], -24);
		}
		$xml .= "<STATUT>Q1: ..".substr($tab_ask[1]["question_txt"], -24)." | Q2: ..".substr($tab_ask[2]["question_txt"], -24)."</STATUT>";
		$xml .= "<Q1>Code :".$tab_ask[1]["question"]." Texte :".$tab_ask[1]["question_txt"]."</Q1>";
		$xml .= "<EC1>Canal :".$tab_ask[1]["canal"]." Délai :".$tab_ask[1]["expiration"]."</EC1>";
		$xml .= "<R1>API :".$tab_ask[1]["reponse_api"]." Value :".$tab_ask[1]["reponse"]." Texte :".$tab_ask[1]["reponse_txt"]."</R1>";
		$xml .= "<D1>Date :".$tab_ask[1]["date"]." Expiration :".$tab_ask[1]["expirdate"]." Snooze :".$tab_ask[1]["snoozedate"]."</D1>";
		
		$xml .= "<Q2>Code :".$tab_ask[2]["question"]." Texte :".$tab_ask[2]["question_txt"]."</Q2>";
		$xml .= "<EC2>Canal :".$tab_ask[2]["canal"]." Délai :".$tab_ask[2]["expiration"]."</EC2>";
		$xml .= "<R2>API :".$tab_ask[2]["reponse_api"]." Value :".$tab_ask[2]["reponse"]." Texte :".$tab_ask[2]["reponse_txt"]."</R2>";
		$xml .= "<D2>Date :".$tab_ask[2]["date"]." Expiration :".$tab_ask[2]["expirdate"]." Snooze :".$tab_ask[2]["snoozedate"]."</D2>";
		
		
		$init_ask = array ("question" => 0, "question_txt" => "", "expiration" => 0, "canal" => "", "reponse_api" => 0, "reponse" => 0, "reponse_txt" => "", "actionexpir_api" => 0, "actionexpir" => 0, "actionexpir_txt" => "", "date" => 0, "expirdate" => 0, "snoozedate" => 0);
			
		if ($tab_ask[1]["question"] != 0) {
			if ($tab_ask[1]["expirdate"] <= $timestamp) {
			// requête expirée
				if ($tab_ask[1]["actionexpir"] > 0) {
					setValue($tab_ask[1]["actionexpir_api"], $tab_ask[1]["actionexpir"], true);
					$envoi = sdk_envoiMessage($tab_ask[1]["actionexpir_txt"], $tab_ask[1]["canal"], "", "", "", "", "", "");
					$setaction = sdk_setAction($tab_ask[1]["actionexpir"]);
					
				}
				$tab_ask[1] = $init_ask;
				saveVariable('ASK', $tab_ask);
				
			}
			if ($tab_ask[1]["snoozedate"] <= $timestamp && $tab_ask[1]["snoozedate"] != 0) {
			// snooze atteint
				$tab_ask[1]["snoozedate"] = 0;
				setValue($tab_ask[1]["reponse_api"], 0, true);
				$api_reponse = loadVariable('ASK_REPONSE_API');
				saveVariable('ASK', $tab_ask);
				$value_reponse_oui = 1;
				$value_reponse_non = 2;
				$value_reponse_snooze = 3;
				$urlOui =  "https://api.eedomus.com/set?action=periph.value&value=".$value_reponse_oui."&periph_id=".$api_reponse."&api_user=".$api_user."&api_secret=".$api_secret;
				$urlNon =  "https://api.eedomus.com/set?action=periph.value&value=".$value_reponse_non."&periph_id=".$api_reponse."&api_user=".$api_user."&api_secret=".$api_secret;
				$urlSnooze =  "https://api.eedomus.com/set?action=periph.value&value=".$value_reponse_snooze."&periph_id=".$api_reponse."&api_user=".$api_user."&api_secret=".$api_secret;
				$envoi = sdk_envoiMessage($tab_ask[1]["question_txt"], $tab_ask[1]["canal"], $urlOui, $urlNon, $urlSnooze, "", "", $apinotif1);
				
			}
		}
		if ($tab_ask[2]["question"] != 0) {
			if ($tab_ask[2]["expirdate"] <= $timestamp) {
			// requête expirée
				if ($tab_ask[2]["actionexpir"] > 0) {
					setValue($tab_ask[2]["actionexpir_api"], $tab_ask[2]["actionexpir"], true);
					$envoi = sdk_envoiMessage($tab_ask[2]["actionexpir_txt"], $tab_ask[2]["canal"], "", "", "", "", "", "");
					$setaction = sdk_setAction($tab_ask[2]["actionexpir"]);
					
				}
				$tab_ask[2] = $init_ask;
				saveVariable('ASK', $tab_ask);
				
			}
			if ($tab_ask[2]["snoozedate"] <= $timestamp && $tab_ask[2]["snoozedate"] != 0) {
			// snooze atteint
				$tab_ask[2]["snoozedate"] = 0;
				$api_reponse = loadVariable('ASK_REPONSE_API');
				saveVariable('ASK', $tab_ask);
				$value_reponse_oui = 4;
				$value_reponse_non = 5;
				$value_reponse_snooze = 6;
				$urlOui =  "https://api.eedomus.com/set?action=periph.value&value=".$value_reponse_oui."&periph_id=".$api_reponse."&api_user=".$api_user."&api_secret=".$api_secret;
				$urlNon =  "https://api.eedomus.com/set?action=periph.value&value=".$value_reponse_non."&periph_id=".$api_reponse."&api_user=".$api_user."&api_secret=".$api_secret;
				$urlSnooze =  "https://api.eedomus.com/set?action=periph.value&value=".$value_reponse_snooze."&periph_id=".$api_reponse."&api_user=".$api_user."&api_secret=".$api_secret;
				$envoi = sdk_envoiMessage($tab_ask[2]["question_txt"], $tab_ask[2]["canal"], $urlOui, $urlNon, $urlSnooze, "", "", $apinotif2);
				
			}
		}
	}
	
	if ($action == 'void') {
		die();
	}
	
	$xml .="</ASK>";
	sdk_header('text/xml');
 	echo $xml;	
	
	// Positionnement d'une action dans le capteur (pour interpretation par des règles)
	function sdk_setAction($value) {
		$preload = loadVariable('ASK_ACTION_API');
		if (is_numeric($preload) && $preload > 1 && $value > 0) {
			setValue($preload, $value, true);
		}
	}
	
	function sdk_noaccent($text) {
	$utf8_keys = array(	'/[áàâãä]/','/[ÁÀÂÃÄ]/', '/[ÍÌÎÏ]/', '/[íìîï]/', '/[éèêë]/', '/[ÉÈÊË]/', '/[óòôõö]/', '/[ÓÒÔÕÖ]/', '/[úùûü]/', '/[ÚÙÛÜ]/' ,	'/ç/', '/Ç/', '/ñ/', '/Ñ/');
	$utf8_values = array('a', 'A', 'I',	'i', 'e', 'E', 'o',	'O', 'u', 'U', 'c',	'C', 'n', 'N');
	return preg_replace($utf8_keys, $utf8_values, $text);
	}	
	
	// envoi de messages
	function sdk_envoiMessage($message, $canal, $urlOui, $urlNon, $urlSnooze, $expiration, $actionexpiration, $apinotif) {
		global $apipb;
		global $apiwh;
		
		$emailobjet = "Ask eedomus - ".substr($message, 0, 30)."...";
		$emailobjet = sdk_noaccent($emailobjet);
		$emailbody = $message."\r\n\r\n";
		$emailbody2 = "";
		$emialbody3 = "";
		if ($urlOui != "") {
			if ($canal == 'PushingBox' && $apipb != "" && $apipb != " " && $apipb != "plugin.parameters.PB") {
				$emailbody .= "Sélectionner Oui/Non/Snooze sur votre application eedomus\r\n\r\n";
				if ($expiration != "") {
					$emailbody .= "Expiration de la demande : ".date('H:i', $expiration)."\r\n\r\n";
				}
				if ($actionexpiration != "") {
					$emailbody .= "A expiration : ".$actionexpiration."\r\n\r\n";
				}
			}
			if ($canal == 'IFTTTelegram' && is_numeric($apiwh) && $apiwh > 1)	{
				$emailbody .= "<br>(Oui/Non/Snooze)";
				if ($expiration != "") {
					$emailbody2 .= "<br><br>Expiration : ".date('H:i', $expiration);
				}
				if ($actionexpiration != "") {
					$emailbody2 .= " (".$actionexpiration.")";
				}
			}
		}
		$emailbody = sdk_noaccent($emailbody);
		$emailbody2 = sdk_noaccent($emailbody2);
		$emailbody3 = sdk_noaccent($emailbody3);
		if ($canal == 'IFTTTelegram' && is_numeric($apiwh) && $apiwh > 1) {
			// charge les variables de notification ASK et lance le plugin notification sur la valeur "ASK" (99999)
			saveVariable("ASK_99999_1", $emailbody.$emailbody2.$emailbody3);
			setValue($apiwh, 99999);
			//$url = "http://maker.ifttt.com/trigger/Ask_eedomus/with/key/".$apiwh."?value1=".urlencode($emailbody)."&value2=".urlencode($emailbody2)."&value3=".urlencode($emailbody3);
			//return httpQuery($url,'GET'); 	
		}
				
		if ($canal == 'RegleNotif' && $apinotif != "") {
			return setValue($apinotif, urlencode($emailbody));
		}
		
		if ($canal == 'PushingBox' && $apipb != "" && $apipb != "plugin.parameters.PB") {
			$url = "http://api.pushingbox.com/pushingbox?devid=".$apipb."&objet=".urlencode($emailobjet)."&body=".urlencode($emailbody);
			return httpQuery($url,'GET'); 	
		}
		
	}
	
	
?>
