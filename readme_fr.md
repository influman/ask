# Installation
Gestion des Questions/Réponses (ASK) avec eedomus  
  
[Documentacion en espanol](http://www.domoticadomestica.com/el-comando-ask-ya-esta-disponible-en-eedomus/ "Domotica Domestica") 
    
### Les principes
  
Ask permet à l'eedomus de vous demander confirmation avant d'exécuter les actions d'une règle.  
Imaginez votre règle actuelle automatique qui ferme les volets le soir l'été à 23h00.  
L'été, cependant, vous aimez rester tard dans votre jardin. Et dans ce cas, la fermeture automatique des volets n'est pas souhaitée.  
Par confort, mais aussi par sécurité, pour ne pas se retrouver enfermé dehors.  
  
Dans votre règle actuelle, au lieu d'agir directement, vous exécutez les paramètres d'Ask, et l'eedomus vous demande confirmation avant d'agir.  
Dans les paramètres d'Ask, avant utilisation, vous définissez la question à poser, l'action à réaliser en cas de réponse positive, et l'action à réaliser automatiquement en cas d'absence de réponse après le délai d'expiration de votre choix.  
  
Ask envoie la question sur un canal de communication.  
Vous avez alors le choix de répondre Oui, Non, ou de Temporiser (Snooze) pour que l'eedomus repose la question dans 30mn.  
Ask positionne alors l'action dans un état informatif. Action qui devra alors être interprétée par la ou les règles de votre choix.  
  
Ask joue en réalité le rôle de passerelle :  
Votre règle initiale n'active pas vos actions, elle pose la question via les paramètres Ask en amont.
Quand vous répondez Oui, ou à l'expiration (si paramétrée), Ask poste l'action souhaitée.  
Une règle que vous devrez alors créer en aval devra détecter cette action Ask pour cette fois-ci bien exécuter les actions souhaitées sur vos périphériques.  
Ce mode de fonctionnement permet de rendre Ask indépendant de vos scènes et périphériques pour un usage donné.  
  
  

### Ajout des périphériques
Cliquez sur "Configuration" / "Ajouter ou supprimer un périphérique" / "Store eedomus" / "Ask" / "Créer"  

  
*Voici les différents champs à renseigner:*

* [Obligatoire] - Le canal de communication par défaut
* [Obligatoire] - Le délai d'expiration des demandes par défaut
* [Optionnel]   - Le plugin de Notifications IFTTT (Telegram, Twitter...)
* [Optionnel]   - Le code API PushingBox
* [Obligatoire] - Votre code API User
* [Obligatoire] - Votre code API Secret
  
![STEP0](https://i.imgur.com/kCnouKT.png)  
  
  
### Utilisation
Dans vos règles, si vous souhaitez que l'eedomus vous interroge au lieu d'agir directement, sélectionnez les actions "Ask" nécessaires, en respectant absolument leur ordre numéroté :      

* Ask - 01 - Canal (optionnel)               
* Ask - 02 - Délai expiration (optionnel)    
* Ask - 03 - Message Réponse OK           	 
* Ask - 04 - Action à expiration (optionnel) 
* Ask - 05 - Question						 

  
![STEP1A](https://i.imgur.com/lGjhWUV.png)   
  
Si vous ne précisez pas le délai d'expiration, celui par défaut sera utilisé (VAR1).  
Si vous ne précisez pas le canal souhaité, celui par défaut sera utilisé (VAR2).  
"Message Réponse OK" est le message que devra confirmer l'eedomus en cas de réponse positive.  
"Action à expiration" est l'action que positionnera l'eedomus à l'expiration de la demande (en l'absence de réponse). Si omise, aucune action ne sera lancée par défaut.  
"Question" est la question que doit vous poser initialement l'eedomus.  
  
![STEP1B](https://i.imgur.com/ksBTVFF.png)  
  
  
Vos différentes "Questions" possibles sont donc à rajouter dans les valeurs de l'actionneur "Ask - 05 - Question".  
Vos différentes "Réponses positives" sont à rajouter dans les valeurs de l'actionneur "Ask - 03 - Message Réponse OK".  Ne pas supprimer les valeurs 99 et 999 (vous pouvez cependant en modifier le texte).  
Vos différentes "Actions à l'expiration" sont à rajouter dans les valeurs de l'actionneur "Ask - 04 - Action à expiration (optionnel)".    
  
A la réponse positive, ou à l'expiration avec action, le capteur "Ask - Action" sera positionné sur l'action sélectionnée, de même valeur numérique.  
Assurez-vous donc bien que les valeurs numériques (value) des actions distinctes de "Ask - 03 - Message Réponse OK"  et "Ask - 04 - Action à expiration (optionnel)" se retrouvent bien dans le capteur "Ask - Action".  
A vous d'y associer ensuite des règles en fonction de la valeur de ce capteur, pour impacter les périphériques souhaités ("Ask - Action" DEVIENT MAINTENANT "[ASK] Fermeture volets" ALORS ...)  
  

Par exemple, si vous paramétrez la valeur 10 suivante dans "Ask - Message Réponse OK" :  
* Value 10 / Description "Ok, j'active ma scène Scène#1"  
alors il faut créer dans Ask-Action la valeur 10 également :  
* Value 10 / Description "Activation de la scène Scène#1".  
Si vous souhaitez que cette valeur 10 soit positionnable à l'expiration d'une requête, on ajoute la valeur 10 dans Ask-Action à expiration :  
* Value 10 / Description "Expiration, j'active la scène Scène#1".  
  
  
### Interactions
  
Actuellement, les modes "PushingBox", "RegleNotif", et "IFTTTelegram" sont disponibles, et vous permettent d'être informé par l'eedomus. 

### Canal PushingBox

En utilisant PushingBox, vous pourrez choisir le ou les canaux souhaités de communication.  
Cependant, pour répondre à la requête formulée via ce mode, il faut alors passer par le portail ou application eedomus.  
   
Sur le portail Eedomus, seuls les capteurs/actionneurs suivants sont donc à conserver en visible (les autres étant gérés dans vos règles) :  
  
* Ask - Statut             
* Ask - Réponse
  
![STEP2](https://i.imgur.com/kdw3S9y.png) 
  
Le capteur "Statut" vous informe sur les questions encore actives (2 au maximum).  
L'actionneur "Réponse" vous permet de répondre Oui/Non ou Snooze à la question correspondant (Q1 ou Q2).  
  
* Oui, l'action définie est confirmée et réalisée par l'eedomus
* Non, l'action est annulée
* Snooze, l'action est temporisée, et sera redemandée dans 30mn
  
![STEP3](https://i.imgur.com/iduV3eM.png) 
  
Il est possible de répondre Oui ou Non juste après un Snooze.  
  
### Canal RegleNotif
  
En attendant que les notifications eedomus internes soient intégrées et exécutables dans les scripts, vous pouvez utiliser des règles. 
Ainsi, vous pourrez utiliser vos modes de notifications eedomus, et surtout intégrer la capacité de réponse à la notification (testé sur email).  
  
Pour ce faire, vous devez suivre la procédure manuelle suivante :  
  
  * Le canal Ask à utiliser doit être "RegleNotif"  
  * Après installation du plugin, recherchez et notez les codes API des périphériques suivants : Ask - Réponse, Ask - Notification (Q1) et Ask - Notification (Q2)  
  * Dans le capteur "Ask - Statut", inscrivez en VAR3 les deux codes API des Ask - Notification (Q1) et (Q2), séparés par une virgule :  
    
![STEP4](https://i.imgur.com/LpZLADz.png) 
  
  * Créez deux nouvelles règles "Ask - Notification (Q1)" et "Ask - Notification (Q2)", tel qu'illustré ci-après.
  * Choisissez les notifications souhaitées à votre guise.  
  
![STEP5](https://i.imgur.com/Eg3Vlv1.png) 

  * Via ce mode de notification, vous n'aurez pas les accusés de réception de vos réponses, mais seulement les questions de l'eedomus.  
  
### Canal IFTTTelegram

Telegram est une application de tchat tel que Whatsapp et Messenger. 
Elle est hautement personnalisable et donc adaptée à une utilisation domotique.
Actuellement, il n'existe pas d'application Telegram dédiée à l'eedomus (un "Bot" en langage Telegram), mais il est possible de s'interfacer grâce au Bot fourni par IFTTT.com  
Ainsi, Ask eedomus vous posera les questions directement via la messagerie Telegram, et vous pourrez y répondre directement également.
  
  * Si vous ne l'avez pas déjà installé, installez le plugin "Notifications IFTTT" du store compatible avec Ask.  
  * Suivez la documentation de ce plugin pour l'installation du Webhooks IFTTT et de Telegram en prérequis.
  * Installer le plugin Ask dans le store eedomus, choisissez le canal IFTTTelegram, et sélectionner le plugin "Notifications Telegram"
   
  
Pour gérer vos réponses dans Telegram,  
  
  * Créez un nouvel applet sous IFTTT, avec pour THIS le service Telegram, et le trigger "New message with key phrase to @IFTTT"
  * Mettez "Oui" comme Key phrase et "(...)" comme "What to send as a reply"
  * Pour THAT, sélectionner l'action "Make a web Request" du service "Webhooks"
  * dans l'URL, insérer la requête API eedomus permettant de positionner le périphérique "Ask - Reponse" (via son code API noté précédemment) à la valeur 97
  * ex:  <https://api.eedomus.com/set?action=periph.value&periph_id=888888&value=97&api_user=XXXXXXX&api_secret=aaaaaaabbbbbbcccc>
  
 ![WH4](https://i.imgur.com/nk9Uu83.png) 
 
  * Créez ensuite un applet supplémentaire identique, avec "Non" comme Key Phrase et la valeur 98 dans l'URL appelée de l'API eedomus
  * Créez ensuite un applet supplémentaire identique, avec "Snooze" comme Key Phrase et la valeur 99 dans l'URL appelée de l'API eedomus

Enjoy !

 ![TLGM2](https://i.imgur.com/klt8pEl.jpg) 
   
   
  



 

 

  


