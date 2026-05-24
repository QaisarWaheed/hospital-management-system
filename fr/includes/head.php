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
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">

<style>
@media print
{    
    .nodisplay_print
    {
        display: none !important;
    }
}
</style>