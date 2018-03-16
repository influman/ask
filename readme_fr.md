# Installation
Gestion des Questions/R�ponses (ASK) avec eedomus  
  
[Documentacion en espanol](http://www.domoticadomestica.com/el-comando-ask-ya-esta-disponible-en-eedomus/ "Domotica Domestica") 
    
### Les principes
  
Ask permet � l'eedomus de vous demander confirmation avant d'ex�cuter les actions d'une r�gle.  
Imaginez votre r�gle actuelle automatique qui ferme les volets le soir l'�t� � 23h00.  
L'�t�, cependant, vous aimez rester tard dans votre jardin. Et dans ce cas, la fermeture automatique des volets n'est pas souhait�e.  
Par confort, mais aussi par s�curit�, pour ne pas se retrouver enferm� dehors.  
  
Dans votre r�gle actuelle, au lieu d'agir directement, vous ex�cutez les param�tres d'Ask, et l'eedomus vous demande confirmation avant d'agir.  
Dans les param�tres d'Ask, avant utilisation, vous d�finissez la question � poser, l'action � r�aliser en cas de r�ponse positive, et l'action � r�aliser automatiquement en cas d'absence de r�ponse apr�s le d�lai d'expiration de votre choix.  
  
Ask envoie la question sur un canal de communication.  
Vous avez alors le choix de r�pondre Oui, Non, ou de Temporiser (Snooze) pour que l'eedomus repose la question dans 30mn.  
Ask positionne alors l'action dans un �tat informatif. Action qui devra alors �tre interpr�t�e par la ou les r�gles de votre choix.  
  
Ask joue en r�alit� le r�le de passerelle :  
Votre r�gle initiale n'active pas vos actions, elle pose la question via les param�tres Ask en amont.
Quand vous r�pondez Oui, ou � l'expiration (si param�tr�e), Ask poste l'action souhait�e.  
Une r�gle que vous devrez alors cr�er en aval devra d�tecter cette action Ask pour cette fois-ci bien ex�cuter les actions souhait�es sur vos p�riph�riques.  
Ce mode de fonctionnement permet de rendre Ask ind�pendant de vos sc�nes et p�riph�riques pour un usage donn�.  
  
  

### Ajout des p�riph�riques
Cliquez sur "Configuration" / "Ajouter ou supprimer un p�riph�rique" / "Store eedomus" / "Ask" / "Cr�er"  

  
*Voici les diff�rents champs � renseigner:*

* [Obligatoire] - Le canal de communication par d�faut
* [Obligatoire] - Le d�lai d'expiration des demandes par d�faut
* [Optionnel]   - Le plugin de Notifications IFTTT (Telegram, Twitter...)
* [Optionnel]   - Le code API PushingBox
* [Obligatoire] - Votre code API User
* [Obligatoire] - Votre code API Secret
  
![STEP0](https://i.imgur.com/kCnouKT.png)  
  
  
### Utilisation
Dans vos r�gles, si vous souhaitez que l'eedomus vous interroge au lieu d'agir directement, s�lectionnez les actions "Ask" n�cessaires, en respectant absolument leur ordre num�rot� :      

* Ask - 01 - Canal (optionnel)               
* Ask - 02 - D�lai expiration (optionnel)    
* Ask - 03 - Message R�ponse OK           	 
* Ask - 04 - Action � expiration (optionnel) 
* Ask - 05 - Question						 

  
![STEP1A](https://i.imgur.com/lGjhWUV.png)   
  
Si vous ne pr�cisez pas le d�lai d'expiration, celui par d�faut sera utilis� (VAR1).  
Si vous ne pr�cisez pas le canal souhait�, celui par d�faut sera utilis� (VAR2).  
"Message R�ponse OK" est le message que devra confirmer l'eedomus en cas de r�ponse positive.  
"Action � expiration" est l'action que positionnera l'eedomus � l'expiration de la demande (en l'absence de r�ponse). Si omise, aucune action ne sera lanc�e par d�faut.  
"Question" est la question que doit vous poser initialement l'eedomus.  
  
![STEP1B](https://i.imgur.com/ksBTVFF.png)  
  
  
Vos diff�rentes "Questions" possibles sont donc � rajouter dans les valeurs de l'actionneur "Ask - 05 - Question".  
Vos diff�rentes "R�ponses positives" sont � rajouter dans les valeurs de l'actionneur "Ask - 03 - Message R�ponse OK".  Ne pas supprimer les valeurs 99 et 999 (vous pouvez cependant en modifier le texte).  
Vos diff�rentes "Actions � l'expiration" sont � rajouter dans les valeurs de l'actionneur "Ask - 04 - Action � expiration (optionnel)".    
  
A la r�ponse positive, ou � l'expiration avec action, le capteur "Ask - Action" sera positionn� sur l'action s�lectionn�e, de m�me valeur num�rique.  
Assurez-vous donc bien que les valeurs num�riques (value) des actions distinctes de "Ask - 03 - Message R�ponse OK"  et "Ask - 04 - Action � expiration (optionnel)" se retrouvent bien dans le capteur "Ask - Action".  
A vous d'y associer ensuite des r�gles en fonction de la valeur de ce capteur, pour impacter les p�riph�riques souhait�s ("Ask - Action" DEVIENT MAINTENANT "[ASK] Fermeture volets" ALORS ...)  
  

Par exemple, si vous param�trez la valeur 10 suivante dans "Ask - Message R�ponse OK" :  
* Value 10 / Description "Ok, j'active ma sc�ne Sc�ne#1"  
alors il faut cr�er dans Ask-Action la valeur 10 �galement :  
* Value 10 / Description "Activation de la sc�ne Sc�ne#1".  
Si vous souhaitez que cette valeur 10 soit positionnable � l'expiration d'une requ�te, on ajoute la valeur 10 dans Ask-Action � expiration :  
* Value 10 / Description "Expiration, j'active la sc�ne Sc�ne#1".  
  
  
### Interactions
  
Actuellement, les modes "PushingBox", "RegleNotif", et "IFTTTelegram" sont disponibles, et vous permettent d'�tre inform� par l'eedomus. 

### Canal PushingBox

En utilisant PushingBox, vous pourrez choisir le ou les canaux souhait�s de communication.  
Cependant, pour r�pondre � la requ�te formul�e via ce mode, il faut alors passer par le portail ou application eedomus.  
   
Sur le portail Eedomus, seuls les capteurs/actionneurs suivants sont donc � conserver en visible (les autres �tant g�r�s dans vos r�gles) :  
  
* Ask - Statut             
* Ask - R�ponse
  
![STEP2](https://i.imgur.com/kdw3S9y.png) 
  
Le capteur "Statut" vous informe sur les questions encore actives (2 au maximum).  
L'actionneur "R�ponse" vous permet de r�pondre Oui/Non ou Snooze � la question correspondant (Q1 ou Q2).  
  
* Oui, l'action d�finie est confirm�e et r�alis�e par l'eedomus
* Non, l'action est annul�e
* Snooze, l'action est temporis�e, et sera redemand�e dans 30mn
  
![STEP3](https://i.imgur.com/iduV3eM.png) 
  
Il est possible de r�pondre Oui ou Non juste apr�s un Snooze.  
  
### Canal RegleNotif
  
En attendant que les notifications eedomus internes soient int�gr�es et ex�cutables dans les scripts, vous pouvez utiliser des r�gles. 
Ainsi, vous pourrez utiliser vos modes de notifications eedomus, et surtout int�grer la capacit� de r�ponse � la notification (test� sur email).  
  
Pour ce faire, vous devez suivre la proc�dure manuelle suivante :  
  
  * Le canal Ask � utiliser doit �tre "RegleNotif"  
  * Apr�s installation du plugin, recherchez et notez les codes API des p�riph�riques suivants : Ask - R�ponse, Ask - Notification (Q1) et Ask - Notification (Q2)  
  * Dans le capteur "Ask - Statut", inscrivez en VAR3 les deux codes API des Ask - Notification (Q1) et (Q2), s�par�s par une virgule :  
    
![STEP4](https://i.imgur.com/LpZLADz.png) 
  
  * Cr�ez deux nouvelles r�gles "Ask - Notification (Q1)" et "Ask - Notification (Q2)", tel qu'illustr� ci-apr�s.
  * Choisissez les notifications souhait�es � votre guise.  
  
![STEP5](https://i.imgur.com/Eg3Vlv1.png) 

  * Via ce mode de notification, vous n'aurez pas les accus�s de r�ception de vos r�ponses, mais seulement les questions de l'eedomus.  
  
### Canal IFTTTelegram

Telegram est une application de tchat tel que Whatsapp et Messenger. 
Elle est hautement personnalisable et donc adapt�e � une utilisation domotique.
Actuellement, il n'existe pas d'application Telegram d�di�e � l'eedomus (un "Bot" en langage Telegram), mais il est possible de s'interfacer gr�ce au Bot fourni par IFTTT.com  
Ainsi, Ask eedomus vous posera les questions directement via la messagerie Telegram, et vous pourrez y r�pondre directement �galement.
  
  * Si vous ne l'avez pas d�j� install�, installez le plugin "Notifications IFTTT" du store compatible avec Ask.  
  * Suivez la documentation de ce plugin pour l'installation du Webhooks IFTTT et de Telegram en pr�requis.
  * Installer le plugin Ask dans le store eedomus, choisissez le canal IFTTTelegram, et s�lectionner le plugin "Notifications Telegram"
   
  
Pour g�rer vos r�ponses dans Telegram,  
  
  * Cr�ez un nouvel applet sous IFTTT, avec pour THIS le service Telegram, et le trigger "New message with key phrase to @IFTTT"
  * Mettez "Oui" comme Key phrase et "(...)" comme "What to send as a reply"
  * Pour THAT, s�lectionner l'action "Make a web Request" du service "Webhooks"
  * dans l'URL, ins�rer la requ�te API eedomus permettant de positionner le p�riph�rique "Ask - Reponse" (via son code API not� pr�c�demment) � la valeur 97
  * ex:  <https://api.eedomus.com/set?action=periph.value&periph_id=888888&value=97&api_user=XXXXXXX&api_secret=aaaaaaabbbbbbcccc>
  
 ![WH4](https://i.imgur.com/nk9Uu83.png) 
 
  * Cr�ez ensuite un applet suppl�mentaire identique, avec "Non" comme Key Phrase et la valeur 98 dans l'URL appel�e de l'API eedomus
  * Cr�ez ensuite un applet suppl�mentaire identique, avec "Snooze" comme Key Phrase et la valeur 99 dans l'URL appel�e de l'API eedomus

Enjoy !

 ![TLGM2](https://i.imgur.com/klt8pEl.jpg) 
   
   
  



 

 

  


