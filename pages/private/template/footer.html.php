
<script>
    $(() => $("[title]").tooltip());
</script>

<!-- Copia dell'elemento dove si trovava il bordo (Per dividerlo senza cazzate) -->
<section class="bg-light pb-10">
    <!-- Bordo -->
    <div class="svg-border-rounded text-dark">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 144.54 17.34" preserveAspectRatio="none" fill="currentColor"><path d="M144.54,17.34H0V0H144.54ZM0,0S32.36,17.34,72.27,17.34,144.54,0,144.54,0"></path></svg>
    </div>
</section>

<!-- Scritte -->
<footer class="footer pt-10 pb-5 mt-auto bg-dark footer-dark">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="footer-brand">BigBlackDeath</div>
                <div class="mb-3">Il giochino brutto</div>
                <div class="icon-list-social mb-5">
                    <a href="a:" class="icon-list-social-link" title="È solo un placeholder, scusa!"><i class="fab fa-instagram"></i></a>
                    <a href="a:" class="icon-list-social-link" title="È solo un placeholder, scusa!"><i class="fab fa-facebook"></i></a>
                    <a href="https://github.com/AFatNiBBa/gravityswitch" class="icon-list-social-link" title="Codice sorgente"><i class="fab fa-github"></i></a>
                    <a href="a:" class="icon-list-social-link" title="È solo un placeholder, scusa!"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-5 mb-lg-0">
                        <div class="text-uppercase-expanded text-xs mb-4">Pagine del sito</div>
                        <ul class="list-unstyled mb-0">
                            <li><a class="mb-2" href="/?">Home</a></li>
                            <li><a class="mb-2" href="/?page=game">Gioco</a></li>
                            <li><a class="mb-2" href="/?page=account">Account</a></li>
                            <li><a class="mb-2" href="/?page=login">Login</a></li>
                            <li><a href="/?page=login/register">Registrazione</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-5 mb-lg-0">
                        <div class="text-uppercase-expanded text-xs mb-4">Livelli di Default</div>
                        <ul class="list-unstyled mb-0">
                            <?php foreach($db("SELECT * FROM mappe WHERE JSON_EXTRACT(stile, '$.rnd') <> true") as [ "id" => $e ]): ?>
                                <li>
                                    <a class="mb-2" href="/?page=game/<?= htmlspecialchars(urlencode($e)) ?>">
                                        Livello
                                        <span class="text-primary">
                                            #<?= htmlspecialchars($e) ?>
                                        </span>
                                    </a>
                                </li>
                            <?php endforeach ?>
                            <li><a href="/?page=game/rnd">Livello Random</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-5 mb-md-0">
                        <div class="text-uppercase-expanded text-xs mb-4">Risorse</div>
                        <ul class="list-unstyled mb-0">
                            <li><a class="mb-2" href="/?page=debug">Debug</a></li>
                            <li><a class="mb-2" href="/?page=private/error">Errore</a></li>
                            <li><a href="https://s534.altervista.org/phpmyadmin/import.php#PMAURL-1:db_structure.php?db=my_dump&table=&server=1&target=&token=658470f6f693bef87e730f9404f00b50">Database</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="text-uppercase-expanded text-xs mb-4">Informazioni</div>
                        <ul class="list-unstyled mb-0">
                            <li>Fatto da Sean Alunni</li>
                            <li>ITIS Franchetti Salviani</li>
                            <li>5<sup>a</sup>C Informatica</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <hr class="my-5" />
        <div class="row align-items-center">
            <div class="col-md-6 small">&copy;<?= (date("Y") - 1) . "-" . date("Y") ?> <?= $_SERVER["SERVER_NAME"] ?></div>
            <div class="col-md-6 text-md-right small">
                <a href="a:" title="È solo un placeholder, scusa!">Privacy Policy</a>
                &middot;
                <a href="a:" title="È solo un placeholder, scusa!">Terms &amp; Conditions</a>
            </div>
        </div>
    </div>
</footer>