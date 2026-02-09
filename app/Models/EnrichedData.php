<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnrichedData extends Model
{
    protected $table = 'enriched_data';

    protected $fillable = [
        'company_id',
        'email',
        'secondary_phone',
        'mobile_phone',
        'fax',
        'linkedin',
        'facebook',
        'instagram',
        'twitter',
        'contact_person',
        'contact_title',
        'additional_info',
        'source',
    ];

    /**
     * İlişkiler
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Sosyal medya var mı?
     */
    public function hasSocialMedia()
    {
        return $this->linkedin || $this->facebook || $this->instagram || $this->twitter;
    }

    /**
     * İletişim bilgisi var mı?
     */
    public function hasContactInfo()
    {
        return $this->email || $this->secondary_phone || $this->mobile_phone;
    }

    /**
     * Tüm telefonlar
     */
    public function getAllPhones()
    {
        return array_filter([
            $this->secondary_phone,
            $this->mobile_phone,
            $this->fax,
        ]);
    }

    /**
     * Sosyal medya linkleri
     */
    public function getSocialLinks()
    {
        return array_filter([
            'linkedin' => $this->linkedin,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'twitter' => $this->twitter,
        ]);
    }
}
