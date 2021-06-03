
<?php

//| Autoloader
spl_autoload_register(
    function ($class) {
        $class = str_replace("\\", "/", $class);
        include __DIR__ . "/lib/$class.php";
    }
);

//| Genera un'oggetto che mantiene la cache di una tabella
class Table extends ArrayObject
{
    public $rows;
    public $meta;

    function __construct($rows, $meta)
    {
        $this->rows = $rows;
        $this->meta = $meta;
    }

    function name($k)
    {
        //| Restituisce il nome della colonna "$k" (Funziona solo se è stato eseguito "Cacher::apply()" sulla tabella)
        return $this->meta[$k]["name"];
    }

    //| Compatibilità 'Array'
    public function count() { return count($this->rows); }
    public function offsetGet($key)  { return $this->rows[$key] ?? null; }
    public function offsetSet($key, $value) { $this->rows[$key] = $value; }
    public function offsetExists($key) { return isset($this->rows[$key]); }
    public function offsetUnset($key) { unset($this->rows[$key]); }

    //| Compatibilità 'Iterator'
    public function getIterator() { return new ArrayIterator($this->rows); }
}

//| Genera un oggetto che mantiene la cache di un database
class Cacher
{
    private $pdo;
    function pdo() { return $this->pdo; }
    private $cache = [];
    function cache() { return $this->cache; }
    private $foreign = null;

    function __construct($opts)
    {
        if (is_string($opts)) $opts = [ "db" => $opts ];
        $opts["host"] = $opts["host"] ?? "localhost";   # Mancato supporto "??="
        $this->pdo = new PDO("mysql:host={$opts['host']};dbname={$opts['db']};charset=utf8", $opts["user"] ?? "root", $opts["pass"] ?? "");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec("SET NAMES utf8");             # Setta per bene il charset su 'utf8'
    }

    function clear() 
    {
        //| Cancella la cache
        return $this->cache = [];
    }

    function int($sql, $args = [])
    {
        //| Restituisce il valore della prima colonna della prima riga come numero intero
        return intval($this($sql, $args, PDO::FETCH_COLUMN, false)[0]);
    }

    function apply($table)
    {
        //| Applica le informazioni sulle foreign key sui metadati della tabella
        $cache = [];
        foreach($table->meta as $meta)
        {
            $name = $meta["table"];
            if (isset($cache[$name])) continue;
            else $cache[$name] = true;
            $stmt = $this->foreign = $this->foreign ?? $this->pdo->prepare(     # Se non c'era già, crea lo 'statement' per richiedere le 'foreign key' di una tabella (Mancato supporto "??=")
                "SELECT `column_name` AS fk_column, `referenced_table_name` AS pk_table, `referenced_column_name` AS pk_column
                FROM `information_schema`.`KEY_COLUMN_USAGE`
                WHERE `constraint_schema` = SCHEMA() AND `table_name` = ? AND `referenced_column_name` IS NOT NULL;"
            );
            $stmt->execute([ $name ]);                                          # Ottiene le 'foreign key' della tabella richiesta
            while($key = $stmt->fetch(PDO::FETCH_ASSOC))                        # Applica i dati delle 'foreign key'
            {
                foreach ($table->meta as &$col)
                {
                    if (!$col["fk"] && $col["table"] == $name && $col["name"] == $key["fk_column"])
                    {
                        $col["fk"] = true;
                        $col["pk_table"] = $key["pk_table"];
                        $col["pk_column"] = $key["pk_column"];
                        break;
                    }
                }
            }
        }
        return $table;
    }

