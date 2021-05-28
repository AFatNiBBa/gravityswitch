
<script>        
    //| Visualizzazione o meno della parte superiore della 'NavBar'
    window.onscroll = function(anim) {
        const max = 99;
        const self = arguments.callee;                                  // La funzione stessa (Immagazzina la lunghezza base di "#nav" direttamente all'interno dell'oggetto della Funzione)
        with($("#nav"))                                                 // Il blocco 'with' globalizza l'oggetto interno (Solo 'Getter') mantenendo i 'this' sulle funzioni
        {
            if (anim !== false) height(self.height);                    // La imposta di base sul elemento sennò non c'è il punto di partenza dell'animazione la prima volta
            if (document.body.scrollTop > max || document.documentElement.scrollTop > max)
                height(0);
        }
    };

    //| Aggiunge alla pagina un po' di 'margin' per aggiustare la 'NavBar' ed il 'Footer'
    $(window).on("load resize", () => {
        with ($("#nav"))
        {
            first().css("height", "auto");
            window.onscroll.height = height();                          // Ogni volta che teoricamente può cambiare la lunghezza di "#nav" aggiorna la sua lunghezza base
        }
        window.onscroll(false);                                         // Fa si che venga chiamata controllatamente per riparare gli eventuali danni generati dalla lettura dell'altezza utomatica; Grazie al 'false' non viene aperta la "#nav" quando è chiusa e si esegue il 'resize' della pagina
        $("body").css({
            // 'margin-bottom': $("footer").outerHeight(),
            'margin-top': $("nav").parent().height() -
                $("#nav").height() +
                window.onscroll.height                                  // Lunghezza del'header - la lunghezza corrente della barra superiore + la lunghezza della barra superiore da aperta
        });
    });

    //| Aggiunta 'tooltip'
    $(() => $("[title]").tooltip());
</script>

<div class="fixed-top bg-frag">
    <!-- Intestazione -->
    <div id="nav" class="overflow-hidden text-white pl-4">
        <img src="utils/res/logo.png" style="width: 60px; height: 60px; position: relative; bottom: 10px;">
        <span class="h1">
            <span class="fw-fat">BigBlack</span><span class="fw-thin">Death</span>
        </span>
        <div class="float-right p-3 mr-3">By Sean</div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#menu">
            <i class="h1 fad fa-bars"></i>
        </button>
        <div id="menu" class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <!-- <li class="nav-item active cursor-pointer">
                    <a class="navbar-brand">
                        <img src="/data/fismov.png" style="width: 30px; height: 30px">
                    </a>
                </li> -->
                <li class="nav-item active cursor-pointer">
                    <a class="nav-link btn bg-white" href="/?">
                        <i class="fad fa-home"></i>
                        Home
                    </a>
                </li>
                &nbsp;
                &nbsp;
                <li class="nav-item active cursor-pointer">
                    <a class="nav-link btn btn-success" href="/?page=game">
                        <i class="fad fa-dice-d6"></i>
                        Gioco
                    </a>
                </li>
                &nbsp;
                &nbsp;
                <?php if($user = @$_SESSION["account"]): ?>
                    <!-- Loggato -->
                    <li class="nav-item active cursor-pointer">
                        <a class="nav-link btn btn-danger" href="/?page=plain/db.php/logout">
                            <i class="fad fa-user-slash"></i>
                            Logout
                        </a>
                    </li>
                <?php else: ?>
                    <!-- Non loggato -->
                    <li class="nav-item active cursor-pointer">
                        <a class="nav-link btn btn-secondary" href="/?page=login">
                            <i class="fad fa-sign-in-alt"></i>
                            Login
                        </a>
                    </li>
                    &nbsp;
                    &nbsp;
                    <li class="nav-item active cursor-pointer">
                        <a class="nav-link btn btn-primary" href="/?page=login/register">
                            <i class="fad fa-user-plus"></i>
                            Registrazione
                        </a>
                    </li>
                <?php endif ?>
            </ul>
            <ul class="navbar-nav navbar-right">
                <li class="nav-item active cursor-pointer">
                    <!-- Searchbar -->
                    <form action="/?" method="get">
                        <input type="hidden" name="page" value="account">
                        <div class="form-group">
                            <div class="input-group shadow-sm">
                                <input
                                    type="search"
                                    name="search"
                                    class="form-control"
                                    placeholder="Cerca Utente"
                                    <?php if($value = @$_POST[$name]): ?>
                                        value="<?= htmlspecialchars($value) ?>"
                                    <?php endif ?>
                                >
                                <div class="input-group-append" onclick='$(this).parents("form").submit()'>
                                    <span class="input-group-text">
                                        <i class="fa-fw fas fa-search"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                </li>
                &nbsp;
                &nbsp;
                <?php if ($user): ?>
                    <li class="nav-item active cursor-pointer">
                        <a class="nav-link btn bg-white" href="/?page=account">
                            <i class="fad fa-user"></i>
                            <?= $user["nick"] ?>
                        </a>
                    </li>
                <?php endif ?>
            </ul>
        </div>
    </nav>
</div>