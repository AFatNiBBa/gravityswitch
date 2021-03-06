
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
        //| Restituisce il nome della colonna "$k" (Funziona solo se √® stato eseguito "Cacher::apply()" sulla tabella)
        return $this->meta[$k]["name"];
    }

    //| Compatibilit√† 'Array'
    public function count() { return count($this->rows); }
    public function offsetGet($key)  { return $this->rows[$key] ?? null; }
    public function offsetSet($key, $value) { $this->rows[$key] = $value; }
    public function offsetExists($key) { return isset($this->rows[$key]); }
    public function offsetUnset($key) { unset($this->rows[$key]); }

    //| Compatibilit√† 'Iterator'
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
        //| Come "(new Cacher)->string()" ma l'output √® convertito in intero
        return intval($this->string($sql, $args));
    }

    function string($sql, $args = [])
    {
        //| Restituisce il valore della prima colonna della prima riga
        return $this($sql, $args, PDO::FETCH_COLUMN, false)[0];
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
            $stmt = $this->foreign = $this->foreign ?? $this->pdo->prepare(     # Se non c'era gi√†, crea lo 'statement' per richiedere le 'foreign key' di una tabella (Mancato supporto "??=")
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

    //| Compatibilit√† 'stdClass'
    function __isset($key) { return isset($this->cache[$key]); }
    function __unset($key) { unset($this->cache[$key]); }
    function __get($key) { return $this->cache[$key] = $this->cache[$key] ?? $this("SELECT * FROM $key", [], PDO::FETCH_ASSOC, true); } # Mancato supporto "??="
    function __invoke($sql, $args = [], $fetch = PDO::FETCH_ASSOC, $apply = false)
    {
        //| Esegue il codice in "$sql" passando "$args" come parametri e restituisce un oggetto "Table" che ha per ogni riga ci√≤ che √® definito da "$fetch"; Con "$apply" si pu√≤ decidere se eseguire "Cacher::apply()" sul risultato
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

//| Interfaccia migliorata per l'ob (output buffer) con aggiunti alcuni metodi
class ob
{
    //| Output attesi al momento; Settandolo si rischia di rompere tutto
    public static $length = 0;

    //| Richiede un nuovo output
    static function wait() {
        ob_start();
        return ++self::$length;
    }

    //| Manda un output al livello specificato (default 1); Si pu√≤ accedere ai livelli al contrario utilizzando i numeri negativi
    static function send($val = "", $l = 1) {
        $temp = [];
        
        # Accumulo
        for ($i = ($l > 0 ? $l - 1 : self::$length + $l); $i < self::$length; $i++)
            $temp[] = ob_get_clean();

        # Output
        echo $val . end($temp);

        # Riordinamento
        for ($i = count($temp) - 2; $i >= 0; $i--)
        {
            ob_start();
            echo $temp[$i];
        }
        return --self::$length;
    }

    //| Genera una funzione che avr√† come output quello di "$f" concatenato a quello dell'echo
    public static function func($f)
    {
        return function(...$args) use($f) {
            ob_start();
            @$f(...$args);
            return ob_get_clean();
        };
    }

    //| Rende le funzioni di base dell'output buffer chiamabili da qui
    public static function __callStatic($name, $args)
    {
        return ("ob_$name")(...$args);
    }
}

//| Contiene funzioni per generare parti molto utilizzate di codice
class ¬£
{
    //| Interpola un oggetto in un elemento 'html'; Se "$getter" √® 'true' crea un intero getter accessibile dall'esecuzione della funzione "onclick"
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
                        <?php if($type == "date" && isset($placeholder)): # L'elemento √® di tipo "date" solo quando viene cliccato, in questo modo √® visibile il placeholder ?>
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

//| Carica il file passandogli certe variabili; Se un file √® in mezzo al percorso viene comunque richiamato e pu√≤ poi comportarsi come nodo-cartella avendo accesso a "$dirs" e "$i"
function assemble($path, array $_ARGS = [], $ext = ".html.php")
{
    global $db;                                             # Variabili accessibili a tutte le pagine
    static $_MSG = [];                                      # Variabile utilizzabile per scambiare informazioni tra le pagine; La parola 'static' la crea la prima volta che serve ed usa la stessa instanza tutte le altre volte

    $_FILE = $path[0] == "/"                                # Se il percorso inizia con "/" allora parte da dentro a "pages", altrimenti dalla cartella corrente
    ? __DIR__ . "/../pages"
    : dirname(debug_backtrace()[0]["file"]) . "/";          # Cartella del file che chiava la funzione**-

    $dirs = preg_split("~[\\\\/]~", $path);
    for ($i = 0, $l = count($dirs); $i < $l; $i++)
    {
        if (is_dir($current = $_FILE . $dirs[$i]))
        {
            //| Cartella
            $_FILE = $current . "/";
            if ($i == $l - 1) $dirs[$l++] = "main";
        }
        else if (!file_exists($_FILE = $current . $ext))
            //| Non trovato
            return false;
        else
        {
            //| File
            for ($_PATH = ""; ++$i < $l;)                   # Creazione variabile speciale "$_PATH" in cui verr√† contenuto il percorso successivo
                $_PATH .= ($_PATH ? "/" : "") . $dirs[$i];
            unset($path, $ext, $dirs, $i, $l, $current);    # Eliminazione variabili definite in funzione (Tranne "$_PATH", "$_FILE", "$_ARGS", "$_MSG" e "$db")
            extract($_ARGS);                                # Definizione parametri personalizzati
            return [ include $_FILE ];                      # Esecuzione "$_FILE"
        }
    }
    return false;
}

//| Ottiene il percorso di sottodomini corrente
function hostpath($offset = 0)
{
    $out = array_reverse(explode(".", $_SERVER["HTTP_HOST"]));
    return $offset
    ? array_slice($out, $offset)
    : $out;
}

//| Come "var_dump()" ma ben formattato ed escapa i caratteri speciali (Solo quando non c'√® "php_xdebug.dll")
function dump($obj)
{
    echo "<pre>";
    echo htmlspecialchars(ob::func("var_dump")($obj));
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
    $mail->Body = ob::func($f)($mail);

    return $mail->send();
}