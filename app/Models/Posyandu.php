<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Posyandu extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'rw',
        'alamat',
    ];

    /**
     * Get all users (petugas) for this posyandu.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all balitas registered at this posyandu.
     */
    public function balitas(): HasMany
    {
        return $this->hasMany(Balita::class);
    }

    /**
     * Get all pemeriksaan records for this posyandu.
     */
    public function pemeriksaans(): HasMany
    {
        return $this->hasMany(Pemeriksaan::class);
    }
}
