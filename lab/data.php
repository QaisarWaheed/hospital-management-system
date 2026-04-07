<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Real Time Data Display</title>
</head>
<body onload = "table();">
    <form method = "GET">
        <input type = "number" name = "item_id" />
    </form>
    <div id="table"></div>
</body>
</html>
<script type="text/javascript">
function table()
{
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function()
    {
    document.getElementById("table").innerHTML = this.responseText;
    }
    <?php if(isset($_GET['item_id']) && $_GET['item_id'] != ''){ ?>
        xhttp.open("GET", "system.php?item_id=" + <?php echo $_GET['item_id']; ?>);
    <?php }else{ ?>
        xhttp.open("GET", "system.php");
    <?php } ?>
    xhttp.send();
}
setInterval
(function()
    {
        table();
    }
,1);
</script>
