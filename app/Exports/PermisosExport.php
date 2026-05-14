<?php

/*
|--------------------------------------------------------------------------
| Namespace del Exportador
|--------------------------------------------------------------------------
| Esta clase pertenece al módulo de exportaciones de Laravel.
| Se utiliza para generar archivos Excel relacionados con
| permisos y vacaciones de empleados.
|--------------------------------------------------------------------------
*/
namespace App\Exports;

/*
|--------------------------------------------------------------------------
| Importación de Clases Necesarias
|--------------------------------------------------------------------------
*/

use Illuminate\Contracts\View\View; // Permite retornar una vista Blade para generar el Excel

use Maatwebsite\Excel\Concerns\FromView; 
// Permite construir el Excel desde una vista Blade

use Maatwebsite\Excel\Concerns\WithDrawings; 
// Permite insertar imágenes en el archivo Excel

use Maatwebsite\Excel\Concerns\WithStyles; 
// Permite aplicar estilos personalizados al Excel

use Maatwebsite\Excel\Concerns\ShouldAutoSize; 
// Ajusta automáticamente el ancho de las columnas

use PhpOffice\PhpSpreadsheet\Worksheet\Drawing; 
// Clase utilizada para manejar imágenes en Excel

use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; 
// Representa la hoja de cálculo donde se aplican estilos

/*
|--------------------------------------------------------------------------
| Clase PermisosExport
|--------------------------------------------------------------------------
| Esta clase genera un archivo Excel con:
| - Datos de permisos y vacaciones
| - Logo institucional
| - Firma digital desde base de datos
| - Estilos personalizados
|--------------------------------------------------------------------------
*/
class PermisosExport implements FromView, WithDrawings, WithStyles, ShouldAutoSize
{
    /*
    |--------------------------------------------------------------------------
    | Propiedad protegida
    |--------------------------------------------------------------------------
    | Aquí se almacenan todos los datos enviados desde el controlador.
    |--------------------------------------------------------------------------
    */
    protected $data;

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    | Recibe la información necesaria para construir el reporte.
    |--------------------------------------------------------------------------
    */
    public function __construct($data) {

        // Guarda los datos recibidos
        $this->data = $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Método drawings() - Inserta Logo y Firma
    |--------------------------------------------------------------------------
    */
   public function drawings()
   {
    $drawings = [];

    // 1. LOGO
    $logo = new Drawing();
    $logo->setName('Logo IHCI');
    $logo->setPath(public_path('images/IHCI.png')); 
    $logo->setHeight(75);
    $logo->setCoordinates('A1');
    $logo->setOffsetX(10);
    $logo->setOffsetY(10);
    $drawings[] = $logo;

   
    // 2. CONFIGURACIÓN DE LA FIRMA
    if (!empty($this->data['firmaBlob'])) {
        $firma = new \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing();
        $image = imagecreatefromstring($this->data['firmaBlob']);
        $firma->setImageResource($image);
        $firma->setRenderingFunction(\PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::RENDERING_PNG);
        $firma->setMimeType(\PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::MIMETYPE_DEFAULT);
        $firma->setHeight(80); 

        // AJUSTE: Para que la firma quede ARRIBA, la colocamos unas filas antes
        // del texto de "Gestión de Talento Humano".
        $filaFirma = count($this->data['solicitudes']) + 14; 
        
        $firma->setCoordinates('B' . $filaFirma);
        $firma->setOffsetY(-10); 
        $firma->setOffsetX(60);  
        
        $drawings[] = $firma;
    }

    return $drawings;
   }

    /*
    |--------------------------------------------------------------------------
    | Método styles() - Colores y bordes
    |--------------------------------------------------------------------------
    */
    public function styles(Worksheet $sheet)
    {
    // 1. Azul SOLO para el encabezado
    $sheet->getStyle('A10:F10')->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '003366'], 
        ],
    ]);

    // 2. QUITAR FONDO AZUL DEBAJO: Limpiamos el formato de las filas siguientes
    // Esto elimina la franja marcada con "X" en tu imagen.
    $sheet->getStyle('A11:F100')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE);

    return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Método view() - Carga la vista Blade
    |--------------------------------------------------------------------------
    */
    public function view(): View
    {
        return view('informes.permisos_excel', $this->data);
    }
}