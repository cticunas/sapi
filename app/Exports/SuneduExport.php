<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SuneduExport implements FromView,WithColumnFormatting,ShouldAutoSize,WithStyles
{
    use Exportable;
    function __construct($data)
    {
        $this->data=$data['data'];
        //dd($list);
    }

    public function view(): View
    {
        return view('reports/sunedu_excel',['data'=>$this->data,'i'=>1]);
    }
    public function columnFormats(): array
    {
    return [
        'N'=>NumberFormat::FORMAT_NUMBER_00
    ];
    }
    public function styles(Worksheet $sheet)
    {
        /*return $sheet->getStyle('A1')->applyFromArray(array
            ('fill'=>array('color'=>['rgb'=>'FF0000'])));*/
        return [
            'A1'=>[
                'borders'=>[
                    'top'=>[
                            'borderStyle'=>\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => '00000000'],
                            ]
                           ]
                 ]
                    ];
    }
}