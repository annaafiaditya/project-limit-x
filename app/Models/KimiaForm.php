<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KimiaForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'no', 'tanggal', 'deskripsi', 'catatan', 'created_by'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function entries()
    {
        return $this->hasMany(KimiaEntry::class, 'form_id');
    }

    public function columns()
    {
        return $this->hasMany(KimiaColumn::class, 'form_id')->orderBy('urutan');
    }

    public function tables()
    {
        return $this->hasMany(KimiaTable::class, 'form_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function signatures()
    {
        return $this->hasMany(KimiaSignature::class, 'form_id');
    }
}
