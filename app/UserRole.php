<?php

namespace App;

enum UserRole: string
{
    case Admin = 'admin';
    case FinanceAdmin = 'finance_admin';
    case Teacher = 'teacher';
    case Parent = 'parent';
    case Student = 'student';
}
