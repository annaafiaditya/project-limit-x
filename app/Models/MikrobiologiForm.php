<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MikrobiologiForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'no', 'tgl_inokulasi', 'tgl_pengamatan', 'created_by'
    ];

    public function entries()
    {
        return $this->hasMany(MikrobiologiEntry::class, 'form_id');
    }

    public function signatures()
    {
        return $this->hasMany(MikrobiologiSignature::class, 'form_id');
    }

    public function columns()
    {
        return $this->hasMany(MikrobiologiColumn::class, 'form_id')->orderBy('urutan');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
