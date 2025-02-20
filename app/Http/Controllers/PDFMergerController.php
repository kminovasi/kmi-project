<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;
use setasign\FpdiPdfParser\PdfParser\PdfParser as FpdiPdfParser;
use setasign\FpdiPdfParser\PdfParser\CrossReference\CompressedReader;
use setasign\FpdiPdfParser\PdfParser\CrossReference\CorruptedReader;

class PDFMergerController extends Controller
{
    public function merge(Request $request)
    {
        $parser = 'default';
        $parserParams = [
            FpdiPdfParser::PARAM_PASSWORD => '',
            FpdiPdfParser::PARAM_IGNORE_PERMISSIONS => false
        ];

        // Ambil file yang diunggah dan file lokal
        $files = $request->file('files');
        $localFilePath = Storage::disk('public')->path('assets/file1.pdf');
        $localFilePath2 = Storage::disk('public')->path('fileku/file2.pdf');

        // Periksa apakah semua file lokal ada
        if (!file_exists($localFilePath) || !file_exists($localFilePath2)) {
            return response()->json(['error' => 'One or more local files not found.'], 404);
        }

        // Extend FPDI untuk menggunakan parser yang sesuai dan mengakses informasi dari parser instance
        class Pdf extends Fpdi
        {
            protected $pdfParserClass = null;

            public function setPdfParserClass($pdfParserClass)
            {
                $this->pdfParserClass = $pdfParserClass;
            }

            protected function getPdfParserInstance(StreamReader $streamReader, array $parserParams = [])
            {
                if ($this->pdfParserClass !== null) {
                    return new $this->pdfParserClass($streamReader, $parserParams);
                }

                return parent::getPdfParserInstance($streamReader, $parserParams);
            }

            public function getXrefInfo()
            {
                foreach (array_keys($this->readers) as $readerId) {
                    $crossReference = $this->getPdfReader($readerId)->getParser()->getCrossReference();
                    $readers = $crossReference->getReaders();
                    foreach ($readers as $reader) {
                        if ($reader instanceof CompressedReader) {
                            return 'compressed';
                        }

                        if ($reader instanceof CorruptedReader) {
                            return 'corrupted';
                        }
                    }
                }

                return 'normal';
            }

            public function isSourceFileEncrypted()
            {
                $reader = $this->getPdfReader($this->currentReaderId);
                if ($reader && $reader->getParser() instanceof FpdiPdfParser) {
                    return $reader->getParser()->getSecHandler() !== null;
                }

                return false;
            }
        }

        $pdf = new Pdf();
        if ($parser === 'default') {
            $pdf->setPdfParserClass(PdfParser::class);
        }

        $pdf->AddPage();
        $pdf->setSourceFileWithParserParams($localFilePath2, $parserParams);
        $tplIdx = $pdf->ImportPage(1);
        $size = $pdf->useTemplate($tplIdx, 20, 20, 100);
        $pdf->SetDrawColor(216);
        $pdf->Rect(20, 20, 100, $size['height'], 'D');

        $leftMargin = $pdf->getX() + 20 + 100;
        $pdf->SetLeftMargin($leftMargin);
        $pdf->SetXY($leftMargin, 20);
        $pdf->SetFont('helvetica');

        $xrefInfo = $pdf->getXrefInfo();

        if ($xrefInfo === 'compressed') {
            $pdf->SetTextColor(72, 179, 84);
            $pdf->Write(5, 'This document uses new PDF compression technics introduced in PDF version 1.5 and can be handled with the FPDI PDF-Parser add-on!');
        } elseif ($xrefInfo === 'corrupted') {
            $pdf->SetTextColor(72, 179, 84);
            $pdf->Write(5, 'This document is corrupted but can be read and repaired with the FPDI PDF-Parser add-on.');
        } elseif (!$pdf->isSourceFileEncrypted()) {
            $pdf->SetTextColor(182);
            $pdf->Write(5, 'This document will work with the free parser version.');
        }

        if ($pdf->isSourceFileEncrypted()) {
            $pdf->Ln();
            $pdf->Ln();
            $pdf->SetTextColor(72, 179, 84);
            $pdf->Write(5, 'The document is encrypted, you are authenticated appropriately or ignore the permissions and it can be handled with the FPDI PDF-Parser add-on!');
        }

        // Tambahkan file yang diunggah ke merger jika ada
        if ($request->hasFile('files')) {
            foreach ($files as $file) {
                $pdf->setSourceFileWithParserParams($file->getRealPath(), $parserParams);
                $tplIdx = $pdf->ImportPage(1);
                $pdf->useTemplate($tplIdx);
            }
        }

        // Gabungkan PDF dan simpan ke disk
        $filename = time() . '.pdf';
        $pdf->Output('F', storage_path('app/public/' . $filename));

        // Unduh file gabungan
        return response()->download(storage_path('app/public/' . $filename));
    }
}