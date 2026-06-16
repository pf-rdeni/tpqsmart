<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LuckydrawKegiatanFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->has('active_id_kegiatan')) {
            // Check if user is logged in first
            if (logged_in()) {
                return redirect()->to('backend/luckydraw/pilih')->with('message', 'Silakan pilih kegiatan terlebih dahulu.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
