<?php
require_once 'config.php';
$current_purchase_no = mysqli_num_rows(mysqli_query($con, "SELECT DISTINCT `invoice_no` FROM `purchase_items`"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Billing System</title>
    <style>
        body { font-family: sans-serif; margin: 20px; background: #f4f7f6; }
        .form-container { background: white; padding: 20px; border-radius: 8px; max-width: 600px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .field { display: flex; flex-direction: column; }
        label { font-weight: bold; margin-bottom: 5px; font-size: 0.9em; }
        input, select { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .full-width { grid-column: span 2; }
        button { margin-top: 20px; padding: 10px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #218838; }
    </style>
</head>
<body>
<form method = "POST" action = "process_add_bill.php">
<div class="form-container" style="max-width: 900px;">
    <h2>Create Multi-Item Bill</h2>
    
    <div class="grid">
        <div class="field">
            <label>Receipt No.</label>
            <input type="text" id="receipt_no" value = "<?php echo $current_purchase_no; ?>" required name="invoice_no" >
        </div>
        <div class="field">
            <label>Invoice No.</label>
            <input type="text" id="invoice_no"  required name="party_invoice_no">
        </div>
        <div class="field">
            <label>Party Name</label>
            <input list="party" name="party_id" id="party_id" class = "form-control">
            <datalist id="party">
    			<?php
    			$categories = mysqli_query($con, "SELECT * FROM `parties` WHERE status = '1' ORDER BY name");
    			if (mysqli_num_rows($categories) > 0) {
    				while ($row_category = mysqli_fetch_array($categories)) {
    					$cat_id = $row_category['id'];
    					$cat_name = $row_category['name'];
    					$cat_address = $row_category['address'];
    					echo '<option value="'.$cat_id.'">'.$cat_name.' ('.$cat_address.')</option>';
    				}
    			}
    			?>
            </datalist>	
        </div>
        <div class="field">
            <label>Company</label>
    		<select name="company_id" class="form-control" required>
    			<option value="">Select Company</option>
    			<?php
    			$compaines = mysqli_query($con, "SELECT * FROM `item_companies` WHERE status = '1' ORDER BY name");
    			if (mysqli_num_rows($compaines) > 0) {
    				while ($row_company = mysqli_fetch_array($compaines)) {
    					$com_id = $row_company['id'];
    					$com_name = $row_company['name'];
    					echo '<option value="'.$com_id.'">'.$com_name.'</option>';
    				}
    			}
    			?>
    		</select>        
		</div>
    </div>

    <hr>

    <table id="itemsTable" style="width:100%; margin-top:20px; border-collapse: collapse;">
        <thead>
            <tr style="background:#eee;">
                <th>Item</th>
                <th>Trade Name</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>Total</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr><datalist id="itemList"></datalist>
                <td><input type="text" class="item_desc" name = "item_desc[]" list="itemList" onchange="fetchItemPrice(this)"></td>
                <td><input type="text" class="item_desc" name = "item_desc[]"></td>
                <td><input type="number" class="qty" name = "qty[]" oninput="calculateRow(this)"></td>
                <td><input type="number" class="rate" name = "rate[]" oninput="calculateRow(this)"></td>
                <td><input type="number" class="row_total" name = "row_total" readonly></td>
                <td><button onclick="removeRow(this)">❌</button></td>
            </tr>
        </tbody>
    </table>
    
    <button type="button" onclick="addRow()" style="background:#007bff;">+ Add Item</button>
    
    <div style="text-align: right; margin-top: 20px;">
        <h3>Grand Total: <span id="grand_total">0.00</span></h3>
        <input type = "submit" value = "SAVE BILL DATA" name = "save_bill" />
        <button type="button" onclick="submitBill()" style="background:#28a745; padding: 15px 30px;">Save</button>
    </div>
</div>
</form>
<script>
    // Simple logic to calculate total automatically
    function calculateTotal() {
        const qty = document.getElementById('quantity').value || 0;
        const rate = document.getElementById('per_item_rate').value || 0;
        document.getElementById('total_amount').value = (qty * rate).toFixed(2);
    }
function addRow() {
    const tbody = document.querySelector("#itemsTable tbody");
    const row = `<tr>
        <td><input type="text" class="item_desc" name = "item_desc[]" list="itemList" onchange="fetchItemPrice(this)"></td>
        <td><input type="text" class="item_desc" name = "item_desc[]"></td>
        <td><input type="number" class="qty" name = "qty[]" oninput="calculateRow(this)"></td>
        <td><input type="number" class="rate" name = "rate[]" oninput="calculateRow(this)"></td>
        <td><input type="number" class="row_total" readonly></td>
        <td><button type="button" onclick="removeRow(this)">❌</button></td>
    </tr>`;
    tbody.insertAdjacentHTML('beforeend', row);
}

function calculateRow(input) {
    const row = input.closest('tr');
    const qty = row.querySelector('.qty').value || 0;
    const rate = row.querySelector('.rate').value || 0;
    
    // Update individual row total
    row.querySelector('.row_total').value = (qty * rate).toFixed(2);
    
    // Update the bottom grand total
    updateGrandTotal();
}

async function submitBill() {
    const items = [];
    document.querySelectorAll("#itemsTable tbody tr").forEach(row => {
        items.push({
            trade_name: row.querySelector('.t_name').value,
            item: row.querySelector('.item_desc').value,
            quantity: row.querySelector('.qty').value,
            rate: row.querySelector('.rate').value,
            total: row.querySelector('.row_total').value
        });
    });

    const payload = {
        receipt_no: document.getElementById('receipt_no').value,
        invoice_no: document.getElementById('invoice_no').value,
        party: document.getElementById('party').value,
        company: document.getElementById('company').value,
        items: items // Array of items
    };

    const response = await fetch('save_bill.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });
    
    const res = await response.json();
    alert(res.success ? "Bill Saved!" : "Error: " + res.message);
}
function removeRow(button) {
    const row = button.closest('tr');
    row.remove();
    updateGrandTotal();
}

function updateGrandTotal() {
    let grandTotal = 0;
    document.querySelectorAll('.row_total').forEach(el => {
        grandTotal += parseFloat(el.value || 0);
    });
    document.getElementById('grand_total').innerText = grandTotal.toFixed(2);
}
</script>

<script>
window.onload = function() {
    loadInventory();
};
async function loadInventory() {
    try {
        const response = await fetch('get_items.php');
        const items = await response.json();
        const dataList = document.getElementById('itemList');

        items.forEach(item => {
            const option = document.createElement('option');
            // We show the Item Name, but you can also include Trade Name
            option.value = item.id; 
            // option.value = item.name; 
            option.label = item.name + item.category_title;
            dataList.appendChild(option);
        });
    } catch (error) {
        console.error("Could not load items:", error);
    }
}    
</script>
</body>
</html>