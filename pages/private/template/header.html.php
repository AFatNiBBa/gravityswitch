
<!-- Navbar -->
<nav class="navbar navbar-marketing navbar-expand-lg bg-transparent navbar-dark fixed-top">
    <div class="container" style="max-width: 1337px">
        <a class="navbar-brand d-block mr-5" href="/?">
            <img src="utils/res/logo.png" style="width: 60px; height: 60px; position: relative; bottom: 10px;">
            <span class="h1">
                <span class="fw-fat">BigBlack</span><span class="fw-thin">Death</span>
            </span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i class="fas fa-bars"></i></button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto mr-lg-5">
                <li class="nav-item active cursor-pointer float-left">
                    <a class="nav-link" href="/?">
                        <i class="fad fa-home"></i>
                        Home
                    </a>
                </li>
                <li class="nav-item active cursor-pointer">
                    <a class="nav-link" href="/?page=game">
                        <i class="fad fa-dice-d6"></i>
                        Gioco
                    </a>
                </li>
                <?php if($user = @$_SESSION["account"]): ?>
                    <!-- Loggato -->
                    <li class="nav-item active cursor-pointer">
                        <a class="nav-link" href="/?page=plain/db.php/logout">
                            <i class="fad fa-user-slash"></i>
                            Logout
                        </a>
                    </li>
                <?php else: ?>
                    <!-- Non loggato -->
                    <li class="nav-item active cursor-pointer">
                        <a class="nav-link" href="/?page=login">
                            <i class="fad fa-sign-in-alt"></i>
                            Login
                        </a>
                    </li>
                    <li class="nav-item active cursor-pointer">
                        <a class="nav-link" href="/?page=login/register">
                            <i class="fad fa-user-plus"></i>
                            Registrazione
                        </a>
                    </li>
                <?php endif ?>
            </ul>
            <ul class="navbar-nav ml-auto">
                <!-- Searchbar -->
                <li class="nav-item active cursor-pointer">
                    <form action="/?" method="get">
                        <input type="hidden" name="page" value="account">
                        <div class="form-group">
                            <div class="input-group shadow-sm">
                                <input
                                    type="search"
                                    name="search"
                                    class="form-control"
                                    placeholder="Cerca Utente"
                                    <?php if($value = @$_GET["search"]): ?>
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
                <?php if ($user): ?>
                    <!-- Bottone Account -->
                    <li class="nav-item active cursor-pointer">
                        <a class="nav-link btn bg-white text-black" href="/?page=account">
                            <i class="fad fa-user"></i>
                            <?= $user["nick"] ?>
                        </a>
                    </li>
                <?php endif ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Titolo -->
<?php if($title = @$_MSG["title"]): ?>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary">
        <div class="page-header-content pt-10">
            <div class="container text-center">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <h1 class="page-header-title mb-3"><?= $title[0] ?? "" ?></h1>
                        <p class="page-header-text"><?= $title[1] ?? "" ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="svg-border-rounded text-light">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 144.54 17.34" preserveAspectRatio="none" fill="currentColor"><path d="M144.54,17.34H0V0H144.54ZM0,0S32.36,17.34,72.27,17.34,144.54,0,144.54,0"></path></svg>
        </div>
    </header>
<?php endif ?>