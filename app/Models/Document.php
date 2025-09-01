<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',   // 👈 Add this
        'file_path',
        'file_name',
        'file_type',
    ];
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

}
