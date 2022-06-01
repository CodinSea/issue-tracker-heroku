<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Attachment extends Model
{
    use HasFactory, Sortable;

    public $sortable = [
        'attachment',
        'description',
        'created_at'
    ];

    public function lastNameUploaderSortable($query, $direction) {
        return $query->leftJoin('users', 'attachments.uploader_id', '=', 'users.id')
                     ->orderBy('last_name', $direction)
                     ->select('attachments.*');
    }

    public function uploader() {
        return $this->belongsTo(User::class, "uploader_id", "id");
    }
}