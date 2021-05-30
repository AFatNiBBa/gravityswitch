
<?php

/*
    v26
    [WIP]: La skybox non fa più
    [MAY]: "#wrapper" e ".item" di base (include.html.php)
    [WIP]: Rifà stile
        [WIP]: Colore bottone login adattato
    [MAY]: Niente ricarica pagina per le opzioni (Inserisci id livello)
    [WIP]: game.html.php senza livello fai accedere ai sette di base ed a "rnd" (private/default.html.php)
    
    [WIP]: Account
        [WIP]: Partite giocate
        [WIP]: Classifiche [partite/(morti*5+salti*2+tempo)] no nome
            [WIP]: Grafici
*/

session_start();
include __DIR__ . "/utils/global.php";

try
{
    //| Inizializzazione
    global $db, $page, $localhost, $altervista;
    $altervista = "dump";
    $localhost = in_array($_SERVER["SERVER_ADDR"], [$cell = "0.0.0.0", "127.0.0.1", "::1"]);
    $localhost += $_SERVER["SERVER_ADDR"] == $cell;
    $page = rtrim(preg_replace("/\.\.\//", "", $_GET["page"] ?? ""), '/');
    $db = new Cacher([
        "host" => $localhost != 2
            ? "localhost"
            : $cell,
        "pass" => $localhost
            ? ($localhost == 2
                ? "root"
                : "")
            : "",
        "user" => $localhost
            ? "root"
            : $altervista,
        "db" => $localhost
            ? "a"
            : "my_$altervista"
    ]);
    
    //| Reindirizzamento; Mette il template se non è in "plain/" o c'è ma non esiste; Da errore se prova ad accedere a "private/"
    if (preg_match("/^private\/.+/", $page))
        assemble("private/template/base", [ "page" => "private/error", "code" => 404 ]);
    else if (!preg_match("/^plain\/.+/", $page) || !assemble($page, [], ""))
        assemble("private/template/base", [ "page" => $page ]);
}
catch (Exception $e)
{
    // echo $e->getMessage() . ' in ' . $e->getFile() . ': ' . $e->getLine();
    throw $e;
}