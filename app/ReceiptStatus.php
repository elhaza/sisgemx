<?php

namespace App;

enum ReceiptStatus: string
{
    case Pending = 'pending';
    case Validated = 'validated';
    case Rejected = 'rejected';
}
