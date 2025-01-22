<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Team;
use App\Models\User;
use App\Models\Paper;
use App\Models\PvtMember;
use App\Models\PvtCustomBenefit;
use Illuminate\Support\Facades\Storage;
use TCPDF;
//use TCPDI;
use shihjay2\tcpdi_merger\MyTCPDI;
use shihjay2\tcpdi_merger\Merger;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use App\Services\GhostscriptService;
use View;

class EmailApprovalBenefit extends Mailable
{
    use Queueable, SerializesModels;

    public $paper;
    public $status;
    public $innovation_title;
    public $team_name;
    public $gmName;
    public $benefitFinancial;
    public $benefitPotential;
    public $potensiReplikasi;
    public $benefitNonFinancial;
    public $leaderName;
    public $inovasi_lokasi;

    public function __construct(Paper $paper, $status, $innovation_title, $team_name, $gmData, $benefitFinancial, $benefitPotential, $potensiReplikasi, $benefitNonFinancial, $leaderData, $inovasi_lokasi)
    {
        $this->paper = $paper;
        $this->status = $status;
        $this->innovation_title = $innovation_title;
        $this->team_name = $team_name;
        $this->gmName = $gmData->name;
        $this->benefitFinancial = $benefitFinancial;
        $this->benefitPotential = $benefitPotential;
        $this->potensiReplikasi = $potensiReplikasi;
        $this->benefitNonFinancial = $benefitNonFinancial;
        $this->leaderName = $leaderData->name;
        $this->inovasi_lokasi = $inovasi_lokasi;
    }

    public function build()
    {
        $attachment = $this->getAttachment();
        //$attachment = Storage::path('public/' . $this->paper->file_review);

        if ($this->status == 'accepted benefit by facilitator') {
            return $this->view('emails.email_benefit_approval')
                ->subject('Notification: Benefit Accepted')
                ->attach($attachment, [
                    'as' => 'Berita Acara Benefit.pdf',
                    'mime' => 'application/pdf',
                ]);
        } elseif ($this->status == 'rejected benefit by facilitator') {
            return $this->view('emails.email_benefit_approval')
                ->subject('Notification: Benefit Rejected')
                ->attach($attachment, [
                    'as' => 'Berita Acara Benefit.pdf',
                    'mime' => 'application/pdf',
                ]);
        } elseif ($this->status == 'revision benefit by facilitator') {
            return $this->view('emails.email_benefit_approval')
                ->subject('Notification: Benefit Revision')
                ->attach($attachment, [
                    'as' => 'Berita Acara Benefit.pdf',
                    'mime' => 'application/pdf',
                ]);
        } elseif ($this->status == 'revision benefit by facilitator') {
            return $this->view('emails.email_benefit_approval')
                ->subject('Notification: Revision Benefit')
                ->attach($attachment, [
                    'as' => 'Berita Acara Benefit.pdf',
                    'mime' => 'application/pdf',
                ]);
        } elseif ($this->status == 'accepted benefit by general manager') {
            return $this->view('emails.email_benefit_approval')
                ->subject('Notification: Benefit Accepted')
                ->attach($attachment, [
                    'as' => 'Berita Acara Benefit.pdf',
                    'mime' => 'application/pdf',
                ]);
        } elseif ($this->status == 'rejected benefit by general manager') {
            return $this->view('emails.email_benefit_approval')
                ->subject('Notification: Benefit Rejected')
                ->attach($attachment, [
                    'as' => 'Berita Acara Benefit.pdf',
                    'mime' => 'application/pdf',
                ]);
        } elseif ($this->status == 'revision benefit by general manager') {
            return $this->view('emails.email_benefit_approval')
                ->subject('Notification: Benefit Revision')
                ->attach($attachment, [
                    'as' => 'Berita Acara Benefit.pdf',
                    'mime' => 'application/pdf',
                ]);
        } elseif ($this->status == 'revision paper by general manager') {
            return $this->view('emails.email_benefit_approval')
                ->subject('Notification: Paper Revision')
                ->attach($attachment, [
                    'as' => 'Berita Acara Benefit.pdf',
                    'mime' => 'application/pdf',
                ]);
        } elseif ($this->status == 'revision paper and benefit by general manager') {
            return $this->view('emails.email_benefit_approval')
                ->subject('Notification: Paper & Benefit Revision')
                ->attach($attachment, [
                    'as' => 'Berita Acara Benefit.pdf',
                    'mime' => 'application/pdf',
                ]);
        }
    }

