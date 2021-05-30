
<?php 

# Template utilizzato da tutte le query sottostanti
$utenti = <<<SQL
    -- Tabella degli utenti con aggiunto il numero delle partite completate, dei livelli creati, lo stato di amicizia con l'utente loggato (parametro 1) ed il punteggio medio
    SELECT *, (
        -- Partite completate
        SELECT COUNT(*)
        FROM partite
        WHERE partite.utente = utenti.id
    ) AS partite, (
        -- Livelli creati
        SELECT COUNT(*) 
        FROM mappe
        WHERE mappe.creatore = utenti.id
    ) AS generati, (
        -- Stato di attivazione dell'eventuale amicizia, se non vi è mette 'NULL'
        SELECT CASE
                WHEN amicizie.attivo = 0 AND amicizie.a = (@logged := ?) THEN "mandata"
                WHEN amicizie.attivo = 0 AND amicizie.b = @logged THEN "ricevuta"
                ELSE "attivo"
            END
        FROM amicizie
        WHERE 
            @logged <> utenti.id AND
            @logged IN (amicizie.a, amicizie.b) AND
            utenti.id IN (amicizie.a, amicizie.b)
        LIMIT 1
    ) AS amico, (
        -- Punteggio medio dell'utente
        SELECT AVG(
            (
                -- Numero di blocchi della mappa nella partita
                SELECT COUNT(*)
                FROM blocchi
                WHERE blocchi.mappa = partite.mappa
            ) / (morti * 5 + salti * 2 + tempo)
        )
        FROM partite
        WHERE partite.utente = utenti.id
    ) AS punteggio
    FROM utenti
SQL;

# Query particolari
return [
    "del-scaduti" => (<<<SQL
        -- Cancella tutti gli utenti con account scaduti
        DELETE FROM utenti
        WHERE
            attivo = 0 AND                                                  -- Non attivi
            TIME_TO_SEC(TIMEDIFF(CURRENT_TIMESTAMP(), creazione)) > 3600    -- Che hanno la differenza tra l'orario di creazione e l'orario corrente in secondi maggiore di 3600 (Un'ora)
    SQL),

    "sel-amici(2)" => (<<<SQL
        -- Tutti gli utenti che hanno un collegamento di tipo amicizia col profilo selezionato; Se il primo parametro è uguale al secondo allora mostra anche i profili non attivi
        SELECT utenti.*
        FROM ($utenti) AS utenti, amicizie
        WHERE   
            (@a := ?) <> utenti.id AND              -- Selezionato diverso da Corrente; Selezionato è immagazzinato dentro ad "@a" per non doverlo inserire due volte
            (amicizie.attivo OR @a = @logged) AND   -- L'account è attivo o è quello dell'utente loggato
            @a IN (amicizie.a, amicizie.b) AND      -- Selezionato compreso tra le amicizie del collegamento
            utenti.id IN (amicizie.a, amicizie.b)   -- Compreso compreso tra le amicizie del collegamento
        ORDER BY utenti.punteggio DESC
    SQL),

    "sel-search(2)" => (<<<SQL
        -- Tutti gli account che hanno il campo "nick" conforme al pattern in input
        SELECT *
        FROM ($utenti) AS utenti
        WHERE nick LIKE ?
        ORDER BY punteggio DESC
    SQL),

    "sel-stats(2)" => (<<<SQL
        -- Ottiene la tupla dell'utente selezionato
        SELECT *
        FROM ($utenti) AS utenti
        WHERE id = ?
        ORDER BY punteggio DESC
    SQL),

    "count-vettori(1)" => (<<<SQL
        -- Ottiene il numero di vettori utilizzati da ogni livello
        SELECT mappe.id AS mappa, COUNT(*) + 2 AS vettori
        FROM mappe, blocchi, vettori
        WHERE mappe.id = blocchi.mappa AND vettori.id = blocchi.vettore
        GROUP BY mappe.id
    SQL)
];
