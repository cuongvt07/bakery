<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ExcelExportService
{
    /**
     * Export data to Excel với định dạng chuyên nghiệp
     *
     * @param  \Illuminate\Support\Collection  $data
     * @param  array  $config
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public static function export($data, array $config = [])
    {
        $config = array_merge([
            'title' => 'Báo cáo',
            'filename' => 'report',
            'columns' => [],
            'user' => auth()->user()?->ho_ten ?? 'Admin',
            'filters' => null,
        ], $config);

        $filename = self::generateFilename($config['filename']);

        return Excel::download(
            new class($data, $config) implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithEvents
            {
                protected $data;
                protected $config;

                public function __construct($data, $config)
                {
                    $this->data = $data;
                    $this->config = $config;
                }

                public function collection()
                {
                    return $this->data;
                }

                public function headings(): array
                {
                    return $this->config['columns'];
                }

                public function title(): string
                {
                    return substr($this->config['title'], 0, 31); // Excel sheet title max 31 chars
                }

                public function styles(Worksheet $sheet)
                {
                    // Style cho header row
                    $lastColumn = chr(64 + count($this->config['columns']));
                    
                    return [
                        // Header info rows (1-4)
                        1 => [
                            'font' => ['bold' => true, 'size' => 14],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ],
                        
                        // Column headers (row 6)
                        6 => [
                            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => '4472C4']
                            ],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ],
                        
                        // Apply borders to all data
                        "A6:{$lastColumn}" . ($sheet->getHighestRow()) => [
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['rgb' => '000000'],
                                ],
                            ],
                        ],
                    ];
                }

                public function registerEvents(): array
                {
                    return [
                        AfterSheet::class => function(AfterSheet $event) {
                            $sheet = $event->sheet->getDelegate();
                            $lastColumn = chr(64 + count($this->config['columns']));
                            
                            // Insert header info rows
                            $sheet->insertNewRowBefore(1, 5);
                            
                            // Merge cells for title
                            $sheet->mergeCells("A1:{$lastColumn}1");
                            $sheet->setCellValue('A1', strtoupper($this->config['title']));
                            
                            // Ngày xuất
                            $sheet->mergeCells("A2:{$lastColumn}2");
                            $sheet->setCellValue('A2', 'Ngày xuất: ' . now()->format('d/m/Y H:i:s'));
                            
                            // Người xuất
                            $sheet->mergeCells("A3:{$lastColumn}3");
                            $sheet->setCellValue('A3', 'Người xuất: ' . $this->config['user']);
                            
                            // Bộ lọc (nếu có)
                            if ($this->config['filters']) {
                                $sheet->mergeCells("A4:{$lastColumn}4");
                                $sheet->setCellValue('A4', 'Bộ lọc: ' . $this->config['filters']);
                            }
                            
                            // Empty row
                            $sheet->mergeCells("A5:{$lastColumn}5");
                            
                            // Style info rows
                            $sheet->getStyle("A1:{$lastColumn}4")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                            $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
                            $sheet->getStyle("A2:{$lastColumn}4")->getFont()->setSize(10);
                            
                            // Freeze header row
                            $sheet->freezePane('A7');
                        },
                    ];
                }
            },
            $filename
        );
    }

    /**
     * Generate filename theo format chuẩn
     */
    protected static function generateFilename(string $base): string
    {
        $date = now()->format('dmY');
        $time = now()->format('His');
        
        return "{$base}_{$date}_{$time}.xlsx";
    }
}
