<?php
require_once 'classes/PaymentManager.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $paymentManager = new PaymentManager();
    
    $amount = floatval($_POST['amount']);
    $description = $_POST['description'] ?? 'Payment';
    $customerName = $_POST['customer_name'] ?? '';
    $customerEmail = $_POST['customer_email'] ?? '';
    
    if ($amount < 100) {
        $error = "Minimum amount is PHP 100.00";
    } else {
        $result = $paymentManager->createPaymentLink($amount, $description, $customerEmail, $customerName);
        
        if ($result['success']) {
            header("Location: " . $result['checkout_url']);
            exit();
        } else {
            $error = "Error creating payment: " . json_encode($result['error']);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayMongo Payment Gateway</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .payment-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #2d3748;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .header p {
            color: #718096;
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2d3748;
            font-weight: 500;
            font-size: 14px;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            outline: none;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .amount-input {
            position: relative;
        }
        
        .amount-input::before {
            content: '₱';
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #718096;
            font-weight: 600;
            z-index: 1;
        }
        
        .amount-input input {
            padding-left: 35px;
        }
        
        .pay-button {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .pay-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .pay-button:active {
            transform: translateY(0);
        }
        
        .error {
            background: #fed7d7;
            color: #c53030;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .receipt-lookup {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }
        
        .receipt-lookup h3 {
            color: #2d3748;
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .lookup-form {
            display: flex;
            gap: 10px;
        }
        
        .lookup-form input {
            flex: 1;
            padding: 10px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .lookup-btn {
            background: #48bb78;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
        }
        
        .lookup-btn:hover {
            background: #38a169;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="header">
            <h1><i class="fas fa-credit-card"></i> Secure Payment</h1>
            <p>Complete your payment securely with PayMongo</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="customer_name">Full Name</label>
                <input type="text" id="customer_name" name="customer_name" placeholder="Enter your full name">
            </div>
            
            <div class="form-group">
                <label for="customer_email">Email Address</label>
                <input type="email" id="customer_email" name="customer_email" placeholder="Enter your email address">
            </div>
            
            <div class="form-group">
                <label for="amount">Amount (PHP) *</label>
                <div class="amount-input">
                    <input type="number" id="amount" name="amount" min="100" step="0.01" required placeholder="0.00">
                </div>
                <small style="color: #718096; font-size: 12px;">Minimum amount: ₱100.00</small>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" placeholder="What is this payment for?">Payment for services</textarea>
            </div>
            
            <button type="submit" class="pay-button">
                <i class="fas fa-lock"></i>
                Pay Securely
            </button>
        </form>
        
        <div class="receipt-lookup">
            <h3><i class="fas fa-receipt"></i> View Receipt</h3>
            <form class="lookup-form" action="receipt.php" method="GET">
                <input type="text" name="ref" placeholder="Enter Reference Number" required>
                <button type="submit" class="lookup-btn">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>
</body>
</html>