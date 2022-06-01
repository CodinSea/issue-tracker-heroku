<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Comment extends Model
{
    use HasFactory, Sortable;

    public $sortable = [
        'remark',
        'created_at'
    ]; 

    public function lastNameCommenterSortable($query, $direction) {
        return $query->leftJoin('users', 'comments.commenter_id', '=', 'users.id')
                     ->orderBy('last_name', $direction)
                     ->select('comments.*');
    }   

    public function commenter() {
        return $this->belongsTo(User::class, "commenter_id", "id");
    }
}