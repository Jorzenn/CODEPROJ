<?php
require_once 'classes/PaymentManager.php';

$referenceNumber = $_GET['ref'] ?? '';
$paymentManager = new PaymentManager();
$payment = $paymentManager->getPaymentByReference($referenceNumber);

if ($payment) {
    $paymentManager->updatePaymentStatus($payment['payment_id'], 'failed');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
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
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .failed-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        
        .failed-icon {
            font-size: 64px;
            color: #e53e3e;
            margin-bottom: 20px;
        }
        
        .failed-title {
            font-size: 28px;
            color: #2d3748;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .failed-message {
            color: #718096;
            font-size: 16px;
            margin-bottom: 30px;
        }
        
        .error-details {
            background: #fed7d7;
            color: #c53030;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
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
    <div class="failed-container">
        <div class="failed-icon">
            <i class="fas fa-times-circle"></i>
        </div>
        
        <h1 class="failed-title">Payment Failed</h1>
        <p class="failed-message">Unfortunately, your payment could not be processed.</p>
        
        <div class="error-details">
            <p><strong>Common reasons for payment failure:</strong></p>
            <ul style="text-align: left; margin-top: 10px;">
                <li>Insufficient funds</li>
                <li>Invalid card details</li>
                <li>Network connection issues</li>
                <li>Payment method not supported</li>
            </ul>
        </div>
        
        <?php if ($payment): ?>
        <p style="color: #718096; margin-bottom: 20px;">
            Reference: <?php echo htmlspecialchars($payment['reference_number']); ?>
        </p>
        <?php endif; ?>
        
        <div class="actions">
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-redo"></i> Try Again
            </a>
            <a href="mailto:support@example.com" class="btn btn-secondary">
                <i class="fas fa-envelope"></i> Contact Support
            </a>
        </div>
    </div>
</body>
</html>