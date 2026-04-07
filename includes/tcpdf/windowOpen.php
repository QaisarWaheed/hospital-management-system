<!DOCTYPE html>
<html>
<body>
	PLOT<input type="text" name="amount" id="plot_no" value="H100"><br>
	PLOT<input type="text" name="amount" id="name" value="SANA ULLAH"><br>
	PRICE<input type="number" name="amount" id="price" value="1500000"><br>
	RECEIVED<input type="number" name="amount" id="received" value="100"><br>
	CURRENT RECIVED<input type="number" name="amount" id="amount" value="100"><br>
	VOUCHER<input type="number" name="voucher" id="voucher" value="1000124"><br>
	DATE<input type="date" name="date" id="date" >
<button onclick="openWin()">Open "myWindow"</button>
<button onclick="closeWin()">Close "myWindow"</button>

<?php header("Refresh: 5; url=windowOpen.php"); ?>
<script>
var myWindow;
function openWin() {
  myWindow = window.open("", "myWindow", "width=500,height=500");
  var price = "<tr><th>TOTAL PRICE</th><th>"+document.getElementById("price").value+"</th></tr>";
  var name = "<tr><th>CUSTOMER NAME</th><th>"+document.getElementById("name").value+"</th></tr>";
  var amount = "<tr><th>CURRENT RECEIVED AMOUNT</th><th>"+document.getElementById("amount").value+"</th></tr>";
  var date = "<tr><th>DATE</th><th>"+document.getElementById("date").value+"</th></tr>";
  var voucher = "<tr><th>VOUCHER NO</th><th>"+document.getElementById("voucher").value+"</th></tr>";
  var plot_no = "<tr><th>PLOT NO</th><th>"+document.getElementById("plot_no").value+"</th></tr>";
  var total_received = parseInt(document.getElementById("received").value);
  var current_receiced = parseInt(document.getElementById("amount").value);
  var remain_amount = parseInt(total_received - current_receiced);
  var remian = "<tr><th>BALANCE AMOUNT</th><th>"+remain_amount+"</th></tr>";
  myWindow.document.write('<table class="table table-bordered" border="2" style="font-size: 20px">');
  myWindow.document.write('<caption><center><h2>PREVIEW PAYMENT SLIP</h2></center></caption>');
  myWindow.document.write(plot_no);
  myWindow.document.write(name);
  myWindow.document.write(voucher);
  myWindow.document.write(date);
  myWindow.document.write(price);
  myWindow.document.write(amount);
  myWindow.document.write(remian);
  myWindow.document.write('</table>');
}
function closeWin() {
  myWindow.close();
}
</script>

</body>
</html>
				
					
			
				
					
				
						
				
				
				
					
				
				