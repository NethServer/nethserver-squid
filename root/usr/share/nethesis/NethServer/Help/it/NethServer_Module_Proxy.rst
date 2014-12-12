=========
Proxy web
=========

Configura il proxy web (Squid) e le regole di bypass.

Proxy
=====

Il proxy web lavora per ridurre l'utilizzo della banda facendo cache
delle pagine visitate. Può anche forzare il filtraggio dei contenuti.

Il proxy web può essere abilitato solo sulle zone green (reti locali) e blue (reti ospiti).

Abilitato
    Abilita/disabilita il proxy.

Manuale
    Squid è in ascolto sulla porta 3128.
    Tutti i client devono essere esplicitamente configurati per utilizzare il proxy.

Autenticato
    Ad ogni utente sarà richiesto di inserire nome utente e password.
    Squid è in ascolto sulla porta 3128.
    Tutti i client devono essere esplicitamente configurati per utilizzare il proxy.

Trasparente
    Tutto il traffico HTTP verrà rediretto attraverso il proxy.
    Non è necessario effettuare nessuna configurazione sui client.

Trasparente con SSL
    Tutto il traffico HTTP e HTTPS verrà rediretto attraverso il proxy.
    Il certificato del server (CA) deve essere installato in ogni client per permettere
    il traffico HTTPS.

Blocca porte HTTP e HTTPS
    Se abilitato, i client non potranno bypassare il proxy.
    Le porte 80 e 443 saranno raggiungibili solo utilizzando il proxy.

Proxy di secondo livello
    Inserire l'IP e la porta del parent proxy. La sintassi corretta è
    Indirizzo_IP:porta.
    *NON inserire* l'indirizzo IP di questo server.

Host senza proxy
================

Configura alcuni IP per bypassare il proxy trasparente ed accedere ad
internet senza essere filtrati.

Nome
    Nome univoco per la regola di bypass.

Abilitato
    Abilita o disabilita la regola di bypass.

Sorgente
    Seleziona un host tra le macchine locale o gli oggetti del firewall.

Descrizione
    Descrizione personalizzata (opzionale).

Siti senza proxy
================

Configura siti remoti che devono essere acceduti direttamente.
Utile nel caso di siti mal scritti che non funzionano correttamente con il proxy.

Nome
    Nome univoco per la regola di bypass.

Abilitato
    Abilita o disabilita la regola di bypass.

Destinazione
    Seleziona un host tra le macchine remote o gli oggetti del firewall.

Descrizione
    Descrizione personalizzata (opzionale).

