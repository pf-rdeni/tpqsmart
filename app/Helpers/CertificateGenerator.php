<?php

namespace App\Helpers;

use Dompdf\Dompdf;
use Dompdf\Options;

class CertificateGenerator
{
    protected $template;
    protected $fields;
    protected $data;
    protected $dompdf;

    public function __construct($template, $fields)
    {
        $this->template = $template;
        $this->fields = $fields;

        log_message('info', 'CertificateGenerator initialized for Template ID: ' . $template['id']);

        // Configure DomPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $this->dompdf = new Dompdf($options);
    }

    /**
     * Set data untuk field
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Generate PDF
     */
    public function generate()
    {
        log_message('info', 'CertificateGenerator: generating PDF...');
        $html = $this->buildHtml();
        
        $this->dompdf->loadHtml($html);
        
        // Set paper size to match template dimensions exactly
        // DomPDF uses points (72pt/inch), while we use pixels (96px/inch default)
        // We need to convert px to pt for the paper size to match the CSS pixel dimensions
        $widthPx = (float) $this->template['Width'];
        $heightPx = (float) $this->template['Height'];
        
        $widthPt = ($widthPx * 72) / 96;
        $heightPt = ($heightPx * 72) / 96;
        
        // For custom paper size array [0, 0, width, height], we must use 'portrait'
        // otherwise DomPDF will flip the dimensions if we say 'landscape'
        $this->dompdf->setPaper([0, 0, $widthPt, $heightPt], 'portrait');
        
        $this->dompdf->render();
        log_message('info', 'CertificateGenerator: PDF generated successfully.');
        
        return $this;
    }

    /**
     * Output PDF
     * @param string $filename
     * @param string $dest D=Download, I=Inline, S=String, F=File
     */
    public function output($filename = 'certificate.pdf', $dest = 'D')
    {
        log_message('info', "CertificateGenerator: outputting PDF as {$dest} with filename {$filename}");
        $output = $this->dompdf->output();
        
        switch ($dest) {
            case 'D': // Download
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');
                echo $output;
                exit;
                
            case 'I': // Inline
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="' . $filename . '"');
                echo $output;
                exit;
                
            case 'S': // String
                return $output;
                
            case 'F': // File
                file_put_contents($filename, $output);
                return true;
        }
    }

    /**
     * Stream PDF directly
     */
    public function stream($filename = 'certificate.pdf', $options = [])
    {
        log_message('info', "CertificateGenerator: streaming PDF {$filename}");
        // Clear any previous output buffers to prevent corrupt PDF structure
        if (ob_get_length()) {
            ob_end_clean();
        }
        
        $attachment = $options['Attachment'] ?? true;
        $this->dompdf->stream($filename, ['Attachment' => $attachment]);
        exit;
    }

    /**
     * Build HTML for PDF
     */
    protected function buildHtml()
    {
        $templatePath = FCPATH . 'uploads/' . $this->template['FileTemplate'];
        $width = $this->template['Width'];
        $height = $this->template['Height'];

        // Convert image to base64 for embedding
        $imageData = base64_encode(file_get_contents($templatePath));
        $imageSrc = 'data:image/' . pathinfo($templatePath, PATHINFO_EXTENSION) . ';base64,' . $imageData;

        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        .certificate-container {
            position: relative;
            width: ' . $width . 'px;
            height: ' . $height . 'px;
        }
        .template-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .field {
            position: absolute;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <img src="' . $imageSrc . '" class="template-image">
        ';

        // Add fields
        foreach ($this->fields as $field) {
            $value = $this->getFieldValue($field['FieldName']);
            
            if ($value) {
                $style = $this->buildFieldStyle($field);
                $html .= '<div class="field" style="' . $style . '">' . htmlspecialchars($value) . '</div>';
            }
        }

        $html .= '
    </div>
</body>
</html>';

        return $html;
    }

    /**
     * Build CSS style for field
     */
    protected function buildFieldStyle($field)
    {
        $style = [];
        
        // Position
        $style[] = 'left: ' . $field['PosX'] . 'px';
        $style[] = 'top: ' . $field['PosY'] . 'px';
        
        // Font Mapping
        $fontFamily = $this->getFontMapping($field['FontFamily']);
        $style[] = "font-family: {$fontFamily}";
        $style[] = 'font-size: ' . $field['FontSize'] . 'px';
        
        // Font style
        if (strpos($field['FontStyle'], 'B') !== false) {
            $style[] = 'font-weight: bold';
        }
        if (strpos($field['FontStyle'], 'I') !== false) {
            $style[] = 'font-style: italic';
        }
        
        // Text align
        $textAlign = 'left';
        if ($field['TextAlign'] === 'C') {
            $textAlign = 'center';
            $style[] = 'transform: translateX(-50%)';
        } elseif ($field['TextAlign'] === 'R') {
            $textAlign = 'right';
            $style[] = 'transform: translateX(-100%)';
        }
        $style[] = 'text-align: ' . $textAlign;
        
        // Color
        $style[] = 'color: ' . $field['TextColor'];
        
        // Max width
        if ($field['MaxWidth'] > 0) {
            $style[] = 'max-width: ' . $field['MaxWidth'] . 'px';
        }
        
        return implode('; ', $style);
    }

    /**
     * Map friendly font names to DomPDF/CSS standard families
     */
    protected function getFontMapping($friendlyName)
    {
        $map = [
            'Arial' => "'Helvetica', sans-serif",
            'Times New Roman' => "'Times-Roman', serif",
            'Courier New' => "'Courier', monospace",
            'Dejavu Sans' => "'DejaVu Sans', sans-serif",
        ];

        return $map[$friendlyName] ?? "'Helvetica', sans-serif"; // Default to Helvetica/Arial
    }

    /**
     * Get field value from data
     */
    protected function getFieldValue($fieldName)
    {
        if (!isset($this->data[$fieldName])) {
            return '';
        }

        return $this->data[$fieldName];
    }

    /**
     * Helper to convert peringkat number to text
     */
    public static function peringkatToText($peringkat)
    {
        $map = [
            1 => 'JUARA PERTAMA',
            2 => 'JUARA KEDUA',
            3 => 'JUARA KETIGA',
            4 => 'JUARA KEEMPAT',
            5 => 'JUARA KELIMA',
        ];

        return $map[$peringkat] ?? 'PESERTA';
    }
}
