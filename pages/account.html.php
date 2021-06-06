
<?php
    function stats($user)
    {
        //| Visualizza il numero di livelli creati e di completamenti di un utente
        show([
            [ "Livelli Creati:", $user["generati"], "info" ],
            [ "Partite Giocate:", $user["partite"], "info" ],
            [ "Punteggio:", round($user["punteggio"] * 10000), "success" ]
        ]);
    }

    function show($opts)
	{
		//| Visualizza una serie di dati
		?>
			<table class="w-100 text-left" style="border-collapse: separate; border-spacing: 1em 5px;">
                <?php foreach($opts as $e): ?>
                    <tr>
                        <td> <?= htmlspecialchars($e[0]) ?> </td>
                        <td class="rounded px-2 bg-dark text-<?= $e[2] ?? "primary" ?>">
                            <?= htmlspecialchars($e[1]) ?>
                        </td>
                    </tr>
                <?php endforeach ?>
			</table>
		<?php
	}

    function friend($user, $full = false)
    {
        //| Visualizzazione bottoni amici
        static $config = [
            "attivo" => [[
                "friend/delete",
                "danger",
                "fad fa-user-minus"
            ]],
            "mandata" => [[
                "friend/cancel",
                "warning",
                "fad fa-user-times"
            ]],
            "ricevuta" => [
                [
                    "friend/deny",
                    "warning",
                    "fad fa-user-times"
                ],
                [
                    "friend/confirm",
                    "success",
                    "fad fa-user-check"
                ]
            ],
            null => [[
                "friend",
                "primary",
                "fad fa-user-plus"
            ]]
        ];
        
        $out = $config[$user["amico"]];
        $code = htmlspecialchars(urlencode(json_encode($user["id"])));
        foreach($out as $e)
        {
            ?>
                <a class="m-2 <?= $full ? "w-75" : "" ?> btn btn-<?= $e[1] ?>" href="/?page=plain/db.php/<?= $e[0] ?>&data=<?= $code ?>">
                    <i class="<?= $e[2] ?>"></i>
                </a>
                <?php if($full) echo "<br>" ?>
            <?php
        }
    }

    $loggato = @$_SESSION["account"]["id"];
    $id = ($mappa = @$_GET["mappa"])
    ? $db("SELECT creatore FROM mappe WHERE id = ?", [ $mappa ], PDO::FETCH_COLUMN)[0]
    : (
        isset($_GET["id"])
        ? json_decode($_GET["id"])
        : $loggato
    );
    
    if ($search = isset($_GET["search"]))
        $out = $db($query->sel_search, [ $loggato, "%{$_GET["search"]}%" ]);
    else if ($id)
    {
        if ($user = @$db($query->sel_stats, [ $loggato, $id ])[0])
        {
            $out = $db($query->sel_amici, [ $loggato, $id ]);
            ?>
                <div class="m-5">
                    <span style="font-size: 100px;">
                        <?= $user["nick"] ?>
                    </span>
                    <br>
                    <div class="w-25">
                        <?php stats($user) ?>
                    </div>
                    <br>
                    <?php if($loggato && $loggato != $id) friend($user) ?>
                </div>
            <?php   
        }
        else return assemble("/private/error", [ "code" => 404 ]);
    }

    $_MSG["title"] = [
        "Account",
        $search
        ? "Profili che corrispondono ai criteri di ricerca"
        : ($loggato == $id
            ? "Profilo del proprio utente"
            : "Profilo dell'utente selezionato")
    ];
?>

<script>
    $(() => {
        $("#selected")[0]?.scrollIntoView?.({
            behavior: "smooth",     // Anima il movimento
            block: "center"         // Dove sarà l'elemento dopo lo scroll
        });
    });
</script>

