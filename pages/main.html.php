
<style>
    #blob {
        background-image: url("utils/res/i/blob.svg");
        height: 500px;

        display: grid;
        place-items: center;
    }
</style>

<script>
    $(function() {
        const temp = [...$(".out-left, .out-right")];
        Manager.sequence([
            [
                [ temp[0], 0, { translateX: "-1000" } ],
                [ temp[1], 0, { translateX: "1000" } ],
            ],
            [
                [ temp, 1000, , new Manager.Lock({ translateX: "0" }) ]
            ]
        ]);
    });
</script>

<!-- Titolo -->
<div id="blob" class="bg-full">
    <div style="transform: translateY(-100%);">
        <center>
            <img src="utils/res/logo.png" style="width: 60px; height: 60px; position: relative; bottom: 10px;">
            <span class="h1">
                <span class="fw-fat">BigBlack</span><span class="fw-thin">Death</span>
            </span>
        </center>
    </div>
</div>

<!-- Istruzioni -->
<div class="container" style="transform: translateY(-20%)">
    <div class="row">
        <div class="col-6 out-left overflow-hidden">
            <img height="600px" src="utils/res/i/help.png">
        </div>
        <div class="col-6 out-right">
            <div class="pt-4">
                <span class="h1 fw-fat">
                    Help
                </span>
                <br>
                Tu sei il blocco grigio, il tuo obiettivo è quello di raggiungere il blocco blu e per farlo puoi muoverti in tutte e sei le direzioni, ma c'è un problema: Non puoi fermarti! <br>
                Infatti una volta che comincerai ad andare in una direzione non ti sarà possibile arrestare la tua corsa finchè non sbatterai contro un blocco giallo o non raggiungerai la fine. <br>
                Premendo <b>[ESC]</b> si aprirà il menù delle opzioni da dove sarà possibile: fissare la telecamera sul personaggio e mostrare i comandi (tutti o solo parzialmente). <br>
                Dal blocco del personaggio passano tre assi, ed ognuno ha i suoi comandi per seguire quella direzione, rispettivamente nel verso dell'asse e quello opposto:
                <ul>
                    <li>
                        <span class="text-danger">
                            Rosso
                        </span>
                        (<b>[D]</b>, <b>[A]</b>)
                    </li>
                    <li>
                        <span class="text-success">
                            Verde
                        </span>
                        (<b>[Spazio]</b>, <b>[Control]</b>)
                    </li>
                    <li>
                        <span class="text-primary">
                            Blu
                        </span>
                        (<b>[W]</b>, <b>[S]</b>)
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>