<?php

namespace App\Traits;
use Illuminate\Http\Client\Response;


trait FileNameTrait{

    /**
     * ambil nama untuk berbagai jenis file, excel, pdf etc
     */
    public function getFileName(Response $file, string $default = 'document'): string
    {
        $contentDisposition = $file->header('Content-Disposition');
        $filename = $this->parseContentDisposition($contentDisposition);
        
        if ($filename) {
            return $filename;
        }

        // Fallback: generate default file name
        return $this->generateDefaultFileName($file, $default);
    }

    private function parseContentDisposition(?string $contentDisposition): ?string{

        if (empty($contentDisposition)) {
            return null;
        }

        // Try different patterns
        $patterns = [
           '/filename="([^"]+)"/',           // filename="file.xlsx" atau filename="file.pdf"
            "/filename='([^']+)'/",           // filename='file.xlsx' atau filename='file.pdf'
            '/filename=([^;\s]+)/',           // filename=file.xlsx atau filename=file.pdf
            "/filename\*=(?:UTF-8'')?(.+)/", // filename*=UTF-8''file.xlsx atau filename*=UTF-8''file.pdf
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $contentDisposition, $matches)) {
                return urldecode(trim($matches[1]));
            }
        }

        return null;
    }

    /**
     * generate default file name dari mime type nya kalo gada filename dari backend
     */
    private function generateDefaultFileName(Response $file, $default): string
    {
        // Get extension from MIME typenya
        $mimeType = $file?->header('Content-Type');
        $extension = $this->getExtensionFromMime($mimeType);
        
        return $default . '.' . $extension;
    }

    private function getExtensionFromMime($mimeType): string
    {
        $mimeToExt = [
            'application/pdf' => 'pdf',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/msword' => 'doc',
            'text/csv' => 'csv',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
        ];
        
        return $mimeToExt[$mimeType] ?? 'bin';
    }
}