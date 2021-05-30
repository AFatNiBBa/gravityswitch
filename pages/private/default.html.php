
<div class="grow w-50 mx-auto">
    Default:
    <?php foreach($db("SELECT * FROM mappe WHERE JSON_EXTRACT(stile, '$.rnd') <> true", [ $id ]) as $row): ?>
        <a class="item" href="/?page=game/<?= htmlspecialchars(urlencode($row["id"])) ?>"> #<?= htmlspecialchars($row["id"]) ?> </a>
    <?php endforeach ?>
    <a class="item" href="/?page=game/rnd"> Rnd </a>
</div>