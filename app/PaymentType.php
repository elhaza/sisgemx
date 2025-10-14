<?php

namespace App;

enum PaymentType: string
{
    case Tuition = 'tuition';
    case Books = 'books';
    case Uniform = 'uniform';
    case Enrollment = 'enrollment';
    case Other = 'other';
}
