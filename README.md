# Google Maps Firma Arama & CRM

Google Maps Places API kullanarak firma/işletme arama, yönetim ve Excel/CSV formatında dışa aktarma yapabilen Laravel CRM uygulaması.

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

## Özellikler

### Firma Arama
- Google Maps Places API ile kapsamlı firma arama
- Konum bazlı filtreleme (şehir/bölge)
- Detaylı bilgiler (telefon, adres, website, çalışma saatleri)
- Google rating ve yorum sayısı

### CRM Özellikleri
- **Firma Yönetimi** - Firmaları kaydetme, durum takibi, notlar
- **Aktivite Takibi** - Firma bazlı aktivite/görev yönetimi
- **Zenginleştirilmiş Veriler** - Ek firma bilgileri saklama
- **Dashboard** - Genel bakış ve istatistikler

### Harita & Navigasyon
- **Harita Görünümü** - Firmaların harita üzerinde görüntülenmesi
- **Rota Optimizasyonu** - Ziyaret rotası planlama
- **Navigasyon** - Firma lokasyonuna yönlendirme

### Raporlama & Export
- Excel/CSV formatında dışa aktarma
- Raporlama ekranı
-  **Modern UI** - Tailwind CSS ile responsive tasarım

##  Gereksinimler

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL veya SQLite veritabanı
- Google Maps API Key (Places API etkin olmalı)

##  Kurulum

### 1. Projeyi Klonlayın

```bash
git clone https://github.com/syberess/google_maps.git
cd google_maps
```

### 2. Bağımlılıkları Yükleyin

```bash
composer install
npm install
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

> **Önemli:** Google Cloud Console'dan API Key alırken **Places API**'yi etkinleştirmeyi unutmayın!

### 5. Veritabanını Yapılandırın

`.env` dosyasında veritabanı ayarlarını yapın:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=google_maps
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Veritabanı Tablolarını Oluşturun

```bash
php artisan migrate
php artisan db:seed
```

### 7. Frontend Asset'lerini Derleyin

```bash
npm run build
```

### 8. Uygulamayı Başlatın

```bash
php artisan serve
```

Veya geliştirme modunda (hot reload ile):

```bash
composer dev
```

Tarayıcınızda `http://localhost:8000` adresine gidin.

## Google Maps API Key Alma

