
<?php 

//| Query particolari
return [
    "del-scaduti" => (<<<SQL
        -- Cancella tutti gli utenti con account scaduti
        DELETE FROM utenti
        WHERE
            attivo = 0 AND                                                  -- Non attivi
            TIME_TO_SEC(TIMEDIFF(CURRENT_TIMESTAMP(), creazione)) > 3600    -- Che hanno la differenza tra l'orario di creazione e l'orario corrente in secondi maggiore di 3600 (Un'ora)
    SQL),

    "sel-amici(1)" => (<<<SQL
        -- Tutti gli utenti che hanno un collegamento di tipo amicizia col profilo Selezionato; Se "amico" è NULL allora non sono amici
        SELECT utenti.*, (
            -- Partite completate
            SELECT COUNT(*)
            FROM partite
            WHERE utente = utenti.id
        ) AS partite, (
            -- Livelli creati
            SELECT COUNT(*) 
            FROM mappe
            WHERE creatore = utenti.id
        ) AS generati, (
            CASE
                WHEN amicizie.attivo = 0 AND amicizie.a = (@a := ?) THEN "mandata"
                WHEN amicizie.attivo = 0 AND amicizie.b = @a THEN "ricevuta"
                ELSE "attivo"
            END
        ) AS amico
        FROM utenti, amicizie
        WHERE 
            @a <> utenti.id AND                     -- Selezionato diverso da Corrente; Selezionato è immagazzinato dentro ad "@a" per non doverlo inserire due volte
            @a IN (amicizie.a, amicizie.b) AND      -- Selezionato compreso tra le amicizie del collegamento
            utenti.id IN (amicizie.a, amicizie.b)   -- Compreso compreso tra le amicizie del collegamento
    SQL),

    "sel-search(2)" => (<<<SQL
        -- Tutti gli account che hanno il campo "nick" conforme al pattern in input con dettagli sullo stato di amicizia col profilo corrente e se l'eventuale amicizia è attiva
        SELECT *, (
            -- Partite completate
            SELECT COUNT(*)
            FROM partite
            WHERE utente = utenti.id
        ) AS partite, (
            -- Livelli creati
            SELECT COUNT(*) 
            FROM mappe
            WHERE creatore = utenti.id
        ) AS generati, (
            -- Praticamente "sel-amici(1)"
            SELECT CASE
                    WHEN amicizie.attivo = 0 AND amicizie.a = (@a := ?) THEN "mandata"
                    WHEN amicizie.attivo = 0 AND amicizie.b = @a THEN "ricevuta"
                    ELSE "attivo"
                END
            FROM amicizie
            WHERE 
                @a <> utenti.id AND
                @a IN (amicizie.a, amicizie.b) AND
                utenti.id IN (amicizie.a, amicizie.b)
            LIMIT 1
        ) AS amico                                  -- Restituisce lo stato di attivazione dell'eventuale amicizia, se non vi è mette 'NULL'
        FROM utenti
        WHERE nick LIKE ?
    SQL),

    "sel-stats(1)" => (<<<SQL
        -- Ottiene la tupla dell'utente selezionato aggiungendo il numero di partite che ha creato e di partite che ha completato
        SELECT *, (
            -- Partite completate
            SELECT COUNT(*)
            FROM partite
            WHERE utente = utenti.id
        ) AS partite, (
            -- Livelli creati
            SELECT COUNT(*) 
            FROM mappe
            WHERE creatore = utenti.id
        ) AS generati
        FROM utenti 
        WHERE id = ? 
        LIMIT 1
    SQL)
];
