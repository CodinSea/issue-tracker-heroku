<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class History extends Model
{
    use HasFactory, Sortable;

    public $sortable = [
        'property',
        'old_value', 
        'new_value',
        'updated_at'
    ];
}