    //| Compatibilità 'stdClass'
    function __isset($key) { return isset($this->cache[$key]); }
    function __unset($key) { unset($this->cache[$key]); }
    function __get($key) { return $this->cache[$key] = $this->cache[$key] ?? $this("SELECT * FROM $key", [], PDO::FETCH_ASSOC, true); } # Mancato supporto "??="
    function __invoke($sql, $args = [], $fetch = PDO::FETCH_ASSOC, $apply = false)
    {
        //| Esegue il codice in "$sql" passando "$args" come parametri e restituisce un oggetto "Table" che ha per ogni riga ciò che è definito da "$fetch"; Con "$apply" si può decidere se eseguire "Cacher::apply()" sul risultato
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($args);
        if ($l = $stmt->columnCount())
        {
            $rows = $stmt->fetchAll($fetch);
            $meta = [];
            for ($i = 0; $i < $l; $i++)
            {
                $temp = $stmt->getColumnMeta($i);
                $meta[] = [
                    "id" => $i,
                    "name" => $temp["name"],
                    "table" => $temp["table"],
                    "type" => $temp["native_type"],
                    "pk" => in_array("primary_key", $temp["flags"]),
                    "null" => !in_array("not_null", $temp["flags"]),
                    "fk" => null # default
                ];
            }
            $out = new Table($rows, $meta);
            return $apply
            ? $this->apply($out)
            : $out;
        }
        else return $stmt->rowCount();
    }
}

//| Contiene funzioni per generare parti molto utilizzate di codice
class £
{
    //| Interpola un oggetto in un elemento 'html'; Se "$getter" è 'true' crea un intero getter accessibile dall'esecuzione della funzione "onclick"
    static function put($v, $getter = true)
    {
        $out = htmlspecialchars(json_encode($v));
        if ($getter) $out = "data-json=\"$out\"";
        return $out;
    }

    //| Inserisce un fragment ("http://blah.com#frag"); Se usato normalmente verrebbe appicciato alla fine dell'href del '<base>'
    static function hash($frag)
    {
        return htmlspecialchars("javascript: void(window.location.hash = '') || void(window.location.hash = " . json_encode($frag) . ")");
    }

    //| Stampa del codice javascript che porta alla pagina selezionata; Se non viene passato "$url" ricarica la pagina; Se viene passato '-1' porta alla precedente
    static function href($url = 0)
    {
        ?>
            <script>
                <?php if($url == -1): ?>
                    if(window.history.length > 1) window.history.back();
                    else window.location.href = "/?";
                <?php elseif($url): ?>
                    window.location.href = <?= json_encode($url) ?>;
                <?php else: ?>
                    window.location.href = window.location.href;
                <?php endif ?>
            </script>
        <?php
    }

    //| Stampa un "alert" di bootstrap con colore, icona e testo personalizzato
    static function alert($icon, $text, $type) 
    {
        ?>
            <div class="alert alert-<?= $type ?>">
                <i class="fa-fw <?= $icon ?> mr-2"></i>
                <?= $text ?>
            </div>
        <?php
    }

    //| Un campo della '<form>'
    static function field($name, $type, $label, $icon, $placeholder, $required = true)
    {
        ?>
            <div class="form-group">
                <label> <?= htmlspecialchars($label) ?> </label>
                <div class="input-group shadow-sm">
                    <div 
                        class="input-group-prepend"
                        <?php if($type == "password"): ?>
                            onclick='{
                                const self = $(this);
                                const input = self.parent().find("input")[0];
                                self.find("i").toggleClass("fa-eye-slash").toggleClass("fa-eye");
                                input.type = {
                                    password: "text",
                                    text: "password"
                                }[input.type];
                            }'
                        <?php endif ?>
                    >
                        <span class="input-group-text">
                            <i class="fa-fw <?= $icon ?>"></i>
                        </span>
                    </div>
                    <?php if($type == "file"): ?>
                        <!-- Fa le veci grafiche del vero '<input>' -->
                        <input 
                            type="text"
                            class="form-control text-primary"
                            value="Scegli file"
                            style="background-color: white"
                            readonly

