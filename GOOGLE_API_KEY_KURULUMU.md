# Google Maps API Key Kurulumu

## Adım 1: Google Cloud Console'a Giriş

1. [Google Cloud Console](https://console.cloud.google.com) adresine gidin
2. Google hesabınızla giriş yapın
3. Eğer ilk kez kullanıyorsanız, kullanım şartlarını kabul edin

---

## Adım 2: Yeni Proje Oluşturma

1. Üst menüdeki proje seçiciye tıklayın (Google Cloud Platform yazısının yanında)
2. **"NEW PROJECT"** veya **"Yeni Proje"** butonuna tıklayın
3. Proje adı girin (örn: "Google Maps Laravel")
4. **"CREATE"** veya **"OLUŞTUR"** butonuna tıklayın
5. Proje oluşturulana kadar 10-20 saniye bekleyin
6. Bildirimlerde projenin oluşturulduğunu gördüğünüzde, üst menüden yeni projenizi seçin

---

## Adım 3: Fatura Hesabı Aktifleştirme (Zorunlu)

> **ÖNEMLİ:** Google Maps API kullanmak için fatura hesabı eklemek zorunludur. Ancak aylık $200 ücretsiz kullanım kredisi verilir.

1. Sol menüden **"Billing"** veya **"Faturalama"** seçeneğine tıklayın
2. **"LINK A BILLING ACCOUNT"** veya **"Fatura Hesabı Bağla"** butonuna tıklayın
3. Yeni fatura hesabı oluşturmak için formu doldurun:
   - Ülke seçin (Türkiye)
   - Kredi kartı bilgilerinizi girin
   - Adres bilgilerinizi girin
4. Kullanım şartlarını kabul edip **"START MY FREE TRIAL"** veya **"ÜCRETSİZ DENEMEMİ BAŞLAT"** tıklayın

**Not:** İlk 90 gün $300 ücretsiz kredi + her ay $200 sürekli ücretsiz kredi alırsınız.

---

## Adım 4: Gerekli API'leri Aktifleştirme

1. Sol menüden **"APIs & Services"** > **"Library"** seçeneğine gidin
2. Aşağıdaki API'leri tek tek arayıp aktifleştirin:

### 4.1 Geocoding API
- Arama kutusuna **"Geocoding API"** yazın
- API'ye tıklayın
- **"ENABLE"** veya **"ETKİNLEŞTİR"** butonuna tıklayın

### 4.2 Places API
- Geri dönün ve **"Places API"** arayın
- API'ye tıklayın
- **"ENABLE"** veya **"ETKİNLEŞTİR"** butonuna tıklayın

### 4.3 Maps JavaScript API (Opsiyonel - harita gösterimi için)
- **"Maps JavaScript API"** arayın
- API'ye tıklayın
- **"ENABLE"** veya **"ETKİNLEŞTİR"** butonuna tıklayın

---

## Adım 5: API Key Oluşturma

1. Sol menüden **"APIs & Services"** > **"Credentials"** seçeneğine gidin
2. Üst menüde **"+ CREATE CREDENTIALS"** veya **"+ KİMLİK BİLGİSİ OLUŞTUR"** butonuna tıklayın
3. Açılan menüden **"API key"** seçeneğini seçin
4. API key oluşturulacak ve bir popup'ta gösterilecek
5. **API key'i kopyalayın ve güvenli bir yere kaydedin**

---

## Adım 6: API Key'i Kısıtlama (Önerilen - Güvenlik İçin)

API key'in kötüye kullanılmasını önlemek için kısıtlamalar ekleyin:

### 6.1 API Kısıtlamaları
1. Oluşturulan API key'in yanındaki **düzenle (kalem)** ikonuna tıklayın
2. **"API restrictions"** bölümünde **"Restrict key"** seçeneğini seçin
3. Sadece kullanacağınız API'leri seçin:
   - ✓ Geocoding API
   - ✓ Places API
   - ✓ Maps JavaScript API (gerekirse)
4. **"SAVE"** butonuna tıklayın

### 6.2 Uygulama Kısıtlamaları (Opsiyonel)
**HTTP referrers** (Web siteleri için):
- Application restrictions bölümünde **"HTTP referrers"** seçin
- Website restrictions'a domain ekleyin:
  - `localhost/*`
  - `127.0.0.1/*`
  - `yourdomain.com/*`

**veya**

**IP addresses** (Sunucu tarafı için):
- Application restrictions bölümünde **"IP addresses"** seçin
- Sunucu IP adresinizi ekleyin

---

## Adım 7: API Key'i Laravel Projesine Ekleme

1. `.env` dosyasını açın
2. API key'i ekleyin:

```env
# Google Maps API
GOOGLE_MAPS_API_KEY=AIzaSyA1M-3WdCdnk-xahDUdTlvxWYoStk8y_9k
```

3. `config/services.php` dosyasında tanımlandığından emin olun:

```php
'google_maps' => [
    'api_key' => env('GOOGLE_MAPS_API_KEY'),
],
```

4. Laravel cache'i temizleyin:

```bash
php artisan config:clear
php artisan cache:clear
```

---

## Adım 8: API Key'i Test Etme

Terminal'de test script'i çalıştırın:

```bash
php test-api-key.php
```

**veya**

Tarayıcıda test sayfasını açın:
```
http://127.0.0.1:8000/test-api
```

---

## Başarılı Sonuç Örneği

```
=================================
Google Maps API Key Test
=================================

Test 1: Geocoding API (İstanbul, Türkiye)
-------------------------------------------
✓ Başarılı!
Adres: Istanbul, İstanbul, Türkiye

Test 2: Places API - Nearby Search
-------------------------------------------
✓ Başarılı!
Bulunan yer sayısı: 20
```

---

## Sık Karşılaşılan Hatalar ve Çözümleri

### ❌ "The provided API key is expired"
**Çözüm:** API key yeni oluşturulduysa, Laravel cache'i temizleyin:
```bash
php artisan config:clear
```

### ❌ "This API project is not authorized to use this API"
**Çözüm:** İlgili API'yi (Geocoding, Places) Google Cloud Console'dan aktifleştirin

### ❌ "REQUEST_DENIED"
**Çözüm:** 
- Fatura hesabı eklendiğinden emin olun
- API kısıtlamalarını kontrol edin
- API'lerin etkinleştirildiğini doğrulayın

### ❌ "OVER_QUERY_LIMIT"
**Çözüm:** Günlük kullanım limitinizi aştınız. Fatura hesabınızı kontrol edin veya yarına kadar bekleyin

---

## Kullanım Limitleri ve Fiyatlandırma

### Ücretsiz Kullanım (Aylık)
- **$200 kredi** = yaklaşık:
  - 28,000 Geocoding isteği
  - veya 40,000 Places API isteği

### Fiyatlandırma (Kredi bittikten sonra)
- **Geocoding API:** $5 / 1000 istek
- **Places API:** $17 / 1000 istek (Nearby Search)
- **Maps JavaScript API:** $7 / 1000 yükleme

**Not:** Çoğu küçük/orta proje için aylık $200 kredi yeterlidir.

---

## Güvenlik İpuçları

1. ✓ **API key'i asla GitHub'a yüklemeyin** (`.env` dosyası `.gitignore`'da olmalı)
2. ✓ API kısıtlamaları mutlaka ekleyin
3. ✓ Kullanım kotalarını düzenli kontrol edin
4. ✓ Gereksiz API çağrılarını önlemek için caching kullanın
5. ✓ Production'da farklı bir API key kullanın

---

## Faydalı Linkler

- [Google Cloud Console](https://console.cloud.google.com)
- [API Dashboard](https://console.cloud.google.com/apis/dashboard)
- [Credentials](https://console.cloud.google.com/apis/credentials)
- [Faturalama](https://console.cloud.google.com/billing)
- [Google Maps Platform Dokümantasyonu](https://developers.google.com/maps/documentation)

---

