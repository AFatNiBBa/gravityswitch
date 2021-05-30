
<br>
<div class="grow w-50 mx-auto">
    <form action="/?">
        <?php £::field("id", "number", "Inserisci codice livello", "fad fa-layer-group", "8") ?>
        <input type="hidden" name="page" value="game">
        <button type="submit" class="btn btn-success w-100"> Vai </button>
    </form>

    Default:
    <?php foreach($db("SELECT * FROM mappe WHERE JSON_EXTRACT(stile, '$.rnd') <> true", [ $id ]) as $row): ?>
        <a class="item" href="/?page=game/<?= htmlspecialchars(urlencode($row["id"])) ?>"> #<?= htmlspecialchars($row["id"]) ?> </a>
    <?php endforeach ?>
    <a class="item" href="/?page=game/rnd"> Rnd </a>
</div>