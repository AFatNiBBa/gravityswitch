
<?php

use function PHPSTORM_META\map;

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

    $id = $_GET["id"] ?? $_SESSION["account"]["id"];
    $user = @$db($query["sel-stats(1)"], [ $id ])[0];

    if ($search = isset($_GET["search"]))
        $out = $db($query["sel-search(2)"], [ $id, "%{$_GET["search"]}%" ]);
    else
    {
        $out = $db($query["sel-amici(1)"], [ $id ]);
        ?>
            <div class="m-5">
                <span style="font-size: 100px;">
                    <?= $user["nick"] ?>
                </span>
                <br>
                <?php stats($user) ?>
                <br>
                <?php friend($user) ?>
            </div>
        <?php
    }
?>

<style>
    #level {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        grid-row-gap: 70px;
    }
</style>

<br>
<div id="level" class="px-3 py-5 mt-5 mx-5">
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
                    <a href="/?page=account&id=<?= htmlspecialchars(urlencode($row["id"])) ?>" class="btn btn-primary">
                        Profilo
                    </a>
                    <?php friend($row) ?>
                </div>
            </div>
        <?php endforeach ?>
    <?php else: ?>
        Non sono stati trovati account<?php if (!$search) echo " amici" ?>.
    <?php endif ?>
</div>

<script>
    function rotate(outer, inner, angle = 0, os = 1, is = 1) {
        if (inner instanceof jQuery) inner = Array.from(inner);
        const opts = { duration: 1000, elasticity: 300 }
        anime.remove(inner);
        anime.remove(outer);
        anime({ targets: inner, rotateZ: -angle, scale: is });
        anime({ targets: outer, rotateZ: angle, scale: os });
    }

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
</script>

<?php if(!$search): ?>
    <style>
        #wrapper {
            display: grid;
            grid-template-columns: 1fr;
            row-gap: 30px;
        }

        .item {
            display: grid;
            grid-template-columns: 1fr 3fr 1fr;
            align-items: center;
            padding: 10px 30px 10px 10px;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 5px 7px -1px rgba(51, 51, 51, 0.23);
            cursor: pointer;
            transition: transform 0.25s cubic-bezier(0.7, 0.98, 0.86, 0.98), box-shadow 0.25s cubic-bezier(0.7, 0.98, 0.86, 0.98);
            background-color: #fff;
        }

        .item:hover {
            transform: scale(1.1);
            box-shadow: 0 9px 47px 11px rgba(51, 51, 51, 0.18);
        }
    </style>

    <div id="wrapper" class="w-50 mx-auto">
        Livelli:
        <?php foreach($db("SELECT * FROM mappe WHERE creatore = ?", [ $id ]) as $row): ?>
            <a class="item" href="/?page=game/<?= htmlspecialchars(urlencode($row["id"])) ?>"> #<?= htmlspecialchars($row["id"]) ?> </a>
        <?php endforeach ?>
    </div>
<?php endif ?>