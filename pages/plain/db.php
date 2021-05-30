
<?php

$data = json_decode($_GET["data"] ?? "[]", true);
switch ($_PATH)
{
    case "ping":
        $out = "pong";
        break;
    case "confirm":
        //| Registra
        if ($row = @$db("SELECT * FROM utenti WHERE id = ? LIMIT 1", [ $data ])[0])
        {
            if (!$row["attivo"])
            {
                //| Successo
                $db("UPDATE utenti SET attivo = 1 WHERE id = ?", [ $data ]);
                $_SESSION["account"] = $row;
                return £::href("/?");
            }
            else return £::href("/?page=login&active");    # Già attivo
        }
        else return £::href("/?page=login&expired");       # Link scaduto
    case "logout":
        unset($_SESSION["account"]);
        return £::href(-1);
    case "friend":
        $db("INSERT INTO amicizie (a, b) VALUES (?, ?)", [ @$_SESSION["account"]["id"], $data ]);
        return £::href(-1);
    case "friend/confirm":
        $db("UPDATE amicizie SET attivo = 1 WHERE b = ? AND a = ? AND NOT attivo", [ @$_SESSION["account"]["id"], $data ]);
        return £::href(-1);
    case "friend/deny":
        $db("DELETE FROM amicizie WHERE b = ? AND a = ? AND NOT attivo", [ @$_SESSION["account"]["id"], $data ]);
        return £::href(-1);
    case "friend/cancel":
        $db("DELETE FROM amicizie WHERE a = ? AND b = ? AND NOT attivo", [ @$_SESSION["account"]["id"], $data ]);
        return £::href(-1);
    case "friend/delete":
        $db("DELETE FROM amicizie WHERE (@a := ?) <> (@b := ?) AND @a IN (a, b) AND @b IN (a, b) AND attivo", [ @$_SESSION["account"]["id"], $data ] );
        return £::href(-1);
    case "vector3":
        $db("INSERT INTO vettori (x, y, z) VALUES (?, ?, ?)", $data);
        $out = $db->pdo()->lastInsertId();
        break;
    case "mappa/exists":
        $out = $db->int("SELECT COUNT(*) FROM mappe WHERE id = ?", [ $data ]);
        break;
    case "mappa":
        //| Mappa
        $db("INSERT INTO mappe (raggio, giocatore, traguardo, stile, creatore) VALUES (?, ?, ?, ?, ?)", [
            $data["size"],
            $data["player"],
            $data["end"],
            json_encode($data["style"]),
            @$_SESSION["account"]["id"]
        ]);
        $out = $db->pdo()->lastInsertId();

        //| Blocchi
        $i = 0; # Ordine dei blocchi, per rendere il bot funzionante
        foreach($data["blocks"] as $e)
            $db("INSERT INTO blocchi (mappa, vettore, num) VALUES (?, ?, ?)", [ $out, $e, $i++ ]);
        break;
    case "partita":
        $data[4] = @$_SESSION["account"]["id"];
        $out = $db("INSERT INTO partite (mappa, salti, morti, tempo, utente) VALUES (?, ?, ?, ?, ?)", $data);
        break;
}

echo json_encode(@$out);