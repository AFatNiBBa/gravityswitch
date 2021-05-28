
<?php
    http_response_code($code = $code ?? "404");
    $config = [
        403 => [
            "far fa-ban",
            "Accesso non consentito."
        ],
        404 => [
            "far fa-file-exclamation",
            "La pagina non è stata trovata."
        ],
        500 => [
            "fas fa-server",
            "Il server ha riscontrato un errore."
        ],
        "default" => [
            "fas fa-question",
            "Errore sconosciuto."
        ]
    ];
?>

<style>
    body {
        background: #dedede;
    }

    .page-wrap {
        min-height: 100vh;
    }
</style>

<div class="page-wrap d-flex flex-row align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center">
                <span class="display-1 d-block">
                    Errore <?= htmlspecialchars($code) ?> -
                    <i class="<?= ($dex = $config[$code] ?? $config["default"])[0] ?> text-danger text-75"></i>
                </span>
                <div class="mb-4 lead"> <?= $dex[1] ?> </div>
                <a href="/?" class="btn btn-link">Home</a>
            </div>
        </div>
    </div>
</div>