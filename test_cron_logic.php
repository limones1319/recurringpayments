<?php

// Simulate simple inputs for logic verification
$paymentDate = '2025-02-15'; // Calculated payment due date
$deliveryDate = '2025-02-25'; // Expiry date (10 days later)

// Calculate logic based on my implementation
$daysDifference = (strtotime($deliveryDate) - strtotime($paymentDate)) / (60 * 60 * 24);
$halfDays = floor($daysDifference / 2);
$midDate = date('Y-m-d', strtotime('+' . $halfDays . ' days', strtotime($paymentDate)));

echo "Payment Date: $paymentDate\n";
echo "Delivery/Expiry Date: $deliveryDate\n";
echo "Days Diff: $daysDifference\n";
echo "Half Days: $halfDays\n";
echo "Mid Date (Target Reminder): $midDate\n";

// Test cases
$testDates = [
    $paymentDate => 'Original Payment Notification',
    $midDate => 'Mid-Period Reminder',
    $deliveryDate => 'Expiry Warning',
    '2025-02-20' => 'Random Date (Should be ignored)',
];

foreach ($testDates as $today => $description) {
    echo "\nTesting Today as: $today ($description)\n";
    $sendEmail = false;
    $additionalMessage = '';

    if ($today == $paymentDate) {
        $sendEmail = true;
        echo " -> Triggered: Payment Notification\n";
    }
    
    if ($today == $midDate) {
        $sendEmail = true;
        $additionalMessage = 'Friendly reminder: your subscription renewal is coming up. To ensure uninterrupted service, please complete payment soon.';
        echo " -> Triggered: Mid-period Reminder\n";
        echo " -> Message: $additionalMessage\n";
    }

    if ($today == $deliveryDate) {
        $sendEmail = true;
        $additionalMessage = 'Your subscription expires today. Please finalize payment to avoid service interruption.';
        echo " -> Triggered: Expiry Reminder\n";
         echo " -> Message: $additionalMessage\n";
    }

    if (!$sendEmail) {
        echo " -> No email sent.\n";
    }
}
