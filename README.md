# 🗺️ Google Maps Firma Arama

Google Maps Places API kullanarak firma/işletme arama ve Excel/CSV formatında dışa aktarma yapabilen Laravel uygulaması.

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

## ✨ Özellikler

- 🔍 **Firma Arama** - Google Maps Places API ile kapsamlı firma arama
- 📍 **Konum Bazlı Filtreleme** - Belirli şehir veya bölgeye göre arama
- 📞 **Detaylı Bilgiler** - Telefon, adres, website, çalışma saatleri
- ⭐ **Puanlama Bilgisi** - Google rating ve yorum sayısı
- 📊 **Excel Export** - Sonuçları .xlsx formatında indirme
- 📄 **CSV Export** - Sonuçları .csv formatında indirme
- 🎨 **Modern UI** - Tailwind CSS ile responsive tasarım

## 📋 Gereksinimler

- PHP >= 8.2
- Composer
- Google Maps API Key (Places API etkin olmalı)

## 🚀 Kurulum

### 1. Projeyi Klonlayın

```bash
git clone https://github.com/syberess/google_maps.git
cd google_maps
```

### 2. Bağımlılıkları Yükleyin

```bash
composer install
```

### 3. Ortam Dosyasını Oluşturun

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Google Maps API Key Ekleyin

`.env` dosyasını açın ve aşağıdaki satırı ekleyin:

```env
GOOGLE_MAPS_API_KEY=your_api_key_here
```

> ⚠️ **Önemli:** Google Cloud Console'dan API Key alırken **Places API**'yi etkinleştirmeyi unutmayın!

### 5. Uygulamayı Başlatın

```bash
php artisan serve
```

Tarayıcınızda `http://localhost:8000` adresine gidin.

## 🔑 Google Maps API Key Alma

1. [Google Cloud Console](https://console.cloud.google.com/) adresine gidin
2. Yeni bir proje oluşturun veya mevcut projeyi seçin
3. **APIs & Services > Library** bölümüne gidin
4. **Places API** ve **Maps JavaScript API**'yi etkinleştirin
5. **APIs & Services > Credentials** bölümünden API Key oluşturun
6. API Key'i `.env` dosyasına ekleyin

## 📖 Kullanım

1. Ana sayfada arama kutusuna firma türü veya ismi girin (örn: "restoran", "otel", "Apple Store")
2. İsteğe bağlı olarak konum belirtin (örn: "İstanbul", "Kadıköy")
3. Maksimum sonuç sayısını seçin (1-60 arası)
4. **Ara** butonuna tıklayın
5. Sonuçları görüntüleyin ve **Excel** veya **CSV** olarak indirin

## 📁 Proje Yapısı

```
├── app/
│   ├── Exports/
│   │   └── CompaniesExport.php      # Excel export sınıfı
│   ├── Http/Controllers/
│   │   └── CompanySearchController.php  # Ana controller
│   └── Services/
│       └── GoogleMapsService.php    # Google Maps API servisi
├── resources/views/
│   ├── layouts/
│   │   ├── app.blade.php            # Ana layout
│   │   ├── search.blade.php         # Arama sayfası
│   │   └── results.blade.php        # Sonuçlar sayfası
│   └── welcome.blade.php
├── routes/
│   └── web.php                      # Route tanımları
└── .env.example                     # Örnek ortam dosyası
```

## 🛣️ API Endpoints

| Method | URI | Açıklama |
|--------|-----|----------|
| GET | `/` | Ana sayfa (arama formu) |
| POST | `/search` | Arama yap |
| GET | `/export/excel` | Excel olarak indir |
| GET | `/export/csv` | CSV olarak indir |

## 🤝 Katkıda Bulunma

1. Bu repoyu fork edin
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add some amazing feature'`)
4. Branch'inizi push edin (`git push origin feature/amazing-feature`)
5. Pull Request açın

## 📝 Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için [LICENSE](LICENSE) dosyasına bakın.

## 👤 Geliştirici

**syberess**

- GitHub: [@syberess](https://github.com/syberess)

---

⭐ Bu projeyi beğendiyseniz yıldız vermeyi unutmayın!
