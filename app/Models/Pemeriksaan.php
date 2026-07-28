<?php

namespace App\Models;

use App\Models\Scopes\PosyanduScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy([PosyanduScope::class])]
class Pemeriksaan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'balita_id',
        'posyandu_id',
        'tanggal_pemeriksaan',
        'umur_bulan',
        'tinggi_badan',
        'berat_badan',
        'status_stunting',
        'status_dt',
        'zscore_tb_u',
        'status_berat_badan',
        'zscore_bb_u',
        'catatan',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_pemeriksaan' => 'date',
            'tinggi_badan' => 'decimal:2',
            'berat_badan' => 'decimal:2',
            'zscore_tb_u' => 'decimal:2',
            'zscore_bb_u' => 'decimal:2',
        ];
    }

    /**
     * Get the balita for this pemeriksaan.
     */
    public function balita(): BelongsTo
    {
        return $this->belongsTo(Balita::class);
    }

    /**
     * Get the posyandu for this pemeriksaan.
     */
    public function posyandu(): BelongsTo
    {
        return $this->belongsTo(Posyandu::class);
    }

    /**
     * Get the status label with proper formatting.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_stunting) {
            'Normal' => 'Normal',
            'Risk of Stunting' => 'Risk of Stunting',
            'Stunting' => 'Stunting',
            default => 'Belum Diperiksa',
        };
    }

    /**
     * Get the CSS class for status badge.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status_stunting) {
            'Normal' => 'bg-emerald-100 text-emerald-800',
            'Risk of Stunting' => 'bg-amber-100 text-amber-800',
            'Stunting' => 'bg-rose-100 text-rose-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the status label with proper formatting for BB/U.
     */
    public function getBbStatusLabelAttribute(): string
    {
        return $this->status_berat_badan ?? 'Belum Diperiksa';
    }

    /**
     * Get the CSS class for BB/U status badge.
     */
    public function getBbStatusColorAttribute(): string
    {
        return match ($this->status_berat_badan) {
            'Sangat Kurang' => 'bg-rose-100 text-rose-800',
            'Kurang' => 'bg-amber-100 text-amber-800',
            'Normal' => 'bg-emerald-100 text-emerald-800',
            'Risiko Berat Badan Lebih' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
