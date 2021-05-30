
<?php
    # Debug PHP
    
?>

<!-- Debug HTML -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.61.1/codemirror.min.js" integrity="sha512-ZTpbCvmiv7Zt4rK0ltotRJVRaSBKFQHQTrwfs6DoYlBYzO1MA6Oz2WguC+LkV8pGiHraYLEpo7Paa+hoVbCfKw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.61.1/codemirror.min.css" integrity="sha512-xIf9AdJauwKIVtrVRZ0i4nHP61Ogx9fSRAkCLecmE2dL/U8ioWpDvFCAy4dcfecN72HHB9+7FfQj3aiO68aaaw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<script src="https://codemirror.net/mode/sql/sql.js"></script>

<script>
    $(() => {
        $("textarea").map((i, e) => {
            CodeMirror.fromTextArea(e, {
                lineNumbers: true,
                mode: "sql"
            });
        });
    });
</script>

<?php foreach($query as $name => $e): ?>
    <hr>
    <h1> <?= $name ?> </h1>
    <textarea cols="30" rows="10">
        <?= $e ?>
    </textarea>
<?php endforeach ?>