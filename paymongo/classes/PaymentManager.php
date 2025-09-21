<?php
require_once 'config/database.php';
require_once 'config/config.php';

class PaymentManager {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->createTablesIfNotExists();
    }
    
    private function createTablesIfNotExists() {
        $query = "CREATE TABLE IF NOT EXISTS payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            payment_id VARCHAR(255) UNIQUE NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            currency VARCHAR(3) DEFAULT 'PHP',
            description TEXT,
            status VARCHAR(50) DEFAULT 'pending',
            reference_number VARCHAR(100) UNIQUE,
            customer_email VARCHAR(255),
            customer_name VARCHAR(255),
            checkout_url TEXT,
            receipt_url VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $this->conn->exec($query);
    }
    
    public function createPaymentLink($amount, $description, $customerEmail = null, $customerName = null) {
        // Generate unique reference number
        $referenceNumber = 'REF-' . time() . '-' . rand(1000, 9999);
        
        $data = [
            "data" => [
                "attributes" => [
                    "amount" => $amount * 100, // Convert to centavos
                    "currency" => "PHP",
                    "description" => $description,
                    "remarks" => "Payment for Order #" . $referenceNumber,
                    "redirect" => [
                        "success" => SITE_URL . "/success.php?ref=" . $referenceNumber,
                        "failed" => SITE_URL . "/failed.php?ref=" . $referenceNumber
                    ]
                ]
            ]
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, PAYMONGO_API_URL . "/links");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Basic " . base64_encode(PAYMONGO_SECRET_KEY . ":")
        ]);
        
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $response = json_decode($result, true);
        
        if ($httpCode == 200 && isset($response['data'])) {
            // Save to database
            $paymentId = $response['data']['id'];
            $checkoutUrl = $response['data']['attributes']['checkout_url'];
            
            $query = "INSERT INTO payments (payment_id, amount, description, reference_number, customer_email, customer_name, checkout_url) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$paymentId, $amount, $description, $referenceNumber, $customerEmail, $customerName, $checkoutUrl]);
            
            return [
                'success' => true,
                'checkout_url' => $checkoutUrl,
                'reference_number' => $referenceNumber,
                'payment_id' => $paymentId
            ];
        }
        
        return [
            'success' => false,
            'error' => $response['errors'] ?? 'Unknown error occurred'
        ];
    }
    
    public function updatePaymentStatus($paymentId, $status) {
        $query = "UPDATE payments SET status = ? WHERE payment_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$status, $paymentId]);
    }
    
    public function getPaymentByReference($referenceNumber) {
        $query = "SELECT * FROM payments WHERE reference_number = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$referenceNumber]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getPaymentById($paymentId) {
        $query = "SELECT * FROM payments WHERE payment_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$paymentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function generateReceiptUrl($referenceNumber) {
        return SITE_URL . "/receipt.php?ref=" . $referenceNumber;
    }
}