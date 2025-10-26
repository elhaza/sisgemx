<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Grace Period Days
    |--------------------------------------------------------------------------
    |
    | Number of days after the due date before late fees start being applied.
    |
    */
    'grace_period_days' => env('GRACE_PERIOD_DAYS', 0),

    /*
    |--------------------------------------------------------------------------
    | Late Fee Type
    |--------------------------------------------------------------------------
    |
    | Type of late fee calculation:
    | - ONCE: A one-time fixed fee
    | - DAILY: Fee calculated per day late
    | - MONTHLY: Fee calculated per month late
    |
    */
    'late_fee_type' => env('LATE_FEE_TYPE', 'MONTHLY'),

    /*
    |--------------------------------------------------------------------------
    | Late Fee Rate
    |--------------------------------------------------------------------------
    |
    | Percentage rate applied when calculating percentage-based late fees.
    | Example: 0.05 = 5% of the tuition amount
    |
    */
    'late_fee_rate' => env('LATE_FEE_RATE', 0.00),

    /*
    |--------------------------------------------------------------------------
    | Late Fee Daily Amount
    |--------------------------------------------------------------------------
    |
    | Fixed amount for daily late fees.
    | If set to 0, percentage-based calculation will be used instead.
    |
    */
    'late_fee_daily_amount' => env('LATE_FEE_DAILY_AMOUNT', 0.00),

    /*
    |--------------------------------------------------------------------------
    | Late Fee Monthly Amount
    |--------------------------------------------------------------------------
    |
    | Fixed amount for monthly late fees.
    | If set to 0, percentage-based calculation will be used instead.
    |
    */
    'late_fee_monthly_amount' => env('LATE_FEE_MONTHLY_AMOUNT', 0.00),

    /*
    |--------------------------------------------------------------------------
    | Late Fee Amount (Deprecated)
    |--------------------------------------------------------------------------
    |
    | Kept for backwards compatibility. Use late_fee_daily_amount or
    | late_fee_monthly_amount instead based on your late_fee_type.
    |
    */
    'late_fee_amount' => env('LATE_FEE_DAILY_AMOUNT', env('LATE_FEE_MONTHLY_AMOUNT', 0.00)),

    /*
    |--------------------------------------------------------------------------
    | Tuition Due Day
    |--------------------------------------------------------------------------
    |
    | Day of the month when tuition payments are due (1-31).
    | Example: 10 means tuition is due on the 10th of each month.
    |
    */
    'tuition_due_day' => env('TUITION_DUE_DAY', 10),
];
