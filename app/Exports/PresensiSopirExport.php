<?php

namespace App\Exports;

use App\Models\HistoryBekerjaSopir;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class PresensiSopirExport implements 
    FromQuery, 
    WithHeadings, 
    WithMapping, 
    WithStyles,
    WithColumnWidths,
    WithTitle
{
    protected $startDate;
    protected $endDate;
    
    /**
     * Constructor untuk terima parameter filter
     * 
     * @param string|null $startDate - Tanggal mulai (Y-m-d)
     * @param string|null $endDate - Tanggal akhir (Y-m-d)
     */
    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
    
    /**
     * Query data dengan filter
     * Menggunakan FromQuery agar lebih efisien (tidak load semua data ke memory)
     */
    public function query()
    {
        $query = HistoryBekerjaSopir::with('sopir');
        
        // Filter berdasarkan tanggal (jika ada)
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tanggal', [
                date("Y-m-d", strtotime($this->startDate)), 
                date("Y-m-d", strtotime($this->endDate)),
            ]);
        } elseif ($this->startDate) {
            // Jika hanya start date (dari tanggal ini sampai sekarang)
            $query->where('tanggal', '>=', date("Y-m-d", strtotime($this->startDate)));
        } elseif ($this->endDate) {
            // Jika hanya end date (sampai tanggal ini)
            $query->where('tanggal', '<=', date("Y-m-d", strtotime($this->endDate)));
        }

        $query = $query->orderBy('tanggal', 'desc');
        
        return $query;
    }
    
    /**
     * Heading kolom Excel
     */
    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Nama Sopir',
            'Jumlah Order',
        ];
    }
    
    /**
     * Mapping data untuk setiap row
     * Variabel $row berisi 1 record dari query()
     */
    public function map($row): array
    {
        static $rowNumber = 0;
        $rowNumber++;
        
        return [
            $rowNumber,
            Carbon::parse($row->tanggal)->format('Y-m-d'),
            $row->sopir->name,
            $row->order_completed ?? '0', 
        ];
    }
    
    /**
     * Styling Excel
     */
    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        
        // Style header
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4CAF50'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        
        // Center align untuk kolom tertentu
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B2:B' . $lastRow)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D2:D' . $lastRow)->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Border semua cell
        $sheet->getStyle('A1:D' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        
        // Zebra striping
        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle("A{$i}:D{$i}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F5F5F5'],
                    ],
                ]);
            }
        }
        
        return [];
    }
    
    /**
     * Set lebar kolom
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,  // No
            'B' => 20, // Tanggal   
            'C' => 18, // Nama Sopir
            'D' => 18, // Jumlah Order
        ];
    }
    
    /**
     * Title sheet Excel
     */
    public function title(): string
    {
        return 'Presensi Sopir';
    }
}