    public function getAttachment()
    {
        if ($this->status == 'accepted benefit by facilitator' || $this->status == 'accepted benefit by general manager' || $this->status == 'rejected benefit by general manager' || $this->status == 'reject' || $this->status == 'accept') {
            $pdfFolder = 'public/benefit-approvals/';
            $pdf = new TCPDF();
            $pdf->SetFont('helvetica', '', 12);
            $pdf->AddPage();

            $facilitatorRow = $this->generatePdfContentForRole('facilitator');
            $gmRow = '';
            $adminRow = '';

            if ($this->status == 'accepted benefit by general manager' || $this->status == 'reject') {
                $gmRow = $this->generatePdfContentForRole('gm');
            }

            if ($this->status == 'accept') {
                $gmRow = $this->generatePdfContentForRole('gm');
                $adminRow = $this->generatePdfContentForRole('admin');
            }

            $html = '<style>
                        table {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        th, td {
                            border: 1px solid black;
                            padding: 3px;
                            text-align: center;
                            vertical-align: middle;
                        }
                        th {
                            background-color: #f2f2f2;
                            text-align: center;
                        }
                        img {
                            width: 70px;
                            height: auto;
                        }
                    </style>
                    <table border="1" cellpadding="5">
                        <tr>
                            <th width="33%">NAMA</th>
                            <th width="33%">KETERANGAN</th>
                            <th width="34%">TANDA TANGAN</th>
                        </tr>
                            ' . $facilitatorRow . '
                            ' . $gmRow . '
                            ' . $adminRow . '
                    </table>';

            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf_file_name = 'Berita Acara Benefit_' . $this->paper->id . '.pdf';
            //$pdf_folder = 'public/benefit-approvals/';
            Storage::put($pdfFolder . $pdf_file_name, $pdf->Output($pdf_file_name, 'S'));

            $attachment = Storage::path($pdfFolder . $pdf_file_name);

            // $file_review = Storage::path($this->paper->file_review);
            $file_review_path = $this->paper->file_review;
            $file_review = Storage::disk('public')->path($file_review_path);

            $pdfMerger = PDFMerger::init();
            $pdfMerger->addPDF($file_review, 'all');
            $pdfMerger->addPDF($attachment, 'all');
            $pdfMerger->merge();
            $filename = time() . '_attachment_acc.pdf';
            Storage::disk('public')->put('benefit-approvals/' . $filename, $pdfMerger->output());
            $attachment = Storage::disk('public')->path('benefit-approvals/' . $filename);
        } else {
            $attachment = Storage::path($this->paper->file_review);
        }

        return $attachment;
    }

    public function generatePdfContentForRole($role)
    {
        $name = '';
        $employee_id = '';
        $position_title = '';
        $qr_code_data = '';
        $description = '';

        if ($role == 'facilitator') {
            $facilitator = PvtMember::where('team_id', $this->paper->team->id)
                ->where('status', 'facilitator')
                ->pluck('employee_id')
                ->first();
            $facilitatorData = User::where('employee_id', $facilitator)
                ->select('name', 'employee_id', 'position_title')
                ->first();
            $name = $facilitatorData->name;
            $employee_id = $facilitatorData->employee_id;
            $position_title = $facilitatorData->position_title;
            $description = 'Fasilitator';
        } elseif ($role == 'gm') {
            $gm = PvtMember::where('team_id', $this->paper->team->id)
                ->where('status', 'gm')
                ->pluck('employee_id')
                ->first();
            $gmData = User::where('employee_id', $gm)
                ->select('name', 'employee_id', 'position_title')
                ->first();
            $name = $gmData->name;
            $employee_id = $gmData->employee_id;
            $position_title = $gmData->position_title;
            $description = 'General Manager';
        } else {
            $adminData = User::where('role', 'Admin')
                ->select('name', 'employee_id', 'position_title')
                ->first();
            $name = $adminData->name;
            $employee_id = $adminData->employee_id;
            $position_title = $adminData->position_title;
            $description = 'Admin';
        }

        $info = "Benefit yang diajukan telah ditinjau dan disetujui oleh :\n"
            . "Nama Pegawai : " . $name . "\n"
            . "Nomor Pegawai: " . $employee_id . "\n"
            . "Jabatan: " . $position_title . "\n"
            . "Waktu Approval: " . now()->format('Y-m-d H:i:s') . "\n";

        $qr_code = new QrCode($info);
        $qr_code->setSize(200);
        $writer = new PngWriter();
        $qr_code_data = $writer->write($qr_code)->getString();

        //return $qr_code_data;
        return
            '<tr>
            <td>' . $name . '</td>
            <td>' . $description . '</td>
            <td><img src="data:image/png;base64,' . base64_encode($qr_code_data) . '" /></td>
        </tr>';
    }
}
