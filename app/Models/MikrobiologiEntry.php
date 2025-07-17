<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MikrobiologiEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id', 'no', 'string_field', 'tanggal_field', 'jam_field', 'keterangan'
    ];

    public function form()
    {
        return $this->belongsTo(MikrobiologiForm::class, 'form_id');
    }
}
