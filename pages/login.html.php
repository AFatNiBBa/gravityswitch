
<?php
    //| Rimozione account scaduti
    $db("DELETE FROM utenti WHERE attivo = 0 AND TIME_TO_SEC(TIMEDIFF(CURRENT_TIMESTAMP(), creazione)) > 3600");

    $nick = @$_POST["nick"];
    $user = @$_POST["user"];
    $pass = @$_POST["pass"];
    $register = $_PATH == "register";
?>

<style>
    .container > form {
        --pad: 100px;
        padding-top: var(--pad);
        padding-bottom: var(--pad);
    }
</style>

<!-- Form -->
<div class="container w-25 my-15" style="min-width: 530px">
    <form method="post">
        <center>
            <!-- Logo -->
            <img src="utils/res/logo.png" style="width: 60px; height: 60px; position: relative; bottom: 10px;">
            <span class="h1">
                <span class="fw-fat">BigBlack</span><span class="fw-thin">Death</span>
            </span>
            <!-- Operazioni -->
            <?php
                if (isset($user, $pass) && (!$register || $nick))
                {
                    if ($register)
                    {
                        //| Registrazione
                        if ($db->int("SELECT COUNT(*) FROM utenti WHERE email = ? LIMIT 1", [ $user ]))
                            £::alert("fad fa-user-friends", "Utente <b>già registrato</b>.", "warning"); # Utente già registrato
                        else
                        {
                            $config = array_map(function ($x) use($pass) {
                                $x[0] = preg_match($x[0], $pass);
                                return $x;
                            }, [
                                [ "/[a-z]/", "Lettera minuscola" ],
                                [ "/[A-Z]/", "Lettera maiuscola" ],
                                [ "/[0-9]/", "Numero" ],
                                [ "/\W(?<!\s)/", "Simbolo" ],
                                [ "/.{8,}/", "Almeno di 8 caratteri" ]
                            ]);

                            if (count(array_filter($config, function ($x) { return !$x[0]; })) > 0)
                                //| Password troppo semplice
                                £::alert("fad fa-user-unlock", ob::func(function() use($config) {
                                    ?>
                                        La password inserita è <b>troppo semplice</b>.
                                        <div class="text-left">
                                            <?php foreach($config as $e): ?>
                                                <i class="fad <?= $e[0] ? "fa-check-circle text-success" : "fa-times-circle text-danger" ?>"></i>
                                                <?= $e[1] ?>
                                                <br>
                                            <?php endforeach ?>
                                        </div>
                                    <?php
                                })(), "warning");
                            else
                            {
                                //| Successo
                                $id = uniqid();
                                if (!$db("INSERT INTO utenti (id, nick, email, pass, creazione, attivo) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP(), 0)", [ $id, $nick, $user, password_hash($pass, PASSWORD_DEFAULT) ]))
                                    £::alert("fad fa-bug", "Si è verificato un <b>errore</b> anomalo.", "danger"); # Errore anomalo
                                else if (
                                    !send(function() use($id) {
                                        assemble("/private/mail", [
                                            "link" => "{$_SERVER["HTTP_X_FORWARDED_PROTO"]}://{$_SERVER["HTTP_HOST"]}/?page=plain/db.php/confirm&data=" . urlencode(json_encode($id))
                                        ]);
                                    }, $user, "BigBlackDeath", "Conferma Email")
                                ) £::alert("fad fa-exclamation-circle", "Il <b>server</b> dove è contenuto il sito non ha ben configurate le impostazioni delle <b>email</b>.", "info"); # Server non configurato per le email
                                else return £::href("/?page=login&registered");
                            }
                        }
                    }
                    else
                    {
                        //| Login
                        if ($row = @$db("SELECT * FROM utenti WHERE email = ? LIMIT 1", [ $user ])[0])
                        {
                            if (password_verify($pass, $row["pass"]))
                            {
                                if ($row["attivo"])
                                {
                                    //| Successo
                                    $_SESSION["account"] = $row;
                                    return £::href("/?"); # Pagina precedente
                                }
                                else £::alert("fad fa-qrcode", "Profilo <b>non attivo</b>.", "warning"); # Profilo non attivo
                            }
                            else £::alert("fad fa-key", "Password <b>errata</b>.", "danger"); # Password errata
                        }
                        else £::alert("fad fa-user-slash", "Utente <b>non trovato</b>.", "danger"); # Utente non trovato
                    }
                }
                else if (isset($_GET["registered"]))
                    £::alert("fad fa-at", "Registrazione effettuata, verifica se ti è arrivata la <b>email di conferma</b>. Hai tempo un'ora.", "success"); # Conferma di riuscita registrazione
                else if (isset($_GET["expired"]))
                    £::alert("fad fa-do-not-enter", "Il <b>codice</b> utilizzato è scaduto o <b>invalido</b>, ricrea il tuo account.", "danger"); # Codice invalido
                else if (isset($_GET["active"]))
                    £::alert("fad fa-info-circle", "L'account è <b>già attivo</b>, esegui il login.", "info"); # Codice già utilizzato
            ?>
        </center>
        <?php if($register) £::field("nick", "text", "Nome Utente", "fad fa-user", "beppino16") ?>
        <?php £::field("user", "email", "Email", "fad fa-at", "utente@gmail.com") ?>
        <?php £::field("pass", "password", "Password", "fad fa-eye-slash", "segretO#123") ?>
        <button type="submit" class="btn btn-success w-100">
            <?= $register ? "Registrati" : "Accedi" ?>
        </button>
    </form>
</div>