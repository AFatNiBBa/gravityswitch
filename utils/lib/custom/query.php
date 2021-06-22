
<?php 

$out = new stdClass();

$out->tab_mappe = <<<SQL
    -- Tabella delle mappe con aggiunto il numero di blocchi
    SELECT mappe.*, COUNT(*) AS blocchi
    FROM mappe, blocchi
    WHERE mappe.id = blocchi.mappa
    GROUP BY mappe.id
SQL;

$out->tab_partite = <<<SQL
    -- Tabella delle partite con aggiunto il punteggio
    SELECT partite.*, ( COUNT(*) / ( partite.morti*5 + partite.salti*2 + partite.tempo ) ) AS punteggio
    FROM partite, blocchi
    WHERE partite.mappa = blocchi.mappa
    GROUP BY partite.id
SQL;

$out->tab_utenti = <<<SQL
    -- Tabella degli utenti attivi con aggiunto il numero delle partite completate, dei livelli creati, lo stato di amicizia con l'utente loggato (parametro 1) ed il punteggio medio
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
        SELECT
            CASE
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
        SELECT AVG(partite.punteggio)
        FROM ($out->tab_partite) AS partite
        WHERE partite.utente = utenti.id
    ) AS punteggio
    FROM utenti
    WHERE utenti.attivo = 1
SQL;

$out->sel_amici = <<<SQL
    -- Tutti gli utenti che hanno un collegamento di tipo amicizia col profilo selezionato; Se il primo parametro è uguale al secondo allora mostra anche i profili non attivi
    SELECT utenti.*
    FROM ($out->tab_utenti) AS utenti, amicizie
    WHERE   
        (@a := ?) <> utenti.id AND              -- Selezionato diverso da Corrente; Selezionato è immagazzinato dentro ad "@a" per non doverlo inserire due volte
        (amicizie.attivo OR @a = @logged) AND   -- L'account è attivo o è quello dell'utente loggato
        @a IN (amicizie.a, amicizie.b) AND      -- Selezionato compreso tra le amicizie del collegamento
        utenti.id IN (amicizie.a, amicizie.b)   -- Compreso compreso tra le amicizie del collegamento
    ORDER BY utenti.punteggio DESC
SQL;

$out->sel_search = <<<SQL
    -- Tutti gli account che hanno il campo "nick" conforme al pattern in input
    SELECT *
    FROM ($out->tab_utenti) AS utenti
    WHERE nick LIKE ?
    ORDER BY punteggio DESC
SQL;

$out->sel_stats = <<<SQL
    -- Ottiene la tupla dell'utente selezionato
    SELECT *
    FROM ($out->tab_utenti) AS utenti
    WHERE id = ?
    ORDER BY punteggio DESC
SQL;

$out->count_vettori = <<<SQL
    -- Ottiene il numero di vettori utilizzati da ogni livello
    SELECT mappe.id AS mappa, COUNT(*) + 2 AS vettori
    FROM mappe, blocchi, vettori
    WHERE mappe.id = blocchi.mappa AND vettori.id = blocchi.vettore
    GROUP BY mappe.id
SQL;

return $out;
