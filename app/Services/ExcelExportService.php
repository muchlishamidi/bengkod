<?php

namespace App\Services;

class ExcelExportService
{
    /**
     * Export data sebagai file Excel (.xls) berstandar SpreadsheetML yang didukung penuh oleh Microsoft Excel.
     *
     * @param string $filename Nama file output (misal: data-dokter.xls)
     * @param array $headers Judul kolom (misal: ['No', 'Nama', 'Poli'])
     * @param array $rows Isi data (array of arrays)
     * @param string $sheetTitle Nama lembar kerja / sheet
     * @return \Illuminate\Http\Response
     */
    public static function export(string $filename, array $headers, array $rows, string $sheetTitle = 'Data')
    {
        if (!str_ends_with($filename, '.xls')) {
            $filename .= '.xls';
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        $xml .= ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
        $xml .= ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
        $xml .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        $xml .= ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";

        // Styles
        $xml .= ' <Styles>' . "\n";
        $xml .= '  <Style ss:ID="Default" ss:Name="Normal"><Alignment ss:Vertical="Center"/><Font ss:FontName="Calibri" ss:Size="11"/></Style>' . "\n";
        $xml .= '  <Style ss:ID="Header">' . "\n";
        $xml .= '   <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#FFFFFF"/>' . "\n";
        $xml .= '   <Interior ss:Color="#2D4499" ss:Pattern="Solid"/>' . "\n";
        $xml .= '   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>' . "\n";
        $xml .= '   <Borders>' . "\n";
        $xml .= '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D1D5DB"/>' . "\n";
        $xml .= '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D1D5DB"/>' . "\n";
        $xml .= '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D1D5DB"/>' . "\n";
        $xml .= '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D1D5DB"/>' . "\n";
        $xml .= '   </Borders>' . "\n";
        $xml .= '  </Style>' . "\n";
        $xml .= '  <Style ss:ID="DataCell">' . "\n";
        $xml .= '   <Alignment ss:Vertical="Center"/>' . "\n";
        $xml .= '   <Borders>' . "\n";
        $xml .= '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
        $xml .= '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
        $xml .= '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
        $xml .= '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
        $xml .= '   </Borders>' . "\n";
        $xml .= '  </Style>' . "\n";
        $xml .= '  <Style ss:ID="NumberCell">' . "\n";
        $xml .= '   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>' . "\n";
        $xml .= '   <Borders>' . "\n";
        $xml .= '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
        $xml .= '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
        $xml .= '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
        $xml .= '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>' . "\n";
        $xml .= '   </Borders>' . "\n";
        $xml .= '  </Style>' . "\n";
        $xml .= ' </Styles>' . "\n";

        // Worksheet
        $xml .= ' <Worksheet ss:Name="' . htmlspecialchars($sheetTitle, ENT_XML1, 'UTF-8') . '">' . "\n";
        $xml .= '  <Table ss:DefaultRowHeight="20">' . "\n";

        // Header Row
        $xml .= '   <Row ss:Height="24">' . "\n";
        foreach ($headers as $header) {
            $xml .= '    <Cell ss:StyleID="Header"><Data ss:Type="String">' . htmlspecialchars((string)$header, ENT_XML1, 'UTF-8') . '</Data></Cell>' . "\n";
        }
        $xml .= '   </Row>' . "\n";

        // Data Rows
        foreach ($rows as $row) {
            $xml .= '   <Row>' . "\n";
            foreach ($row as $val) {
                if (is_int($val) || is_float($val)) {
                    $xml .= '    <Cell ss:StyleID="NumberCell"><Data ss:Type="Number">' . $val . '</Data></Cell>' . "\n";
                } else {
                    $strVal = htmlspecialchars((string)($val ?? '-'), ENT_XML1, 'UTF-8');
                    $xml .= '    <Cell ss:StyleID="DataCell"><Data ss:Type="String">' . $strVal . '</Data></Cell>' . "\n";
                }
            }
            $xml .= '   </Row>' . "\n";
        }

        $xml .= '  </Table>' . "\n";
        $xml .= ' </Worksheet>' . "\n";
        $xml .= '</Workbook>';

        return response($xml, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
