
<?php

    function stats($user)
	{
		//| Visualizza il numero di livelli creati e di completamenti di un utente
		?>
			<table style="border-collapse: separate; border-spacing: 1em 5px;">
				<tr>
					<td> Livelli Creati: </td>
					<td class="rounded bg-warning px-2">
						<?= htmlspecialchars($user["generati"]) ?>
					</td>
				</tr>
				<tr>
					<td> Partite Giocate: </td>
					<td class="rounded bg-danger text-white px-2">
						<?= htmlspecialchars($user["partite"]) ?>
					</td>
				</tr>
                <tr>
					<td> Punteggio: </td>
					<td class="rounded bg-info text-white px-2">
						<?= htmlspecialchars(round($user["punteggio"] * 10000)) ?>
					</td>
				</tr>
			</table>
		<?php
	}

    function friend($user)
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
        
        if (@$_SESSION["account"]["id"] != $user["id"])
        {
            $out = $config[$user["amico"]];
            $code = htmlspecialchars(urlencode(json_encode($user["id"])));
            foreach($out as $e)
            {
                ?>
                    <a href="/?page=plain/db.php/<?= $e[0] ?>&data=<?= $code ?>" class="btn btn-<?= $e[1] ?>">
                        <i class="<?= $e[2] ?>"></i>
                    </a>
                <?php
            }
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
                    <?php stats($user) ?>
                    <br>
                    <?php if($loggato) friend($user) ?>
                </div>
                <span class="ml-5 pl-5">Amici:</span>
            <?php   
        }
        else return assemble("/private/error", [ "code" => 404 ]);
    }
?>

<style>
    #friend {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        grid-row-gap: 70px;
    }
</style>

<br>
<?php if ($id): ?>
    <div id="friend" class="px-3 py-5 mt-5 mx-5">
        <?php if (count($out)): ?>
            <?php foreach($out as $row): ?>
                <!-- Card -->
                <div class="card mx-auto r-outer" style="width: 18rem;">
                    <img class="card-img-top" src="utils/res/i/circles.svg">
                    <div class="card-body r-inner">
                        <h5 class="card-title"> <?= $row["nick"] ?> </h5>
                        <p class="card-text">
                            <?php stats($row) ?>
                        </p>
                        <a href="/?page=account&id=<?= htmlspecialchars(urlencode(json_encode($row["id"]))) ?>" class="btn btn-primary">
                            Profilo
                        </a>
                        <?php if($loggato) friend($row) ?>
                    </div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            Non sono stati trovati account<?php if (!$search) echo " amici" ?>.
        <?php endif ?>
    </div>
<?php endif ?>

<script>
    function rotate(outer, inner, angle = 0, os = 1, is = 1) {
        if (inner instanceof jQuery) inner = Array.from(inner);
        const opts = { duration: 1000, elasticity: 300 }
        anime.remove(inner);
        anime.remove(outer);
        anime({ targets: inner, rotateZ: -angle, scale: is });
        anime({ targets: outer, rotateZ: angle, scale: os });
    }

    $(() => {
        $(".r-outer").on('mouseenter', function() {
            const inner = $(this).find(".r-inner");
            rotate(this, inner, 90, 1.5, .8);
            inner.addClass("pl-5");
        });

        $(".r-outer").on('mouseleave', function() {
            const inner = $(this).find(".r-inner");
            rotate(this, inner, 0);
            inner.removeClass("pl-5");
        });

        $("#selected")[0].scrollIntoView({
            behavior: "smooth",     // Anima il movimento
            block: "center"         // Dove sarà dopo lo scroll l'elemento
        });
    });
</script>

<?php if(!$search): ?>
    <div class="w-75 mx-auto">
        <br> Livelli: <br> <br>
        <div class="grow">
            <?php foreach($db("SELECT * FROM ($query->tab_mappe) AS mappe WHERE creatore <=> ?", [ $id ]) as $row): ?>
                <div <?= $row["id"] == $mappa ? 'id="selected" class="bg-warning"' : "" ?>>
                    <a href="/?page=game/<?= htmlspecialchars(urlencode($row["id"])) ?>"> #<?= htmlspecialchars($row["id"]) ?> </a>
                    <br>
                    <?= htmlspecialchars($row["blocchi"]) ?> blocchi
                </div>
            <?php endforeach ?>
        </div>

        <?php if($id): ?>
            <br> Partite: <br> <br>
            <div class="grow">
                <?php foreach($db("SELECT * FROM ($query->tab_partite) AS partite WHERE utente = ? ORDER BY creazione DESC", [ $id ]) as $row): ?>
                    <div class="text-monospace">
                        morti: <?= $row["morti"] ?> <br>
                        salti: <?= $row["salti"] ?> <br>
                        tempo: <?= $row["tempo"] ?>s <br>
                        punti: <?= round($row["punteggio"] * 10000) ?>
                        <a href="/?page=account&mappa=<?= htmlspecialchars(urlencode($row["mappa"])) ?>"> #<?= htmlspecialchars($row["mappa"]) ?> </a>
                    </div>
                <?php endforeach ?>        
            </div>
        <?php endif ?>
    </div>
<?php endif ?>