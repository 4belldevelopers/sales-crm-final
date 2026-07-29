<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory;

/**
 * Downloads a resume from R2 and extracts raw text from PDF / DOCX / DOC.
 */
class ResumeTextExtractorService
{
    /**
     * Download the object from R2 to a temp file, extract text, then delete temp file.
     *
     * @param  string $objectKey  R2 object path (e.g. temp/uuid.pdf)
     * @return string             Extracted plain text
     */
    public function extractFromR2(string $objectKey): string
    {
        $ext     = strtolower(pathinfo($objectKey, PATHINFO_EXTENSION));
        $content = Storage::disk('r2')->get($objectKey);

        if ($content === null || $content === false) {
            throw new \RuntimeException("Cannot read R2 object: {$objectKey}");
        }

        return match ($ext) {
            'pdf'  => $this->extractPdfFromContent($content),
            'docx' => $this->extractDocxFromContent($content),
            'doc'  => $this->extractDocFromContent($content),
            default => throw new \RuntimeException("Unsupported file type: {$ext}"),
        };
    }

    /** PDF — fully in-memory via parseContent(), no local file needed */
    private function extractPdfFromContent(string $content): string
    {
        $parser = new PdfParser();
        $pdf    = $parser->parseContent($content);
        return $pdf->getText();
    }

    /**
     * DOCX — phpoffice/phpword requires a real file path (ZIP format).
     * tmpfile() gives an anonymous OS-managed file: auto-deleted when the
     * handle is closed or the process exits — no named temp file, no orphans.
     */
    private function extractDocxFromContent(string $content): string
    {
        $handle = tmpfile();
        if ($handle === false) {
            throw new \RuntimeException('Could not create anonymous temp file for DOCX parsing');
        }

        try {
            fwrite($handle, $content);
            $tmpPath = stream_get_meta_data($handle)['uri'];

            $phpWord = IOFactory::load($tmpPath, 'Word2007');
            $text    = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    } elseif (method_exists($element, 'getElements')) {
                        foreach ($element->getElements() as $child) {
                            if (method_exists($child, 'getText')) {
                                $text .= $child->getText() . ' ';
                            }
                        }
                        $text .= "\n";
                    }
                }
            }

            return $text;
        } finally {
            fclose($handle); // OS deletes the anonymous file immediately
        }
    }

    /** DOC — binary string manipulation, fully in-memory */
    private function extractDocFromContent(string $content): string
    {
        $content = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', ' ', $content);
        $content = preg_replace('/\s+/', ' ', $content);
        return trim(strip_tags($content));
    }
}
