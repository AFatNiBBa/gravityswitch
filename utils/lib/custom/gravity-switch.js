
const center = new THREE.Vector3();
const size3D = (size) => new THREE.Vector3(size, size, size).distanceTo(center);
const round = (num, dec = 0) => Math.round(num * (dec = 10 ** dec)) / dec;
const random = (min, max) => Math.floor(Math.random() * (max != undefined ? (max - min + 1) : min + 1)) + (max != undefined ? min : 0);
const storage = new Proxy(() => { }, {
    get: (t, k) => JSON.parse(localStorage.getItem(k)),
    set: (t, k, v) => localStorage.setItem(k, JSON.stringify(v)) || true,
    deleteProperty: (t, k) => localStorage.removeItem(k),
    apply: (t, self, [opts]) => Object.assign(storage, opts)
});

class App
{
    static c_mesh(color)
    {
        //| Crea un cubo standard col colore specificato
        return new THREE.Mesh(
            new THREE.BoxGeometry(1, 1, 1),
            new THREE.MeshLambertMaterial({ color, transparent: true })
        );
    }

    static c_axis(player, p, keys)
    {
        //| Posizione
        const vec = new THREE.Vector3(...Object.assign(
            [0, 0, 0], 
            { [Math.abs(p) - 1]: Math.sign(p) }
        ));

        //| Assi
        const axis = new THREE.LineSegments(
            new THREE.BufferGeometry().setFromPoints([
                new THREE.Vector3(0, 0, 0),
                vec
            ]),
            //| Colore
            new THREE.LineBasicMaterial({ color: 0xff0000 >> (8 * (Math.abs(p) - 1)) })
        );
        player.add(axis);

        //| Tasti
        keys.forEach((x, i) => {
            if (!storage["consigli"] || (!storage["consigli-full"] && i)) return;
            const temp = new SpriteText(x);
            axis.add(temp);
            temp.backgroundColor = "rgba(20, 20, 20, .8)";
            temp.scale.multiplyScalar(.5 ** 5);
            temp.position.set(...vec.clone().multiplyScalar( -(2 * i) + 1 ).toArray());
        });
    }

    //| Imposta la scena
    constructor(lock, root, level)
    {
        this.lock = lock;
        this.level = level;
        this.template = JSON.stringify(level);

        const mng = this.mng = new Manager().mouse().apply(root);
        const { scene, renderer, camera } = mng;
        mng.loop.push(this.loop.bind(this));

        //| Handler pressione tasti
        window.addEventListener("keydown", this.press.bind(this));

        //| Impostazione statistiche
        this.stats = {
            salti: 0,
            morti: 0,
            get tempo() {
                return this.lastTime = round(mng.clock.elapsedTime, 2);
            }
        };

        //| Traguardo
        {
            const e = App.c_mesh(level.style.colors.end);
            e.position.set(...level.end);
            level.end = e;
            scene.add(e);
        }

        //| Giocatore
        {
            const e = App.c_mesh(level.style.colors.player.body).edges(level.style.colors.player.edge);
            App.c_axis(e, +1, [ "D", "A" ]);
            App.c_axis(e, +2, [ "Space", "Ctrl" ]);
            App.c_axis(e, -3, [ "W", "S" ]);
            e.position.set(...level.player);
            level.player = e;
            scene.add(e);
            
            const temp = storage["fixed"];
            Object.assign(mng.orbit, {
                enablePan: !temp,
                target: temp
                ? e.position
                : e.position.clone() // Punta la telecamera verso il giocatore; Se il 'Vector3' non viene clonato continuerà sempre a puntarlo
            });
        }

        //| Muri
        for (let i = 0; i < level.blocks.length; i++)
        {
            const e = App.c_mesh(level.style.colors.base);
            e.position.set(...level.blocks[i]);
            level.blocks[i] = e;
            scene.add(e);
        }

        //| Luci
        for (let i = 0; i < level.style.lights.length; i++)
        {
            const e = new THREE.PointLight(level.style.colors.lights, 1.25, 500);
            e.position.set(...level.style.lights[i]);
            level.style.lights[i] = e;
            scene.add(e);
        }
    }

