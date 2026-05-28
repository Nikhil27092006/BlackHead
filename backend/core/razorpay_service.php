<?php
class RazorpayService {
    private $keyId;
    private $keySecret;
    private $baseUrl = "https://api.razorpay.com/v1/";

    public function __construct($keyId, $keySecret) {
        $this->keyId = $keyId;
        $this->keySecret = $keySecret;
    }

    /**
     * Create a Razorpay Order
     */
    public function createOrder($amount, $receiptId, $currency = "INR") {
        $data = [
            'amount'          => $amount * 100, // Amount in paise
            'currency'        => $currency,
            'receipt'         => $receiptId,
            'payment_capture' => 1 // Auto capture
        ];

        return $this->makeRequest('orders', $data);
    }

    /**
     * Verify Payment Signature
     */
    public function verifySignature($orderId, $paymentId, $signature) {
        $generated_signature = hash_hmac('sha256', $orderId . "|" . $paymentId, $this->keySecret);
        return hash_equals($generated_signature, $signature);
    }

    private function makeRequest($endpoint, $data) {
        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->keyId . ":" . $this->keySecret);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        } else {
            error_log("Razorpay API Error: " . $response);
            return null;
        }
    }
}
?>
