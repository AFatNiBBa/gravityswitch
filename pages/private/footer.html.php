
<footer class="container py-5">
    <div class="row">
        <div class="col-12 col-md">
            <i class="fad fa-info-circle text-info h2"></i>
            <small class="d-block mb-3 text-muted">© <?= (date("Y") - 1) . "-" . date("Y") ?> <?= $_SERVER["SERVER_NAME"] ?> </small>
        </div>
        <div class="col-6 col-md">
            <h5>Pagine del sito</h5>
            <ul class="list-unstyled text-small">
                <li><a class="text-muted" href="/?">Home</a></li>
                <li><a class="text-muted" href="/?page=game">Gioco</a></li>
                <li><a class="text-muted" href="/?page=login">Login</a></li>
                <li><a class="text-muted" href="/?page=login/register">Registrazione</a></li>
            </ul>
        </div>
        <div class="col-6 col-md">
            <h5>Livelli di Default</h5>
            <ul class="list-unstyled text-small">
                <?php foreach($db("SELECT * FROM mappe WHERE stile->'$.rnd' <> true") as [ "id" => $e ]): ?>
                    <li>
                        <a class="text-muted" href="/?page=game/<?= htmlspecialchars(urlencode($e)) ?>">
                            Livello
                            <span class="text-primary">
                                #<?= htmlspecialchars($e) ?>
                            </span>
                        </a>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
        <div class="col-6 col-md">
            <h5>Risorse</h5>
            <ul class="list-unstyled text-small">
                <li><a class="text-muted" href="/?page=debug">Debug</a></li>
                <li><a class="text-muted" href="https://s534.altervista.org/phpmyadmin/import.php#PMAURL-1:db_structure.php?db=my_dump&table=&server=1&target=&token=658470f6f693bef87e730f9404f00b50">Database</a></li>
                <li><a class="text-muted" href="#">Github</a></li>
            </ul>
        </div>
        <div class="col-6 col-md">
            <h5>Informazioni</h5>
            <ul class="list-unstyled text-small">
                <li>Fatto da Sean Alunni</li>
                <li>ITIS Franchetti Salviani</li>
                <li>5<sup>a</sup>C Informatica</li>
            </ul>
        </div>
    </div>
</footer>