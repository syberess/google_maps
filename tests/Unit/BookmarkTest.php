<?php

namespace Tests\Unit;

use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_yer_imi_olusturulabilir()
    {
        $user = User::factory()->create();

        $bookmark = Bookmark::create([
            'user_id' => $user->id,
            'place_name' => 'Galata Kulesi',
            'latitude' => 41.0256,
            'longitude' => 28.9744,
            'notes' => 'Muhteşem İstanbul manzarası!'
        ]);

        $this->assertDatabaseHas('bookmarks', [
            'place_name' => 'Galata Kulesi',
            'user_id' => $user->id,
        ]);
    }

    public function test_yer_imi_kullaniciya_ait()
    {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $bookmark->user);
        $this->assertEquals($user->id, $bookmark->user->id);
    }

    public function test_koordinatlar_decimal_formatinda()
    {
        $bookmark = Bookmark::factory()->create([
            'latitude' => 39.9334,
            'longitude' => 32.8597,
        ]);

        $this->assertIsNumeric($bookmark->latitude);
        $this->assertIsNumeric($bookmark->longitude);
    }

    public function test_fillable_alanlar_dogru()
    {
        $bookmark = new Bookmark();
        
        $fillable = $bookmark->getFillable();
        
        $this->assertContains('user_id', $fillable);
        $this->assertContains('place_name', $fillable);
        $this->assertContains('latitude', $fillable);
        $this->assertContains('longitude', $fillable);
        $this->assertContains('notes', $fillable);
    }

    public function test_factory_otomatik_user_olusturur()
    {
        $bookmark = Bookmark::factory()->create();

        $this->assertNotNull($bookmark->user_id);
        $this->assertInstanceOf(User::class, $bookmark->user);
    }

    public function test_factory_varsayilan_degerler_olusturur()
    {
        $bookmark = Bookmark::factory()->create();

        $this->assertNotEmpty($bookmark->place_name);
        $this->assertNotNull($bookmark->latitude);
        $this->assertNotNull($bookmark->longitude);
    }

    public function test_notes_alani_opsiyonel()
    {
        $bookmark = Bookmark::factory()->create(['notes' => null]);

        $this->assertNull($bookmark->notes);
        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'notes' => null,
        ]);
    }

    public function test_kullanici_birden_fazla_yer_imi_olusturabilir()
    {
        $user = User::factory()->create();

        Bookmark::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, Bookmark::where('user_id', $user->id)->get());
    }

    public function test_koordinat_degerleri_gecerli_aralikta()
    {
        $bookmark = Bookmark::factory()->create();

        // Latitude: -90 ile 90 arası
        $this->assertGreaterThanOrEqual(-90, $bookmark->latitude);
        $this->assertLessThanOrEqual(90, $bookmark->latitude);

        // Longitude: -180 ile 180 arası
        $this->assertGreaterThanOrEqual(-180, $bookmark->longitude);
        $this->assertLessThanOrEqual(180, $bookmark->longitude);
    }

    public function test_kullanici_silinince_yer_imleri_silinir()
    {
        $user = User::factory()->create();
        Bookmark::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertCount(2, Bookmark::where('user_id', $user->id)->get());

        $user->delete();

        $this->assertCount(0, Bookmark::where('user_id', $user->id)->get());
    }
}