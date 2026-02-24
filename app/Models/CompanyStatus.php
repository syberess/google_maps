<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'color', 'icon', 'order'];

    /**
     * İlişkiler
     */
    public function companies()
    {
        return $this->hasMany(Company::class, 'status_id');
    }

    /**
     * Firma sayısı
     */
    public function getCountAttribute()
    {
        return $this->companies()->count();
    }

    /**
     * Slug'a göre bul
     */
    public static function findBySlug($slug)
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * Varsayılan durum (Prospekt)
     */
    public static function getDefault()
    {
        return static::where('slug', 'prospekt')->first() ?? static::first();
    }
}
