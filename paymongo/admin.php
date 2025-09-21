<?php
require_once 'classes/PaymentManager.php';

// Simple authentication (implement proper auth in production)
session_start();
if (!isset($_SESSION['admin']) && !isset($_POST['admin_password'])) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Login</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 400px; margin: 100px auto; padding: 20px; }
            input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }
            button { width: 100%; padding: 12px; background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer; }
        </style>
    </head>
    <body>
        <h2>Admin Login</h2>
        <form method="POST">
            <input type="password" name="admin_password" placeholder="Admin Password" required>
            <button type="submit">Login</button>
        </form>
    </body>
    </html>
    <?php
    exit;
}

if (isset($_POST['admin_password']) && $_POST['admin_password'] === 'admin123') {
    $_SESSION['admin'] = true;
}

if (!isset($_SESSION['admin'])) {
    die('Access denied');
}

$paymentManager = new PaymentManager();
$conn = $paymentManager->conn;

// Get payments with pagination
$page = $_GET['page'] ?? 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$stmt = $conn->prepare("SELECT * FROM payments ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->execute([$limit, $offset]);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count
$totalStmt = $conn->query("SELECT COUNT(*) FROM payments");
$totalPayments = $totalStmt->fetchColumn();
$totalPages = ceil($totalPayments / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Admin Panel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, sans-serif; background: #f5f5f5; }
        .header { background: white; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .container { max-width: 1200px; margin: 20px auto; padding: 0 20px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .stat-value { font-size: 32px; font-weight: bold; color: #333; }
        .stat-label { color: #666; font-size: 14px; }
        table { width: 100%; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; font-weight: 600; }
        .status { padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .status-paid { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-failed { background: #f8d7da; color: #721c24; }
        .pagination { display: flex; justify-content: center; margin-top: 20px; gap: 10px; }
        .page-btn { padding: 8px 12px; background: white; border: 1px solid #ddd; text-decoration: none; color: #333; border-radius: 4px; }
        .page-btn.active { background: #007cba; color: white; border-color: #007cba; }
        .logout { float: right; color: #dc3545; text-decoration: none; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Payment Admin Panel</h1>
        <a href="?logout=1" class="logout">Logout</a>
    </div>
    
    <div class="container">
        <?php
        // Get statistics
        $statsQuery = "SELECT 
            COUNT(*) as total_payments,
            SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as total_revenue,
            COUNT(CASE WHEN status = 'paid' THEN 1 END) as successful_payments,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_payments
            FROM payments";
        $stats = $conn->query($statsQuery)->fetch(PDO::FETCH_ASSOC);
        ?>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-value">₱<?php echo number_format($stats['total_revenue'], 2); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['total_payments']; ?></div>
                <div class="stat-label">Total Payments</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['successful_payments']; ?></div>
                <div class="stat-label">Successful</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['pending_payments']; ?></div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Amount</th>
                    <th>Customer</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($payment['reference_number']); ?></td>
                    <td>₱<?php echo number_format($payment['amount'], 2); ?></td>
                    <td><?php echo htmlspecialchars($payment['customer_name'] ?: 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars(substr($payment['description'], 0, 30) . '...'); ?></td>
                    <td>
                        <span class="status status-<?php echo $payment['status']; ?>">
                            <?php echo ucfirst($payment['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y H:i', strtotime($payment['created_at'])); ?></td>
                    <td>
                        <a href="receipt.php?ref=<?php echo $payment['reference_number']; ?>" target="_blank">View Receipt</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="page-btn <?php echo $i == $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
    
    <?php if (isset($_GET['logout'])): session_destroy(); header('Location: admin.php'); exit; endif; ?>
</body>
</html>