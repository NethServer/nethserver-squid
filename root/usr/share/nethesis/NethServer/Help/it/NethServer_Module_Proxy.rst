=========
Proxy web
=========

Configura il proxy web e il filtro contenuti.

Proxy
=====

Il Proxy Web lavora per ridurre l'utilizzo della banda facendo cache
delle pagine visitate. E' trasparente ai web browser che utilizzano
questo server come loro gateway.

Abilitato
    Abilita il Proxy.

Parent proxy
    Inserire l'IP e la porta del parent proxy. La sintassi corretta è
    Indirizzo_IP:porta .

Filtro
======

Il filtro contenuti serve per controllare la navigazione web ed
impostare dei blocchi in base ad alcuni elementi quali parole chiave, ip
interni, utenti interni, valutazione del contenuto della pagina web,
estensioni dei file. Grazie a questo strumento è possibile ad esempio abilitare
l'accesso solo su alcuni siti desiderati (ad esempio quelli di interesse
aziendale) bloccando tutti gli altri.

Modalità
    Abilitando il Filtro Web è possibile configurarlo nella modalità
    "Blocca tutto" e poi permettere le categorie selezionate, oppure
    "Permetti tutto" e poi bloccare le categorie selezionate.

Blocca accesso con IP ai siti web
    Se abilitato, non è possibile accedere ai siti web usando un IP ma solo il nome host.

Abilita filtro con espressioni su URL
    Se abilitato, gli URL sono scansionati alla ricerca di parole che ricadono nelle categorie selezionate. 
    Ad esempio potrebbero essere bloccati gli url che contengono la parola *sesso*.

Lista di estensioni file bloccate
    Inserire le estensioni che si vogliono bloccare, separate da virgola.

Siti e IP bloccati
    Contiene la lista di siti sempre bloccati e la lista degli host della LAN che non possono navigare.

Siti e IP permessi
    Contiene la lista dei siti sempre permessi e degli host della LAN che possono bypassare il filtro contenuti.

Bypass proxy trasparente
========================

Configurare alcuni IP per bypassare il proxy trasparente ed accedere ad
internet senza essere proxati

Crea
----

Crea una nuova regola di bypass.

Indirizzo IP
    Indirizzo IP dell'host che non sarà filtrato dal proxy.

Antivirus
=========

Abilita / disabilita la scansione antivirus delle pagine web.

