<?php

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

$STATUS_FLOW = [
    'pending_payment' => ['payment_uploaded', 'rejected', 'cancelled'],
    'payment_uploaded' => ['payment_verified', 'rejected', 'cancelled'],
    'payment_verified' => ['processing', 'rejected'],
    'processing' => ['ready_for_pickup', 'rejected'],
    'ready_for_pickup' => ['claimed'],
    'claimed' => [],
    'rejected' => [],
    'cancelled' => []
];