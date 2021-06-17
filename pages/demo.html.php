
<?php
    if ($eval = @$_POST["eval"])
    {
        $_MSG["title"] = [ "Eval", "Codice inserito dall'utente: SOLO DIMOSTRATIVO" ];
        eval("?>$eval");
        return;
    }
?>

<?php if(is_file($path = $_GET["path"] ?? "/membri/dump")): $_MSG["template"] = false ?>
    <!-- Visualizzazione File -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.61.1/codemirror.min.js" integrity="sha512-ZTpbCvmiv7Zt4rK0ltotRJVRaSBKFQHQTrwfs6DoYlBYzO1MA6Oz2WguC+LkV8pGiHraYLEpo7Paa+hoVbCfKw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.61.1/codemirror.min.css" integrity="sha512-xIf9AdJauwKIVtrVRZ0i4nHP61Ogx9fSRAkCLecmE2dL/U8ioWpDvFCAy4dcfecN72HHB9+7FfQj3aiO68aaaw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="https://codemirror.net/mode/clike/clike.js"></script>
    <script src="https://codemirror.net/mode/php/php.js"></script>
    <script src="https://codemirror.net/mode/xml/xml.js"></script>
    <script src="https://codemirror.net/mode/css/css.js"></script>
    <script src="https://codemirror.net/mode/javascript/javascript.js"></script>

    <script>
        function load() {
            (
                window.area = CodeMirror.fromTextArea(
                    document.querySelector("textarea"), 
                    {
                        lineNumbers: true,
                        matchBrackets: true,
                        mode: "application/x-httpd-php",
                        indentUnit: 4,
                        indentWithTabs: true,
                        enterMode: "keep",
                        tabMode: "shift"
                    }
                )
            ).setSize(window.innerWidth, window.innerHeight);

            window.area.on("keydown", (self, e) => {
                if(e.key.toLowerCase() == "enter" && e.ctrlKey)
                {
                    const temp = self.getTextArea();
                    temp.value = self.getValue();
                    temp.parentElement.submit();
                }
            });
        }
    </script>

    <body onload="load()">
        <form method="post">
            <textarea name="eval">
                <?= htmlspecialchars(file_get_contents($path)) ?>
            </textarea>
        </form>
    </body>
<?php elseif(is_dir($path)): $_MSG["title"] = ["Cartella", "File e Cartelle contenute nella cartella selezionata"] ?>
    <!-- Visualizzazione Cartella -->
    <div class="container grow w-75 mt-15">
        <?php foreach(scandir($path) as $file): ?>
            <a class="<?= is_dir($temp = realpath("$path/$file")) ? "bg-warning" : "" ?>" href="/?page=demo&path=<?= urlencode($temp) ?>">
                <?= htmlspecialchars($file) ?>
            </a>
        <?php endforeach ?>
    </div>
<?php else: ?>
    <!-- File Non Trovato -->
    <?php assemble("/private/error", [ "code" => 404 ]) ?>
<?php endif ?>