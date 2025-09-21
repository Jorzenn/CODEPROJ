<?php
require_once 'classes/PaymentManager.php';

$referenceNumber = $_GET['ref'] ?? '';
$paymentManager = new PaymentManager();
$payment = $paymentManager->getPaymentByReference($referenceNumber);

if ($payment) {
    $paymentManager->updatePaymentStatus($payment['payment_id'], 'paid');
    $receiptUrl = $paymentManager->generateReceiptUrl($referenceNumber);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
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
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .success-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        
        .success-icon {
            font-size: 64px;
            color: #48bb78;
            margin-bottom: 20px;
        }
        
        .success-title {
            font-size: 28px;
            color: #2d3748;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .success-message {
            color: #718096;
            font-size: 16px;
            margin-bottom: 30px;
        }
        
        .payment-details {
            background: #f7fafc;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: left;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .detail-label {
            color: #718096;
            font-weight: 500;
        }
        
        .detail-value {
            color: #2d3748;
            font-weight: 600;
        }
        
        .actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .btn {
            flex: 1;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            min-width: 140px;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a67d8;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #e2e8f0;
            color: #2d3748;
        }
        
        .btn-secondary:hover {
            background: #cbd5e0;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1 class="success-title">Payment Successful!</h1>
        <p class="success-message">Your payment has been processed successfully.</p>
        
        <?php if ($payment): ?>
        <div class="payment-details">
            <div class="detail-row">
                <span class="detail-label">Reference Number:</span>
                <span class="detail-value"><?php echo htmlspecialchars($payment['reference_number']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Amount:</span>
                <span class="detail-value">₱<?php echo number_format($payment['amount'], 2); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Description:</span>
                <span class="detail-value"><?php echo htmlspecialchars($payment['description']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value"><?php echo date('M d, Y H:i', strtotime($payment['created_at'])); ?></span>
            </div>
        </div>
        
        <div class="actions">
            <a href="<?php echo $receiptUrl; ?>" class="btn btn-primary">
                <i class="fas fa-receipt"></i> View Receipt
            </a>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-home"></i> New Payment
            </a>
        </div>
        <?php else: ?>
        <p>Payment details not found.</p>
        <a href="index.php" class="btn btn-primary">Go Back</a>
        <?php endif; ?>
    </div>
</body>
</html>