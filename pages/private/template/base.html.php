
<?php
	$out = ob_function(function() use($page, $args) {
		$args["query"] = include __DIR__ . "/../../../utils/lib/custom/query.php";
		if (!assemble($page, $args))
			assemble("private/error", [ "code" => 404 ]);
	})();

	if (!($_MSG["template"] ?? true))
	{
		echo $out;
		return;
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<base href="/">
		<title> BigBlackDeath - <?= @htmlspecialchars(ucfirst(end(explode("/", $page ? $page : "main")))) ?> </title>
		<link rel="icon" href="utils/res/logo.png">
		<meta charset="UTF-8">

		<!-- Librerie Lato Client -->
		<?php assemble("private/template/includes", [], ".html") ?>
	</head>
	<body>
		<?php if ($_MSG["header"] ?? true) assemble("private/template/header") ?>
		<div id="root">
			<?= $out ?>
		</div>
		<?php if ($_MSG["footer"] ?? true) assemble("private/template/footer") ?>
	</body>
</html>