    //| Imposta il colore del giocatore a seconda della sua posizione
    loop()
    {
        const { level } = this;
        for (const e of level.blocks) e.material.color.set(
            level.style.colors[
                level.player.position.distanceTo(e.position) == 1
                ? "touch"
                : "base"
            ]
        );
    }

    //| Gestisce la pressione dei tasti
    press(e)
    {
        const v = 1;
        const code = e.key.toLowerCase();
        if (code == "escape")
        {
            $("#opts").modal("show");
            ["background", "fixed", "consigli", "consigli-full"].forEach(k => {
                $(`#${ k }`)[0].checked = storage[k] ?? false;
                if (k == "consigli-full")
                    $("#consigli-full").attr("disabled", !storage["consigli"]);
            });
        }
        else this.move({
            w:          [,,-v],
            a:          [-v],
            s:          [,,v],
            d:          [v],
            " ":        [,v],
            control:    [,-v]
        }[code]);
    }

    //| Muove il giocatore nella direzione impostata nell'array "velocity", avanto o indietro a seconda del segno dell'elemento
    move(velocity)
    {
        const { level, level: { player } } = this;
        if (!velocity || !this.lock.completed || player.position.distanceTo(level.end.position) == 0) return false;
        const temp = player.position.clone();
        velocity = new THREE.Vector3(...velocity);
        w: while(temp.distanceTo(center) <= size3D(level.size) && temp.distanceTo(level.end.position) > 0)
        {
            temp.add(velocity);
            for(const { position } of level.blocks)
            {
                if (temp.distanceTo(position) == 0)
                {
                    temp.sub(velocity);
                    break w;
                }
            }
        }

        //| Controllo vittoria o perdita e relative modifiche delle statistiche
        this.stats.salti++;
        if (temp.distanceTo(level.end.position) == 0)
        {
            player.material.color.set(level.style.colors.win);
            $("#msg .modal-header").removeClass("bg-danger").addClass("bg-success").find("h5").text("HAI VINTO!");
            $("#stats").removeClass("d-none");

            //| Visualizzazione statistiche
            for (const k of [ "salti", "morti", "tempo" ])
                $(`#${ k }`).text(this.stats?.[k] ?? "???");
            $(`#punti`).text(Math.round((this.level.blocks.length / (this.stats.morti * 5 + this.stats.salti * 2 + this.stats.lastTime)) * 10000));

            //| Salvataggio statistiche
            this.save(window.template).catch(() => console.log("Esegui l'accesso per salvare le tue statistiche."));

            $("#msg").modal({
                backdrop: 'static',
                keyboard: false
            }).click(() => {
                window.location.href = `/?page=game/${ encodeURI(isNaN($l) ? "rnd" : $l + 1) }`;
            });
        }
        else if (temp.distanceTo(center) > size3D(level.size))
        {
            //| Reset statistiche dopo la morte
            this.stats.morti++;
            this.stats.salti = 0;

            player.material.color.set(level.style.colors.lose);
            $("#msg").modal({
                backdrop: 'static',
                keyboard: false
            });
        }
        
        //| Animazione verso la posizione calcolata
        return Manager.animation(player.position, 100, { ...temp }, this.lock);
    }

    //| Manda le statistiche di completamento del livello, se non si è loggati da errore
    async save(id = $l)
    {
        const mappa = await DB.send("mappa/exists", id)
        ? id
        : await DB.send(
            "mappa",
            await JSON.parse(this.template).but(async x => {
                x.player = await DB.send("vector3", x.player);
                x.end = await DB.send("vector3", x.end);
                x.blocks = await Promise.all(
                    x.blocks.map(y =>
                        DB.send("vector3", y)
                    )
                );
            })
        );
        
        $("#mappa").text("#" + mappa).attr("href", `/?page=game/${ encodeURI(mappa) }`);
        console.log({
            mappa,
            partita: !!await DB.send("partita", [ mappa, this.stats.salti, this.stats.morti, this.stats.lastTime ])
        });
    }
}