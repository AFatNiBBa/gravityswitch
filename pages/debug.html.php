
<?php
    # Debug PHP
    
?>

<!-- Debug HTML -->

<style>
    #wrapper {
        display: grid;
        grid-template-columns: 1fr;
        row-gap: 30px;
    }

    .item {
        display: grid;
        grid-template-columns: 1fr 3fr 1fr;
        align-items: center;
        padding: 10px 30px 10px 10px;
        overflow: hidden;
        border-radius: 10px;
        box-shadow: 0 5px 7px -1px rgba(51, 51, 51, 0.23);
        cursor: pointer;
        transition: transform 0.25s cubic-bezier(0.7, 0.98, 0.86, 0.98), box-shadow 0.25s cubic-bezier(0.7, 0.98, 0.86, 0.98);
        background-color: #fff;
    }

    .item:hover {
        transform: scale(1.2);
        box-shadow: 0 9px 47px 11px rgba(51, 51, 51, 0.18);
    }
</style>

<div id="wrapper" class="w-50 mx-auto">
    <div class="item">ciao mondo</div>
    <div class="item">ciao mondo</div>
    <div class="item">ciao mondo</div>
    <div class="item">ciao mondo</div>
    <div class="item">ciao mondo</div>
    <div class="item">ciao mondo</div>
    <div class="item">ciao mondo</div>
</div>

<?php return; a: ?>

<style>
    #a {
        width: 300px;
        height: 300px;
        background-image: url("utils/res/i/circles.svg");
    }

    #a > div > div {
        width: 100px;
        height: 100px;
    }
</style>

<div class="centered">
    <div id="a" class="bg-full rounded-lg">
        <div class="centered">
            <div id="b" class="text-white">
                <h1>
                    <?= $_GET["title"] ?? "Titolo" ?>
                </h1>
                Lorem Ipsum
            </div>
        </div>
    </div>
</div>

<script>
    const a = $('#a')[0];
    const b = $('#a > div > div')[0];

    function rotate(inner, outer, angle = 0, scale = 1) {
        const opts = {
            duration: 1000,
            elasticity: 300
        }
        anime.remove(inner);
        anime.remove(outer);
        anime({
            targets: inner,
            rotateZ: -angle
        });
        anime({
            targets: outer,
            rotateZ: angle,
            scale
        });
    }

    a.addEventListener('mouseenter', () => rotate(b, a, 90, 1.5));
    a.addEventListener('mouseleave', () => rotate(b, a, 0));
</script>