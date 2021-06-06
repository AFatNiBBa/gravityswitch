
<?php $_MSG["title"] = [ "Livelli Standard", "Livelli fatti a mano dallo sviluppatore del gioco!" ] ?>

<br>
<div class="w-75 mx-auto">
    <form action="/?">
        <?php £::field("id", "number", "Inserisci codice livello", "fad fa-layer-group", "8") ?>
        <input type="hidden" name="page" value="game">
        <button type="submit" class="btn btn-success w-100"> Vai </button>
    </form>

    <br> Default: <br> <br>
    <div class="grow">
        <?php foreach($db("SELECT * FROM ($mappe) AS mappe WHERE JSON_EXTRACT(stile, '$.rnd') <> true") as $row): ?>
            <div>
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
        <div>
            <a class="card-link d-block" href="/?page=game/rnd">
                <span class="text-primary"> Rnd </span>
                <br>
                ? blocchi
            </a>
        </div>
    </div>
</div>