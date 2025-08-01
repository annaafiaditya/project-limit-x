<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MikrobiologiEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id', 'data'
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function form()
    {
        return $this->belongsTo(MikrobiologiForm::class, 'form_id');
    }
}
