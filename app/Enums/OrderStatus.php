<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending   = 'pending';
    case Paid      = 'paid';
    case Shipped   = 'shipped';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Failed    = 'failed';
    case CancellationPending = 'cancellation_pending';
}
