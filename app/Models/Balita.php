<?php

namespace App\Models;

use App\Models\Scopes\PosyanduScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

#[ScopedBy([PosyanduScope::class])]
class Balita extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'posyandu_id',
        'nik',
        'nama',
        'jenis_kelamin',
        'tanggal_lahir',
        'nama_orangtua',
        'alamat',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    /**
     * Get the posyandu that this balita belongs to.
     */
    public function posyandu(): BelongsTo
    {
        return $this->belongsTo(Posyandu::class);
    }

    /**
     * Get all pemeriksaan records for this balita.
     */
    public function pemeriksaans(): HasMany
    {
        return $this->hasMany(Pemeriksaan::class);
    }

    /**
     * Get the latest pemeriksaan for this balita.
     */
    public function latestPemeriksaan(): HasOne
    {
        return $this->hasOne(Pemeriksaan::class)->latestOfMany('tanggal_pemeriksaan');
    }

    /**
     * Calculate age in months from tanggal_lahir.
     */
    public function getUmurBulanAttribute(): int
    {
        return (int) $this->tanggal_lahir->diffInMonths(Carbon::now());
    }

    /**
     * Get formatted age string (e.g. "2 tahun 3 bulan").
     */
    public function getUmurFormatAttribute(): string
    {
        $totalBulan = $this->umur_bulan;
        $tahun = intdiv($totalBulan, 12);
        $bulan = $totalBulan % 12;

        if ($tahun > 0 && $bulan > 0) {
            return "{$tahun} tahun {$bulan} bulan";
        } elseif ($tahun > 0) {
            return "{$tahun} tahun";
        }

        return "{$bulan} bulan";
    }
}
