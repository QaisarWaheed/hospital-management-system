<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<script>
	if ('serviceWorker' in navigator) {
		navigator.serviceWorker.getRegistrations().then(function (regs) {
			regs.forEach(function (reg) { reg.unregister(); });
		});
	}
	</script>
	<meta lang="en">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/nav_style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" crossorigin="anonymous" />
<style>
@font-face{  font-family: "Jameel Noori Nastaleeq";  src: url("fonts/Jameel Noori Nastaleeq Regular.ttf") format("truetype");}    
</style>