<br>
<?php if ($id): ?>
    <div class="container">
        <h3 class="mb-3">
            <?php if($user): ?>
                Amici:
            <?php else: ?>
                Profili ricercati:
            <?php endif ?>
        </h3>
        <?php if (count($out)): ?>
            <?php for($i = 0, $l = count($out), $sec = 3; $i < $l; $i += $sec): ?>
                <div class="row mb-5">
                    <?php for($k = $i; $k < $i + $sec && $k < $l; $k++): $row = $out[$k]; ?>
                        <div class="col-lg-4 mb-5">
                            <div class="card border-top border-top-lg border-primary lift text-center o-visible h-100">
                                <div class="card-body">
                                    <!-- Icona -->
                                    <div class="icon-stack icon-stack-xl bg-primary-soft text-primary mb-4 mt-n5 z-1 shadow"><i class="fad fa-user"></i></div>
                                    <?php if($loggato && $loggato != $row["id"]): ?>
                                        <!-- Interazioni -->
                                        <div class="float-right">
                                            <div class="dropright position-absolute" style="right: 10px">
                                                <div class="btn" data-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </div>
                                                <div class="dropdown-menu text-center">
                                                    <form>
                                                        <?php friend($row, true) ?>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif ?>
                                    <!-- Nome -->
                                    <h5><?= htmlspecialchars($row["nick"]) ?></h5>
                                    <!-- Statistiche -->
                                    <div class="card-text">
                                        <?php stats($row) ?>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="/?page=account&id=<?= htmlspecialchars(urlencode(json_encode($row["id"]))) ?>" class="card-link text-primary font-weight-bold d-inline-flex align-items-center">
                                        Profilo<i class="fas fa-arrow-right text-xs ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endfor ?>
                </div>
            <?php endfor ?>
        <?php else: ?>
            Non sono stati trovati account<?php if (!$search) echo " amici" ?>.
        <?php endif ?>
    </div>
<?php endif ?>

<?php if(!$search): ?>
<!-- Livelli -->
<div class="container mb-5">
    <h3 class="mb-3"> Livelli: </h3>
    <div class="grow">
        <?php foreach($db("SELECT * FROM ($query->tab_mappe) AS mappe WHERE creatore <=> ?", [ $id ]) as $row): ?>
            <div <?= $row["id"] == $mappa ? 'id="selected" class="bg-warning"' : "" ?>>
                <a class="card-link d-block" href="/?page=game/<?= htmlspecialchars(urlencode($row["id"])) ?>">
                    <span class="text-primary">
                        #<?= htmlspecialchars($row["id"]) ?>
                    </span>
                    <br>
                    <span class="text-black">
                        <?= htmlspecialchars($row["blocchi"]) ?> blocchi
                    </span>
                </a>
            </div>
        <?php endforeach ?>
    </div>
</div>

<!-- Partite -->
<?php if($id): $out = $db("SELECT * FROM ($query->tab_partite) AS partite WHERE utente = ? ORDER BY creazione DESC", [ $id ]) ?>
    <div class="container">
        <h3 class="mb-3"> Partite: </h3>
        <?php if (count($out)): ?>
            <?php for($i = 0, $l = count($out), $sec = 3; $i < $l; $i += $sec): ?>
                <div class="row mb-5">
                    <?php for($k = $i; $k < $i + $sec && $k < $l; $k++): $row = $out[$k]; ?>
                        <div class="col-lg-4 mb-5">
                            <div class="card border-top border-top-lg border-secondary lift text-center o-visible h-100">
                                <div class="card-body">
                                    <!-- Icona -->
                                    <div class="icon-stack icon-stack-xl bg-secondary-soft text-secondary mb-4 mt-n5 z-1 shadow"><i class="fad fa-game-board"></i></div>
                                    <!-- Nome -->
                                    <h5>#<?= htmlspecialchars($row["mappa"]) ?></h5>
                                    <!-- Statistiche -->
                                    <div class="card-text">
                                        <?php show([
                                            [ "Morti:", $row["morti"], "danger" ],
                                            [ "Salti:", $row["salti"], "warning" ],
                                            [ "Tempo:", $row["tempo"], "warning" ],
                                            [ "Punti:", round($row["punteggio"] * 10000), "success" ]
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="/?page=account&mappa=<?= htmlspecialchars(urlencode($row["mappa"])) ?>" class="card-link text-secondary font-weight-bold d-inline-flex align-items-center">
                                        Livello<i class="fas fa-arrow-right text-xs ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endfor ?>
                </div>
            <?php endfor ?>
        <?php else: ?>
            Non sono state trovate partite.
        <?php endif ?>
    </div>
<?php endif ?>
<?php endif ?>