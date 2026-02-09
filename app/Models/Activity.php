<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'company_id',
        'type',
        'title',
        'description',
        'status',
        'scheduled_at',
        'completed_at',
        'user_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Etkinlik tipleri
     */
    const TYPES = [
        'search' => ['label' => 'Arama yapıldı', 'icon' => 'search', 'color' => 'blue'],
        'call' => ['label' => 'Telefon görüşmesi', 'icon' => 'phone', 'color' => 'green'],
        'meeting' => ['label' => 'Toplantı', 'icon' => 'users', 'color' => 'purple'],
        'proposal' => ['label' => 'Teklif gönderildi', 'icon' => 'document', 'color' => 'amber'],
        'note' => ['label' => 'Not', 'icon' => 'pencil', 'color' => 'gray'],
        'email' => ['label' => 'E-posta', 'icon' => 'mail', 'color' => 'indigo'],
    ];

    /**
     * İlişkiler
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tip bilgisi
     */
    public function getTypeInfoAttribute()
    {
        return self::TYPES[$this->type] ?? self::TYPES['note'];
    }

    /**
     * Tip etiketi
     */
    public function getTypeLabelAttribute()
    {
        return $this->typeInfo['label'];
    }

    /**
     * Tip rengi
     */
    public function getTypeColorAttribute()
    {
        return $this->typeInfo['color'];
    }

    /**
     * Tamamlandı mı?
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Beklemede mi?
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Tamamla
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }
}
