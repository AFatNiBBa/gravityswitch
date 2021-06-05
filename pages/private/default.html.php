
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
                <a href="/?page=game/<?= htmlspecialchars(urlencode($row["id"])) ?>"> #<?= htmlspecialchars($row["id"]) ?> </a>
                <br>
                <?= htmlspecialchars($row["blocchi"]) ?> blocchi
            </div>
        <?php endforeach ?>
        <div>
            <a href="/?page=game/rnd"> Rnd </a>
            <br>
            ? blocchi
        </div>
    </div>
</div>