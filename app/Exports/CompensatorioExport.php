<?php

// Namespace donde se encuentra la clase de exportación
namespace App\Exports;

// Importaciones necesarias

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompensatorioExport implements FromView, WithDrawings, WithStyles, ShouldAutoSize
{
    protected $data;

    public function __construct($data) {
        $this->data = $data;
    }

   public function drawings()
{
    $drawings = [];

    // 1. Logo IHCI
    if (file_exists(public_path('images/IHCI.png'))) {
        $logo = new Drawing();
        $logo->setName('Logo IHCI');
        $logo->setPath(public_path('images/IHCI.png'));
        $logo->setHeight(60);
        $logo->setCoordinates('A1');
        $drawings[] = $logo;
    }

    // 2. Firma desde LONGBLOB
    if (!empty($this->data['firmaBlob'])) {
        $tempPath = tempnam(sys_get_temp_dir(), 'firma_');
        
        // El LONGBLOB ya es binario, lo guardamos directamente
        file_put_contents($tempPath, $this->data['firmaBlob']);

        $firma = new Drawing();
        $firma->setName('Firma Autorizada');
        $firma->setPath($tempPath);
        $firma->setHeight(80);
        
        // La colocamos debajo de la tabla de registros
        $filaBase = count($this->data['todosLosRegistros']) + 18;
        $firma->setCoordinates('B' . $filaBase);
        $firma->setOffsetX(50);
        
        $drawings[] = $firma;
    }

    return $drawings;
}

public function styles(Worksheet $sheet)
{
    // Limpieza total del fondo azul en las celdas de datos
    $sheet->getStyle('A11:E500')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE);

    return [
        10 => [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '003366']
            ]
        ],
    ];
}
    public function view(): View
    {
        return view('informes.compensatorio_excel', $this->data);
    }
}