\# Changelog



Projedeki tüm önemli değişiklikler bu dosyada dokümante edilir.



Format \[Keep a Changelog](https://keepachangelog.com/en/1.0.0/) standardına uygundur ve \[Semantic Versioning](https://semver.org/spec/v2.0.0.html) kullanır.



## [Unreleased]

### Fixed
- **Company model:** status ilişkisi foreign key hatası düzeltildi (`company_status_id` → `status_id`)
- **Activity model:** HasFactory trait eklendi, factory() metodu artık çalışıyor
- **EnrichedData model:** HasFactory trait eklendi, factory() metodu artık çalışıyor

### Added
- (Henüz yok)

### Changed
- (Henüz yok)

### Fixed
- (Henüz yok)

---

## [0.1.0] - 2026-02-24

### Added
- Yer imi (bookmark) özelliği eklendi
- Kullanıcılar Google Maps yerlerini kaydedebilecek
- `bookmarks` tablosu ve migration
- `Bookmark` modeli (User ilişkisi ile)
- `BookmarkFactory` oluşturuldu
- Company ve CompanyStatus factory'leri eklendi
- 10 unit test (%100 başarı, 28 assertion)

### Changed
- CompanyStatus modeli güncellendi

### Infrastructure
- Foreign key cascade delete ayarlandı
- Performans için user_id ve created_at indexleri eklendi

### Tests
- BookmarkTest unit testleri (10 test, 0.84s)
- Company unit testleri eklendi


\### Added

\- Yer imi (bookmark) özelliği eklendi

\- Kullanıcılar Google Maps yerlerini kaydedebilecek

\- `bookmarks` tablosu ve migration

\- `Bookmark` modeli (User ilişkisi ile)

\- `BookmarkFactory` oluşturuldu

\- Company ve CompanyStatus factory'leri eklendi

\- 10 unit test (%100 başarı, 28 assertion)



\### Changed

\- CompanyStatus modeli güncellendi



\### Infrastructure

\- Foreign key cascade delete ayarlandı

\- Performans için user\_id ve created\_at indexleri eklendi



\### Tests

\- BookmarkTest unit testleri (10 test, 0.84s)

\- Company unit testleri eklendi



---



\## Versiyon Kuralları



\### MAJOR.MINOR.PATCH



\- \*\*MAJOR\*\*: Breaking change (API değişikliği, schema değişikliği)

\- \*\*MINOR\*\*: Yeni feature (geriye uyumlu)

\- \*\*PATCH\*\*: Bug fix, security fix



\### Kategoriler



\- \*\*Added\*\*: Yeni özellikler

\- \*\*Changed\*\*: Mevcut özelliklerde değişiklik

\- \*\*Deprecated\*\*: Yakında kaldırılacak özellikler

\- \*\*Removed\*\*: Kaldırılan özellikler

\- \*\*Fixed\*\*: Bug düzeltmeleri

\- \*\*Security\*\*: Güvenlik düzeltmeleri

