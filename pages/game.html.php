
<?php 
    if ($_PATH == "")
        if (!($_PATH = @$_GET["id"]))
            return assemble("private/default", [ "mappe" => $query->tab_mappe ]);
    $_MSG["header"] = $_MSG["footer"] = false;

    function vector3($id)
    {
        //| Ottiene la rappresentazione in javascript del vettore che ha come id il contenuto di "$id"
        global $db;
        return json_encode(
            array_map(
                "intval",
                @$db("SELECT x, y, z FROM vettori WHERE id = ? LIMIT 1", [ $id ], PDO::FETCH_NUM)[0]
            )
        );
    }
?>

<!-- Librerie della pagina -->
<div>
    <script src="http://threejs.org/examples/js/controls/OrbitControls.js"></script>    <!-- OrbitControls -->
    <script src="http://unpkg.com/three-spritetext"></script>                           <!-- Text Sprite -->
    <script src="utils/lib/custom/gravity-switch.js"></script>                         <!-- Nucleo del gioco -->
</div>

<!-- Generazione ambiente di gioco -->
<script>
    const anim = new Manager.Lock();
    const $l = parseInt(<?= json_encode($_PATH) ?>);

    class Level {
        //| Livello di gioco
        constructor(opts) {
            Object.assign(this, opts);
            this.size ??= 15;
            this.start = this.player ??= [0, 0, 0];
            this.blocks ??= [];

            const style = this.style ??= {};
            style.lights ??= [ [-15, -15, -15], [15, 15, 15] ];
            const colors = style.colors ??= {};
            colors.end ??= 0x123456;
            colors.win ??= 0x0000ff;
            colors.lose ??= 0xff0000;
            colors.base ??= 0xffCC00;
            colors.touch ??= 0x00ff00;
            colors.lights ??= 0xffffff;

            const player = colors.player ??= {};
            player.body ??= 0xeeeeee;
            player.edge ??= 0x000000;
        }

        reset()
        {
            this.player.position.set(...this.start);
            this.player.material.color.set(this.style.colors.player.body);
        }

        //| Autogenerazione livello
        static generate(out = {})
        {
            //| Verifica se "middle" è in mezzo a "left" e "right"
            function middle(left, middle, right)
            {
                //              ↓ La dimensione variata tra due blocchi è contenuta nel secondo
                const { dim } = right;
                const nd1 = right.nd1 ??= (dim + 1) % 3;
                const nd2 = right.nd2 ??= (dim + 2) % 3;
                return ( //                    ↑ Non posso fare '-1' perchè il '%' è il "reminder" non il "mod"
                    //                        ↓ Controlla che sia nello stesso piano
                    middle[nd1] == right[nd1] &&
                    middle[nd2] == right[nd2] &&
                    ( //           ↑ Non si può usare "left" poichè è sfasato, questo perchè il personaggio non si ferma sul blocco, ma una posizione prima: Quindi il vero "left" sarebbe la posizione del personaggio quando ci va a sbattere
                        //                                                    ↓ Controlla che sia in mezzo
                        (left[dim] < middle[dim] && middle[dim] < right[dim]) ||
                        (right[dim] < middle[dim] && middle[dim] < left[dim])
                    )
                );
            }

            //| Presettaggi
            out.player ??= [];
            const length = random(4, 30);
            const { player, blocks } = out = new Level(out);
            out.style.rnd = true;
            for (let i = 0; i < 3; i++)
                player.push(random(-out.size, out.size));

            //| Definizioni
            f: for (let i = 0, prec = player; i < length; i++)
            {
                const temp = prec.slice();
                if (prec.dim != null) temp[prec.dim] -= Math.sign(prec.size);

                //| Nuova dimensione
                temp.dim = random(0, 2);
                if (temp.dim == prec.dim) temp.dim = (temp.dim + 1) % 3;

                //| Variazione
                do
                    temp.size = random(0, out.size - 1) * (!random(0, 1) ? 1 : -1);
                while(Math.abs(temp[temp.dim] - temp.size) < 2);  // Minimo due di distanza
                [temp.size, temp[temp.dim]] = [temp.size - temp[temp.dim], temp.size];

                //| Controllo:   [prec] -> [$k] -> [new]
                for (let k = 0; k < blocks.length - 1; k++)
                {
                    if (middle(prec, blocks[k], temp))
                    {
                        //| Ricreazione blocco corrente
                        i--;
                        continue f;
                    }
                }

                //| Controllo:   [$k-1] -> [new] -> [$k]
                for (let k = 0; k < blocks.length - 1; k++)
                {
                    if (middle(blocks[k], temp, blocks[k + 1]))
                    {
                        //| Ricreazione blocco corrente
                        i--;
                        continue f;
                    }
                }

                blocks.push(prec = temp);
            }
            out.end = blocks.pop();
            return out;
        }
    }

    $(function() {
        //| Inizializzazione
        const app = window.app = new App(
            anim,
            $("#root")[0]
            <?php if ($row = @$db("SELECT * FROM mappe WHERE id = ?", [ $_PATH ])[0]): ?>
                //| Livello
                , new Level({
                    size: <?= $row["raggio"] ?>,
                    player: <?= vector3($row["giocatore"]) ?>,
                    end: <?= vector3($row["traguardo"]) ?>,
                    style: <?= $row["stile"] ?>,
                    blocks: [
                        <?php foreach($db("SELECT vettore FROM blocchi WHERE mappa = ? ORDER BY num ASC", [ $row["id"] ]) as $e): ?>
                            <?= vector3($e["vettore"]) ?>,
                        <?php endforeach ?>
                    ]
                })
            <?php else: ?>
                //| Livello Autogenerato
                , Level.generate()
            <?php endif ?>    
        );

        //| Click di default del modal (Morte)
        $("#msg").click(() => {
            app.level.reset();
            $("#msg").modal("hide");
        });

        //| Skybox universo (+-x, +-y, +-z)
        if (storage["background"]) new THREE.CubeTextureLoader().load([
            <?php foreach([ "right", "left", "top", "bottom", "front", "back" ] as $e): ?>
                "utils/res/i/skybox/<?= $e ?>.png",
            <?php endforeach ?>
        ], x => app.mng.scene.background = x);
    });