                            onclick='$(this).parent().find(`[name="<?= $name ?>"]`).click()'
                        >
                    <?php endif ?>
                    <input
                        <?php if($type == "date" && isset($placeholder)): # L'elemento è di tipo "date" solo quando viene cliccato, in questo modo è visibile il placeholder ?>
                            onblur="(this.type='text')"
                            onfocus="(this.type='date')"
                        <?php else: ?>
                            type="<?= $type ?>"
                        <?php endif ?>
                        name="<?= $name ?>"
                        class="form-control <?= $type == "file" ? "d-none" : "" ?>"
                        placeholder="<?= htmlspecialchars($placeholder ?? "" ) ?>"
                        <?php if($value = @$_POST[$name]): ?>
                            value="<?= htmlspecialchars($value) ?>"
                        <?php endif ?>
                        <?php if($required): ?>
                            required
                        <?php endif ?>
                        <?php if($type == "file"): ?>
                            onchange='$(this).parent().find("div > span > i")[this.files?.length ? "addClass" : "removeClass"]("text-success")'
                        <?php endif ?>
                    >
                </div>
            </div>
        <?php
    }
}

//| Carica il file passandogli certe variabili; Se un file è in mezzo al percorso viene comunque richiamato e può poi comportarsi come nodo-cartella avendo accesso a "$dirs" e "$i"
function assemble($path, array $args = [], $ext = ".html.php")
{
    global $db;                                             # Variabili accessibili a tutte le pagine
    static $_MSG = [];                                      # Variabile utilizzabile per scambiare informazioni tra le pagine; La parola 'static' la crea la prima volta che serve ed usa la stessa instanza tutte le altre volte

    $file = $path[0] == "/"                                 # Se il percorso inizia con "/" allora parte da dentro a "pages", altrimenti dalla cartella corrente
    ? __DIR__ . "/../pages"
    : dirname(debug_backtrace()[0]["file"]) . "/";          # Cartella del file che chiava la funzione**-

    $dirs = preg_split("~[\\\\/]~", $path);
    for ($i = 0, $l = count($dirs); $i < $l; $i++)
    {
        if (is_dir($current = $file . $dirs[$i]))
        {
            //| Cartella
            $file = $current . "/";
            if ($i == $l - 1) $dirs[$l++] = "main";
        }
        else if (!file_exists($file = $current . $ext))
            //| Non trovato
            return false;
        else
        {
            //| File
            for ($_PATH = ""; ++$i < $l;)                   # Creazione variabile speciale "$_PATH" in cui verrà contenuto il percorso successivo
                $_PATH .= ($_PATH ? "/" : "") . $dirs[$i];
            unset($path, $ext, $dirs, $i, $l, $current);    # Eliminazione variabili definite in funzione
            extract($args);                                 # Definizione parametri personalizzati
            include $file;                                  # Esecuzione "$file"
            return true;
        }
    }
    return false;
}

//| Genera una funzione che avrà come output quello di "$f" concatenato a quello dell'echo
function ob_function($f)
{
    return function(...$args) use($f) {
        ob_start();
        $out = @$f(...$args);
        if (!is_string($out)) $out = "";
        $out .= ob_get_contents();
        ob_end_clean();
        return $out;
    };
}

//| Come "var_dump()" ma ben formattato ed escapa i caratteri speciali
function dump($obj)
{
    echo "<pre>";
    echo htmlspecialchars(ob_function("var_dump")($obj));
    echo "</pre>";
    return $obj;
}

//| Manda una email a "$to" da "$from" che ha come oggetto "$sub" e come contenuto lo 'stdout' di "$f()"
function send($f, $to, $from, $sub, $files = [])
{
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isHTML(true);
    $mail->CharSet = "UTF-8";
    $mail->Subject = $sub;
    $mail->setFrom("$from@{$_SERVER["HTTP_HOST"]}", $from);
    $mail->addAddress($to);

    //| Allegati
    foreach ($files as $name => $data)
        $mail->addStringAttachment($data, $name);

    //| Contenuto
    ob_start();
    $f($mail);
    $mail->Body = ob_get_contents();
    ob_end_clean();
    return $mail->send();
}