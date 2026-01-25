<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompaniesExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    protected $companies;

    public function __construct(array $companies)
    {
        $this->companies = $companies;
    }

    /**
     * Excel için veri dizisi
     */
    public function array(): array
    {
        $data = [];
        
        foreach ($this->companies as $company) {
            $data[] = [
                $company['name'],
                $company['address'],
                $company['phone'],
                $company['website'],
                $company['rating'],
                $company['total_ratings'],
                $company['latitude'],
                $company['longitude'],
                $company['types'],
                $company['opening_hours'],
            ];
        }
        
        return $data;
    }

    /**
     * Excel başlıkları
     */
    public function headings(): array
    {
        return [
            'Firma Adı',
            'Adres',
            'Telefon',
            'Web Sitesi',
            'Puan',
            'Toplam Değerlendirme',
            'Enlem',
            'Boylam',
            'Kategoriler',
            'Çalışma Saatleri',
        ];
    }

    /**
     * Excel stil ayarları
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '667eea'],
                ],
                'font' => [
                    'color' => ['rgb' => 'FFFFFF'],
                    'bold' => true,
                ],
            ],
        ];
    }

    /**
     * Sütun genişlikleri
     */
    public function columnWidths(): array
    {
        return [
            'A' => 30, // Firma Adı
            'B' => 50, // Adres
            'C' => 20, // Telefon
            'D' => 40, // Web Sitesi
            'E' => 10, // Puan
            'F' => 15, // Toplam Değerlendirme
            'G' => 15, // Enlem
            'H' => 15, // Boylam
            'I' => 40, // Kategoriler
            'J' => 60, // Çalışma Saatleri
        ];
    }
}
