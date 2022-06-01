<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Ticket extends Model
{
    use HasFactory, Sortable;

    public $sortable = [
        'title',
        'description',
        'status',
        'created_at'
    ];

    protected $fillable = [
        'developer_id'
    ];

    public function lastNameSubmitterSortable($query, $direction) {
        return $query->leftJoin('users', 'tickets.submitter_id', '=', 'users.id')
                     ->orderBy('last_name', $direction)
                     ->select('tickets.*');
    }

    public function lastNameDeveloperSortable($query, $direction) {
        return $query->leftJoin('users', 'tickets.developer_id', '=', 'users.id')
                     ->orderBy('last_name', $direction)
                     ->select('tickets.*');
    }

    public function submitter() {
        return $this->belongsTo(User::class, "submitter_id", "id");
    }

    public function developer() {
        return $this->belongsTo(User::class, "developer_id", "id");
    }

    public function project() {
        return $this->belongsTo(Project::class, "project_id", "id");
    }
}