</script>

<!-- Messaggio di Vittoria/Sconfitta -->
<div id="msg" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white bg-danger">
                <h5 class="modal-title"> HAI PERSO! </h5>
            </div>
            <div class="modal-body">
                <span id="stats" class="d-none text-monospace">
                    Salti: <span id="salti" class="text-info"></span> <br>
                    Morti: <span id="morti" class="text-info"></span> <br>
                    Tempo: <span class="text-info">
                        <span id="tempo"></span>s
                    </span>
                    <br>
                    Punti: <span id="punti" class="text-success">???</span> <br>
                    Mappa: <a id="mappa" href="/?page=game/<?= $temp = htmlentities($_PATH) ?>">#<?= $temp ?></a>
                    <br>
                </span>
                Clicca per continuare...
            </div>
        </div>
    </div>
</div>

<!-- Impostazioni -->
<div id="opts" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title"> Opzioni </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="custom-control custom-switch">
                    <input id="background" type="checkbox" class="custom-control-input">
                    <label for="background" class="custom-control-label"> Sfondo </label>
                </div>
                <div class="custom-control custom-switch">
                    <input id="fixed" type="checkbox" class="custom-control-input">
                    <label for="fixed" class="custom-control-label"> Camera fissa </label>
                </div>
                <div class="custom-control custom-switch">
                    <input id="consigli" type="checkbox" class="custom-control-input" onchange='$("#consigli-full").attr("disabled", !event.target.checked)'>
                    <label for="consigli" class="custom-control-label"> Consigli </label>
                </div>
                <div class="custom-control custom-switch">
                    <input id="consigli-full" disabled type="checkbox" class="custom-control-input">
                    <label for="consigli-full" class="custom-control-label"> Consigli Completi </label>
                </div>
            </div>
            <div class="modal-footer">
                <?php if($user = @$_SESSION["account"]["nick"]): ?>
                    <a class="btn bg-white float-right" href="/?page=account">
                        <i class="fad fa-user"></i>
                        <?= $user ?>
                    </a>
                <?php endif ?>
                <a class="btn btn-danger" href="/?page=main"> Home </a>
                <a class="btn btn-warning" href="/?page=game"> Scelta livelli </a>
                <button type="button" class="btn btn-primary" onclick='
                    ["background", "fixed", "consigli", "consigli-full"].forEach(k =>
                        storage[k] = $(`#${ k }`)[0].checked
                    );
                    window.location.reload();
                '> Applica </button>
            </div>
        </div>
    </div>
</div>