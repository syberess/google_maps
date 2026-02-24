<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Company;
use App\Models\CompanyStatus;
use App\Models\EnrichedData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Test için varsayılan statüler oluştur
        $this->seed(\Database\Seeders\CompanyStatusSeeder::class);
    }

    public function test_firma_olusturulabilir()
    {
        $company = Company::factory()->create([
            'name' => 'Test Şirketi',
            'phone' => '+90 212 555 1234',
        ]);

        $this->assertDatabaseHas('companies', [
            'name' => 'Test Şirketi',
            'phone' => '+90 212 555 1234',
        ]);
    }

    public function test_firma_status_iliskisi()
    {
        $status = CompanyStatus::where('slug', 'prospekt')->first();
        $company = Company::factory()->create(['status_id' => $status->id]);

        $this->assertInstanceOf(CompanyStatus::class, $company->status);
        $this->assertEquals('prospekt', $company->status->slug);
    }

    public function test_firma_aktiviteleri_iliskisi()
    {
        $company = Company::factory()->create();
        
        Activity::create([
            'company_id' => $company->id,
            'type' => 'call',
            'title' => 'Telefon görüşmesi yapıldı',
        ]);

        $this->assertCount(1, $company->activities);
        $this->assertEquals('call', $company->activities->first()->type);
    }

    public function test_koordinatlar_decimal_formatinda()
    {
        $company = Company::factory()->create([
            'latitude' => 41.0082,
            'longitude' => 28.9784,
        ]);

        $this->assertIsNumeric($company->latitude);
        $this->assertIsNumeric($company->longitude);
    }

    public function test_types_array_olarak_cast_edilir()
    {
        $company = Company::factory()->create([
            'types' => ['restaurant', 'food', 'establishment'],
        ]);

        $freshCompany = Company::find($company->id);
        $this->assertIsArray($freshCompany->types);
        $this->assertContains('restaurant', $freshCompany->types);
    }

    public function test_initials_accessor_calisiyor()
    {
        $company = Company::factory()->create(['name' => 'Acme Yazılım']);

        $this->assertEquals('AY', $company->initials);
    }

    public function test_google_maps_url_koordinatli()
    {
        $company = Company::factory()->create([
            'latitude' => 41.0082,
            'longitude' => 28.9784,
        ]);

        $this->assertStringContainsString('41.0082', $company->google_maps_url);
        $this->assertStringContainsString('28.9784', $company->google_maps_url);
    }

    public function test_google_maps_url_koordinatsiz()
    {
        $company = Company::factory()->withoutCoordinates()->create([
            'address' => 'İstanbul, Türkiye',
        ]);

        $this->assertStringContainsString('search', $company->google_maps_url);
        $this->assertStringContainsString('stanbul', $company->google_maps_url);
    }

    public function test_formatted_phone_ozel_karakterleri_temizler()
    {
        $company = Company::factory()->create([
            'phone' => '+90 (212) 555-1234',
        ]);

        $this->assertEquals('+902125551234', $company->formatted_phone);
    }

    public function test_scope_by_status_calisiyor()
    {
        $prospektStatus = CompanyStatus::where('slug', 'prospekt')->first();
        $musteriStatus = CompanyStatus::where('slug', 'musteri')->first();

        Company::factory()->count(3)->create(['status_id' => $prospektStatus->id]);
        Company::factory()->count(2)->create(['status_id' => $musteriStatus->id]);

        $prospektCompanies = Company::byStatus('prospekt')->get();
        $musteriCompanies = Company::byStatus('musteri')->get();

        $this->assertCount(3, $prospektCompanies);
        $this->assertCount(2, $musteriCompanies);
    }

    public function test_scope_with_coordinates_calisiyor()
    {
        Company::factory()->count(2)->create();
        Company::factory()->withoutCoordinates()->create();

        $companiesWithCoordinates = Company::withCoordinates()->get();

        $this->assertCount(2, $companiesWithCoordinates);
    }

    public function test_scope_search_calisiyor()
    {
        Company::factory()->create(['name' => 'Acme Yazılım']);
        Company::factory()->create(['name' => 'Beta Teknoloji']);
        Company::factory()->create(['category' => 'Yazılım Geliştirme']);

        $results = Company::search('Yazılım')->get();

        $this->assertCount(2, $results);
    }

    public function test_has_enriched_data_kontrolu()
    {
        $companyWithEnriched = Company::factory()->create();
        EnrichedData::create([
            'company_id' => $companyWithEnriched->id,
            'email' => 'info@test.com',
        ]);

        $companyWithoutEnriched = Company::factory()->create();

        $this->assertTrue($companyWithEnriched->hasEnrichedData());
        $this->assertFalse($companyWithoutEnriched->hasEnrichedData());
    }

    public function test_last_activity_accessor()
    {
        $company = Company::factory()->create();
        
        Activity::create([
            'company_id' => $company->id,
            'type' => 'note',
            'title' => 'İlk not',
            'created_at' => now()->subDay(),
        ]);

        Activity::create([
            'company_id' => $company->id,
            'type' => 'call',
            'title' => 'Son arama',
            'created_at' => now(),
        ]);

        $this->assertEquals('Son arama', $company->last_activity->title);
    }

    public function test_yuksek_puanli_firmalar_factory_state()
    {
        $company = Company::factory()->highRated()->create();

        $this->assertGreaterThanOrEqual(4.5, $company->rating);
        $this->assertGreaterThanOrEqual(100, $company->review_count);
    }
}
