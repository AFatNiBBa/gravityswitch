
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
			</table>
		<?php
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
                    <?php if ($_SESSION["account"]["id"] != $row["id"]): ?>
                        <!-- Interazioni con profili che non sono il proprio -->
                        <?php $code = htmlspecialchars(urlencode(json_encode($row["id"]))) ?>
                        <?php if($row["amico"] == "attivo"): ?>
                            <a href="/?page=plain/db.php/friend/delete&data=<?= $code ?>" class="btn btn-danger">
                                <i class="fad fa-user-minus"></i>
                            </a>
                        <?php elseif($row["amico"] == "mandata"): ?>
                            <a href="/?page=plain/db.php/friend/cancel&data=<?= $code ?>" class="btn btn-warning">
                                <i class="fad fa-user-times"></i>
                            </a>
                        <?php elseif($row["amico"] == "ricevuta"): ?>
                            <a href="/?page=plain/db.php/friend/deny&data=<?= $code ?>" class="btn btn-warning">
                                <i class="fad fa-user-times"></i>
                            </a>
                            <a href="/?page=plain/db.php/friend/confirm&data=<?= $code ?>" class="btn btn-success">
                                <i class="fad fa-user-check"></i>
                            </a>
                        <?php elseif($search && $row["amico"] == null): ?>
                            <a href="/?page=plain/db.php/friend&data=<?= $code ?>" class="btn btn-primary">
                                <i class="fad fa-user-plus"></i>
                            </a>
                        <?php endif ?>
                    <?php endif ?>
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
    Livelli:
    <table>
        <?php foreach($db("SELECT * FROM mappe WHERE creatore = ?", [ $id ]) as $row): ?>
            <tr>
                <td>
                    <a href="/?page=game/<?= htmlspecialchars(urlencode($row["id"])) ?>"> #<?= htmlspecialchars($row["id"]) ?> </a>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>