<?php
require_once 'classes/PaymentManager.php';

// Verify webhook (you should implement proper webhook verification in production)
$input = file_get_contents("php://input");
$event = json_decode($input, true);

if ($event && isset($event['data'])) {
    $paymentManager = new PaymentManager();
    
    $paymentId = $event['data']['id'];
    $status = $event['data']['attributes']['status'];
    
    // Update payment status
    $paymentManager->updatePaymentStatus($paymentId, $status);
    
    // Log the event
    $logMessage = date('Y-m-d H:i:s') . " - Payment {$paymentId}: {$status}\n";
    file_put_contents('logs/webhook.log', $logMessage, FILE_APPEND | LOCK_EX);
}

http_response_code(200);
echo "OK";