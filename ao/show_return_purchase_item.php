<?php
include 'includes/connect.php';
// 2. The Query
$query = "SELECT r.*, p.batch_no, p.expiry_date, p.per_item_price, p.mfg_date, p.quantity as original_qty, u.u_name AS requested_by, i.name AS item_name, c.name AS category_name
          FROM return_purchase_items r
          JOIN purchase_items p ON r.purchase_id = p.id
          JOIN users u ON r.user_id = u.id
          JOIN items i ON r.item_id = i.id
          JOIN categories c ON i.category_id = c.id
          WHERE r.return_purchase_item_status = 1 ";

$result = mysqli_query($con, $query);

// 3. Initialize an empty array to store rows
$pending_returns = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pending_returns[] = $row;
    }
} else {
    echo "Query Error: " . mysqli_error($con);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authorized Return Approval | Medical System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 text-gray-900 antialiased">

<div class="max-w-7xl mx-auto px-6 py-10">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Return Authorization Queue</h1>
            <p class="text-sm text-gray-500">Review and approve inventory returns for pharmaceutical supplies.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="dashboard.php" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2.5 px-4 rounded-lg shadow-sm transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                GO TO DASHBOARD
            </a>

            <div class="flex items-center bg-white p-2 rounded-lg border shadow-sm h-10">
                <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full uppercase">
                    <?php echo count($pending_returns); ?> Pending Requests
                </span>
            </div>
        </div>
    </div>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
    <div id="success-alert" class="fixed top-5 right-5 z-50 bg-emerald-100 border border-emerald-400 text-emerald-700 px-6 py-3 rounded-lg shadow-lg transition-opacity duration-500">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            <span>Return #<?php echo htmlspecialchars($_GET['id']); ?> processed successfully.</span>
        </div>
    </div>

    <script>
        setTimeout(function() {
            const alert = document.getElementById('success-alert');
            if (alert) {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000); 
    </script>
<?php endif; ?>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="p-4">Request Info</th>
                    <th class="p-4">Medical Details</th>
                    <th class="p-4">Return Stats</th>
                    <th class="p-4">Requested By</th>
                    <th class="p-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($pending_returns as $row): ?>
                <tr class="hover:bg-blue-50/20 transition-colors">
                    <td class="p-4">
                        <div class="text-sm font-bold text-gray-800">#<?php echo $row['return_purchase_item_id']; ?></div>
                        <div class="text-xs text-blue-600 font-mono mt-1">Inv: <?php echo $row['invoice_no']; ?></div>
                        <div class="text-[10px] text-gray-400 mt-1"><?php echo $row['created_at']; ?></div>
                    </td>

                    <td class="p-4 text-sm">
                        <div class="font-semibold text-gray-800 uppercase"><?php echo $row['item_name'] ?? 'Medical Item'; ?></div>
                        <div class="flex gap-2 mt-1">
                            <span class="text-[10px] bg-blue-50 px-2 py-0.5 rounded border"><?php echo $row['category_name']; ?></span>
                            <?php if($row['batch_no'] != ''){ ?>
                            <span class="text-[10px] bg-gray-100 px-2 py-0.5 rounded border">Batch: <?php echo $row['batch_no']; ?></span>
                            <?php } ?>
                            <?php if($row['expiry_date'] != '0000-00-00'){ ?>
                            <span class="text-[10px] bg-red-50 text-red-600 px-2 py-0.5 rounded border border-red-100">Exp: <?php echo $row['expiry_date']; ?></span>
                            <?php } ?>
                        </div>
                    </td>

                    <td class="p-4">
                        <div class="text-sm font-bold text-gray-800"><?php echo $row['return_quantity']; ?> Units</div>
                        <div class="text-sm text-green-600 font-semibold">Rs.<?php echo number_format((float)($row['return_amount'] ?? 0), 2); ?></div>
                    </td>

                    <td class="p-4">
                        <div class="text-sm text-gray-700"><?php echo $row['requested_by']; ?></div>
                        <div class="text-[10px] text-gray-400 uppercase tracking-tighter italic font-semibold">Verification Required</div>
                    </td>

                    <td class="p-4">
                        <div class="flex items-center justify-center gap-2">
                            <form action="process_return_purchase_item.php" method="POST" class="flex gap-2">
                                <input type="hidden" name="return_id" value="<?php echo $row['return_purchase_item_id']; ?>">
                                
                                <input type="text" name="note" placeholder="Optional note..." 
                                       class="text-xs border rounded px-2 py-1.5 w-32 focus:ring-1 focus:ring-blue-500 outline-none">
                                
                                <button name="action" value="approve" class="bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold py-1.5 px-3 rounded shadow-sm transition">
                                    APPROVE
                                </button>
                                
                                <button name="action" value="reject" class="bg-rose-50 hover:bg-rose-100 text-rose-600 text-[11px] font-bold py-1.5 px-3 rounded border border-rose-200 transition">
                                    REJECT
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if (empty($pending_returns)): ?>
            <div class="p-10 text-center">
                <p class="text-gray-400 italic">No pending return requests found in the system.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>