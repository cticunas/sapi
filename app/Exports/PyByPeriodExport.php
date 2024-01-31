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

class PyByPeriodExport implements FromView,WithColumnFormatting,ShouldAutoSize,WithStyles
{
    use Exportable;
    function __construct($data)
    {
        $this->data=$data['data'];
        $this->school = $data['school'];
        $this->type_research = $data['type_research'];
        $this->state = $data['state'];
        $this->year = $data['year'];
        //dd($list);
    }

    public function view(): View
    {
        return view('reports/py_by_period_excel',['data'=>$this->data,'i'=>1, 'school'=>$this->school, 'type_research'=>$this->type_research, 'state'=>$this->state, 'year'=>$this->year]);
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
