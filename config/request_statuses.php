<?php

/*
|--------------------------------------------------------------------------
| ALL VALID STATUSES
|--------------------------------------------------------------------------
*/
$REQUEST_STATUSES = [
    'pending_payment',
    'payment_uploaded',
    'payment_verified',
    'processing',
    'ready_for_pickup',
    'claimed',
    'rejected',
    'cancelled'
];

/*
|--------------------------------------------------------------------------
| VALID STATUS TRANSITIONS (BUSINESS RULES)
|--------------------------------------------------------------------------
*/
$STATUS_FLOW = [
    'pending_payment' => ['payment_uploaded', 'rejected', 'cancelled'],
    'payment_uploaded' => ['payment_verified', 'rejected', 'cancelled'],
    'payment_verified' => ['processing', 'rejected', 'cancelled'],
    'processing' => ['ready_for_pickup', 'rejected', 'cancelled'],
    'ready_for_pickup' => ['claimed'],
    'claimed' => [],
    'rejected' => [],
    'cancelled' => []
];

/*
|--------------------------------------------------------------------------
| HELPER FUNCTION (IMPORTANT FIX)
|--------------------------------------------------------------------------
*/
function can_transition($current_status, $new_status, $STATUS_FLOW) {

    if (!isset($STATUS_FLOW[$current_status])) {
        return false;
    }

    return in_array($new_status, $STATUS_FLOW[$current_status], true);
}