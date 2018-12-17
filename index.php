<?php
	define('URL_LENGTH', 2);
	include_once('libraries/checkUrl.php');
?><!DOCTYPE html>
<html>
	<head>
		<title>Compare links</title>
		<link rel="stylesheet" href="assets/style.css">
		<script type="text/javascript" src="assets/script.js"></script>
	</head>
	<body>
		<div class="layer">
			<h2>Compare links</h2>
			<form method="POST" action="" onsubmit="return validateForm(this);">
				<?php for($i=1; $i<=URL_LENGTH; $i++){ ?>
					<label for="url_<?php echo $i; ?>">URL <?php echo $i; ?> <span>(<i>0</i> caratteri) - [<i></i>])</span></label>
					<input type="text" id="url_<?php echo $i; ?>" name="url[]" required placeholder="Insert URL <?php echo $i; ?>">
				<?php } ?>
				<input type="submit" value="Invia">
			</form>
			<span id="totchars">Totale caratteri: <i>0</i></span>
		</div>
	</body>
</html>