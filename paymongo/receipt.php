<?php
require_once 'classes/PaymentManager.php';

$referenceNumber = $_GET['ref'] ?? '';
$paymentManager = new PaymentManager();
$payment = $paymentManager->getPaymentByReference($referenceNumber);

if (!$payment) {
    header("HTTP/1.0 404 Not Found");
    echo "Receipt not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - <?php echo htmlspecialchars($payment['reference_number']); ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: #f7fafc;
            padding: 20px;
        }
        
        .receipt-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .receipt-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .receipt-header h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .receipt-header p {
            opacity: 0.9;
        }
        
        .receipt-body {
            padding: 40px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 30px;
        }
        
        .status-paid {
            background: #c6f6d5;
            color: #22543d;
        }
        
        .status-pending {
            background: #fef5e7;
            color: #975a16;
        }
        
        .status-failed {
            background: #fed7d7;
            color: #c53030;
        }
        
        .detail-section {
            margin-bottom: 30px;
        }
        
        .detail-section h3 {
            color: #2d3748;
            font-size: 18px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            color: #718096;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        
        .detail-value {
            color: #2d3748;
            font-weight: 600;
            font-size: 16px;
        }
        
        .amount-highlight {
            font-size: 32px;
            color: #48bb78;
            font-weight: 700;
        }
        
        .receipt-footer {
            background: #f7fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        
        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-secondary {
            background: #e2e8f0;
            color: #2d3748;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .footer-text {
            color: #718096;
            font-size: 12px;
            line-height: 1.5;
        }
        
        @media print {
            body { background: white; }
            .actions { display: none; }
            .receipt-container { box-shadow: none; }
        }
        
        @media (max-width: 600px) {
            .detail-grid { grid-template-columns: 1fr; }
            .receipt-body { padding: 20px; }
            .receipt-header { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h1><i class="fas fa-receipt"></i> Payment Receipt</h1>
            <p>Official transaction record</p>
        </div>
        
        <div class="receipt-body">
            <span class="status-badge status-<?php echo strtolower($payment['status']); ?>">
                <?php echo ucfirst($payment['status']); ?>
            </span>
            
            <div class="detail-section">
                <h3>Transaction Details</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Reference Number</span>
                        <span class="detail-value"><?php echo htmlspecialchars($payment['reference_number']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Payment ID</span>
                        <span class="detail-value"><?php echo htmlspecialchars(substr($payment['payment_id'], 0, 20) . '...'); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Date & Time</span>
                        <span class="detail-value"><?php echo date('M d, Y H:i:s', strtotime($payment['created_at'])); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Status</span>
                        <span class="detail-value"><?php echo ucfirst($payment['status']); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="detail-section">
                <h3>Payment Information</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Amount</span>
                        <span class="detail-value amount-highlight">₱<?php echo number_format($payment['amount'], 2); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Currency</span>
                        <span class="detail-value"><?php echo htmlspecialchars($payment['currency']); ?></span>
                    </div>
                    <div class="detail-item" style="grid-column: 1 / -1;">
                        <span class="detail-label">Description</span>
                        <span class="detail-value"><?php echo htmlspecialchars($payment['description']); ?></span>
                    </div>
                </div>
            </div>
            
            <?php if ($payment['customer_name'] || $payment['customer_email']): ?>
            <div class="detail-section">
                <h3>Customer Information</h3>
                <div class="detail-grid">
                    <?php if ($payment['customer_name']): ?>
                    <div class="detail-item">
                        <span class="detail-label">Name</span>
                        <span class="detail-value"><?php echo htmlspecialchars($payment['customer_name']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($payment['customer_email']): ?>
                    <div class="detail-item">
                        <span class="detail-label">Email</span>
                        <span class="detail-value"><?php echo htmlspecialchars($payment['customer_email']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="receipt-footer">
            <div class="actions">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print"></i> Print Receipt
                </button>
                <button onclick="downloadPDF()" class="btn btn-secondary">
                    <i class="fas fa-download"></i> Download PDF
                </button>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i> New Payment
                </a>
            </div>
            
            <div class="footer-text">
                <p>This is an official receipt for your payment transaction.</p>
                <p>For any inquiries, please contact our support team.</p>
                <p>Transaction processed securely via PayMongo</p>
            </div>
        </div>
    </div>
    
    <script>
        function downloadPDF() {
            // Simple PDF generation using browser print
            const originalTitle = document.title;
            document.title = 'Receipt-<?php echo $payment['reference_number']; ?>';
            window.print();
            document.title = originalTitle;
        }
    </script>
</body>
</html>