1. [Google Cloud Console](https://console.cloud.google.com/) adresine gidin
2. Yeni bir proje oluşturun veya mevcut projeyi seçin
3. **APIs & Services > Library** bölümüne gidin
4. **Places API** ve **Maps JavaScript API**'yi etkinleştirin
5. **APIs & Services > Credentials** bölümünden API Key oluşturun
6. API Key'i `.env` dosyasına ekleyin

##  Kullanım

### Firma Arama
1. Menüden **Arama** sayfasına gidin
2. Arama kutusuna firma türü veya ismi girin (örn: "restoran", "otel", "Apple Store")
3. İsteğe bağlı olarak konum belirtin (örn: "İstanbul", "Kadıköy")
4. Maksimum sonuç sayısını seçin (1-60 arası)
5. **Ara** butonuna tıklayın
6. Sonuçları görüntüleyin ve **CRM'e Kaydet** veya **Export** yapın

### CRM Kullanımı
1. **Dashboard** - Genel bakış ve istatistikleri görüntüleyin
2. **Firmalar** - Kayıtlı firmaları listeleyin, durum güncelleyin, notlar ekleyin
3. **Aktiviteler** - Firma bazlı görevler oluşturun ve takip edin
4. **Harita** - Firmaları harita üzerinde görüntüleyin, rota planlayın
5. **Raporlar** - Detaylı raporları inceleyin

##  Proje Yapısı

```
├── app/
│   ├── Exports/
│   │   └── CompaniesExport.php          # Excel export sınıfı
│   ├── Http/Controllers/
│   │   ├── ActivityController.php       # Aktivite yönetimi
│   │   ├── CompanyController.php        # Firma CRM işlemleri
│   │   ├── CompanySearchController.php  # Google Maps arama
│   │   ├── DashboardController.php      # Dashboard
│   │   ├── MapController.php            # Harita & rota
│   │   └── ReportController.php         # Raporlar
│   ├── Models/
│   │   ├── Activity.php                 # Aktivite modeli
│   │   ├── Company.php                  # Firma modeli
│   │   ├── CompanyStatus.php            # Firma durumu modeli
│   │   ├── EnrichedData.php             # Zenginleştirilmiş veri
│   │   └── User.php                     # Kullanıcı modeli
│   └── Services/
│       ├── ExternalDataAdapter.php      # Harici veri adaptörü
│       ├── GoogleMapsService.php        # Google Maps API servisi
│       └── RouteOptimizationService.php # Rota optimizasyonu
├── database/
│   ├── migrations/
│   │   ├── create_company_statuses_table.php
│   │   ├── create_companies_table.php
│   │   ├── create_activities_table.php
│   │   └── create_enriched_data_table.php
│   └── seeders/
│       ├── CompanyStatusSeeder.php
│       └── DatabaseSeeder.php
├── resources/views/
│   ├── activities/
│   │   └── index.blade.php              # Aktivite listesi
│   ├── companies/
│   │   ├── index.blade.php              # Firma listesi
│   │   └── show.blade.php               # Firma detay
│   ├── layouts/
│   │   ├── app.blade.php                # Ana layout
│   │   ├── results.blade.php            # Sonuçlar layout
│   │   └── search.blade.php             # Arama layout
│   ├── maps/
│   │   └── index.blade.php              # Harita görünümü
│   ├── reports/
│   │   └── index.blade.php              # Raporlar
│   ├── dashboard.blade.php              # Dashboard
│   ├── search.blade.php                 # Arama sayfası
│   └── results.blade.php                # Arama sonuçları
├── routes/
│   └── web.php                          # Route tanımları
└── .env.example                         # Örnek ortam dosyası
```

## API Endpoints

### Dashboard
| Method | URI | Açıklama |
|--------|-----|----------|
| GET | `/` | Dashboard |
| GET | `/dashboard` | Dashboard |

### Firma Arama (Google Maps)
| Method | URI | Açıklama |
|--------|-----|----------|
| GET | `/search` | Arama formu |
| POST | `/search` | Google Maps'te arama yap |
| GET | `/search-export` | Arama sonuçlarını export et |

### Firmalar (CRM)
| Method | URI | Açıklama |
|--------|-----|----------|
| GET | `/companies` | Firma listesi |
| GET | `/companies/export` | Firmaları export et |
| POST | `/companies/bulk-store` | Toplu firma kaydet |
| GET | `/companies/{id}` | Firma detay |
| PATCH | `/companies/{id}/status` | Firma durumu güncelle |
| PUT | `/companies/{id}/enriched-data` | Zengin veri güncelle |
| PUT | `/companies/{id}/notes` | Notları güncelle |
| DELETE | `/companies/{id}` | Firma sil |

### Aktiviteler
| Method | URI | Açıklama |
|--------|-----|----------|
| GET | `/activities` | Aktivite listesi |
| POST | `/activities` | Aktivite ekle |
| PATCH | `/activities/{id}/complete` | Aktiviteyi tamamla |
| DELETE | `/activities/{id}` | Aktivite sil |

### Harita & Rota
| Method | URI | Açıklama |
|--------|-----|----------|
| GET | `/maps` | Harita görünümü |
| GET | `/maps/navigation/{id}` | Navigasyon |
| POST | `/api/route/optimize` | Rota optimizasyonu |

### Raporlar
| Method | URI | Açıklama |
|--------|-----|----------|
| GET | `/reports` | Raporlar sayfası |

##  Katkıda Bulunma

1. Bu repoyu fork edin
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add some amazing feature'`)
4. Branch'inizi push edin (`git push origin feature/amazing-feature`)
5. Pull Request açın

## Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için [LICENSE](LICENSE) dosyasına bakın.

##  Geliştirici

**syberess**

- GitHub: [@syberess](https://github.com/syberess)

---

Bu projeyi beğendiyseniz yıldız vermeyi unutmayın!
