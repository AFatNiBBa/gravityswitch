
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
    Default:
    <?php foreach($db("SELECT * FROM mappe WHERE JSON_EXTRACT(stile, '$.rnd') <> true", [ $id ]) as $row): ?>
        <a class="item" href="/?page=game/<?= htmlspecialchars(urlencode($row["id"])) ?>"> #<?= htmlspecialchars($row["id"]) ?> </a>
    <?php endforeach ?>
    <a class="item" href="/?page=game/rnd"> Rnd </a>
</div>