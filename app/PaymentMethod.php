<?php

namespace App;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Transfer = 'transfer';
    case Card = 'card';
    case Check = 'check';
}
