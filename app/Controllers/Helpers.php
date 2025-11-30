<?php

namespace App\Controllers;

class Helpers extends BaseController
{
    /**
     * Serve JavaScript helper files from app/Helpers/js/
     * 
     * @param string $filename Nama file JavaScript helper
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function js($filename)
    {
        // Security: hanya izinkan file .js dengan karakter alphanumeric, underscore, dan dash
        if (!preg_match('/^[a-zA-Z0-9_-]+\.js$/', $filename)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Invalid filename');
        }
        
        $filePath = APPPATH . 'Helpers/js/' . $filename;
        
        if (!file_exists($filePath)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found: ' . $filename);
        }
        
        // Set proper headers untuk JavaScript dengan MIME type yang benar
        $this->response->setContentType('application/javascript; charset=utf-8');
        
        // Tambahkan header untuk mencegah MIME type sniffing
        $this->response->setHeader('X-Content-Type-Options', 'nosniff');
        
        // Set CORS header jika diperlukan (optional, bisa dihapus jika tidak perlu)
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        
        // Set cache header untuk performa (1 jam)
        $this->response->setHeader('Cache-Control', 'public, max-age=3600');
        $this->response->setHeader('Expires', gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
        
        // Set ETag untuk cache validation
        $etag = md5_file($filePath);
        $this->response->setHeader('ETag', '"' . $etag . '"');
        
        // Check if client has cached version
        $ifNoneMatch = $this->request->getHeaderLine('If-None-Match');
        if ($ifNoneMatch && $ifNoneMatch === '"' . $etag . '"') {
            return $this->response->setStatusCode(304)->setBody('');
        }
        
        // Baca dan kirim file
        $this->response->setBody(file_get_contents($filePath));
        
        return $this->response;
    }
}

