<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'google_place_id',
        'phone',
        'website',
        'address',
        'latitude',
        'longitude',
        'rating',
        'review_count',
        'category',
        'types',
        'status_id',
        'source',
        'notes',
    ];

    protected $casts = [
        'types' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'rating' => 'decimal:1',
    ];

    /**
     * İlişkiler
     */
    public function status()
    {
        return $this->belongsTo(CompanyStatus::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class)->orderBy('created_at', 'desc');
    }

    public function enrichedData()
    {
        return $this->hasOne(EnrichedData::class);
    }

    /**
     * Scope'lar
     */
    public function scopeByStatus($query, $status)
    {
        return $query->whereHas('status', fn($q) => $q->where('slug', $status));
    }

    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('category', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%");
        });
    }

    /**
     * Accessor'lar
     */
    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= mb_substr($word, 0, 1);
        }
        return mb_strtoupper($initials);
    }

    public function getGoogleMapsUrlAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }
        return "https://www.google.com/maps/search/" . urlencode($this->address);
    }

    public function getFormattedPhoneAttribute()
    {
        return $this->phone ? preg_replace('/[^0-9+]/', '', $this->phone) : null;
    }

    /**
     * Zengin veri kontrolü
     */
    public function hasEnrichedData()
    {
        return $this->enrichedData !== null && 
               ($this->enrichedData->email || $this->enrichedData->secondary_phone);
    }

    /**
     * Son etkinlik
     */
    public function getLastActivityAttribute()
    {
        return $this->activities()->first();
    }
}
