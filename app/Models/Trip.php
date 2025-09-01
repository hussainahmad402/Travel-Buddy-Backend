<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'destination',
        'start_date',
        'end_date',
        'notes',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

}
