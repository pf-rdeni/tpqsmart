<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;

class LogViewer extends BaseController
{
    /**
     * Display log viewer index page
     * Only accessible by Admin
     */
    public function index()
    {
        // Check if user is Admin
        if (!in_groups('Admin')) {
            return redirect()->to(base_url('auth/index'))->with('error', 'Anda tidak memiliki akses untuk melihat log.');
        }

        // Get date parameter or use today's date
        $date = $this->request->getGet('date') ?? date('Y-m-d');
        
        // Get log files list
        $logFiles = $this->getLogFiles();
        
        // Get log content for selected date (no filters on initial load - show all)
        $logContent = $this->getLogContent($date, null, null, 1000);
        
        // Get log statistics
        $logStats = $this->getLogStatistics($date);

        // Get current logger threshold
        $loggerConfig = new \Config\Logger();
        $currentThreshold = $loggerConfig->getThreshold();
        $thresholdOverride = session()->get('logger_threshold_override');

        $data = [
            'page_title' => 'Log Viewer',
            'date' => $date,
            'logFiles' => $logFiles,
            'logContent' => $logContent,
            'logStats' => $logStats,
            'currentThreshold' => $currentThreshold,
            'thresholdOverride' => $thresholdOverride,
        ];

        return view('backend/logviewer/index', $data);
    }

    /**
     * Get current logger threshold setting
     */
    public function getLoggerThreshold()
    {
        // Check if user is Admin
        if (!in_groups('Admin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda tidak memiliki akses'
            ])->setStatusCode(403);
        }

        $loggerConfig = new \Config\Logger();
        $currentThreshold = $loggerConfig->getThreshold();
        $thresholdOverride = session()->get('logger_threshold_override');
        $defaultThreshold = (ENVIRONMENT === 'production') ? 7 : 9;

        return $this->response->setJSON([
            'success' => true,
            'currentThreshold' => $currentThreshold,
            'thresholdOverride' => $thresholdOverride,
            'defaultThreshold' => $defaultThreshold,
            'isOverridden' => $thresholdOverride !== null
        ]);
    }

    /**
     * Update logger threshold setting
     */
    public function updateLoggerThreshold()
    {
        // Check if user is Admin
        if (!in_groups('Admin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda tidak memiliki akses'
            ])->setStatusCode(403);
        }

        $threshold = $this->request->getPost('threshold');
        $action = $this->request->getPost('action'); // 'set' or 'reset'

        if ($action === 'reset') {
            // Reset to default
            session()->remove('logger_threshold_override');

            // Reload config to apply default
            $loggerConfig = new \Config\Logger();
            $newThreshold = $loggerConfig->getThreshold();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Logger threshold berhasil direset ke default',
                'currentThreshold' => $newThreshold,
                'isOverridden' => false
            ]);
        }

        // Validate threshold value
        if ($threshold === null || $threshold === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Threshold tidak boleh kosong'
            ]);
        }

        // Convert to integer or array
        if (is_array($threshold)) {
            $thresholdValue = array_map('intval', $threshold);
        } else {
            $thresholdValue = (int)$threshold;

            // Validate threshold range (0-9)
            if ($thresholdValue < 0 || $thresholdValue > 9) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Threshold harus antara 0-9'
                ]);
            }
        }

        // Save to session
        session()->set('logger_threshold_override', $thresholdValue);

        // Reload config to apply new threshold
        $loggerConfig = new \Config\Logger();
        $newThreshold = $loggerConfig->getThreshold();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Logger threshold berhasil diubah',
            'currentThreshold' => $newThreshold,
            'isOverridden' => true
        ]);
    }

    /**
     * Get log content for specific date
     */
    public function getLogContentByDate()
    {
        // Check if user is Admin
        if (!in_groups('Admin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda tidak memiliki akses'
            ])->setStatusCode(403);
        }

        $date = $this->request->getPost('date') ?? date('Y-m-d');
        $filters = $this->request->getPost('filters'); // Array of filters: ['ERROR', 'WARNING'], etc.
        
        // Ensure filters is an array
        if ($filters !== null && !is_array($filters)) {
            $filters = [$filters];
        }
        
        $search = $this->request->getPost('search'); // Search keyword
        $lines = (int)($this->request->getPost('lines') ?? 1000); // Number of lines to show

        $logContent = $this->getLogContent($date, $filters, $search, $lines);

        return $this->response->setJSON([
            'success' => true,
            'content' => $logContent['content'],
            'stats' => $logContent['stats'],
            'totalLines' => $logContent['totalLines'],
            'shownLines' => $logContent['shownLines'] ?? $logContent['filteredTotal'] ?? 0
        ]);
    }

    /**
     * Download log file
     */
    public function download()
    {
        // Check if user is Admin
        if (!in_groups('Admin')) {
            return redirect()->to(base_url('auth/index'))->with('error', 'Anda tidak memiliki akses.');
        }

        $date = $this->request->getGet('date') ?? date('Y-m-d');
        $logFile = WRITEPATH . 'logs/log-' . $date . '.log';

        if (!file_exists($logFile)) {
            return redirect()->back()->with('error', 'File log tidak ditemukan.');
        }

        return $this->response->download($logFile, null);
    }

    /**
     * Get list of available log files
     */
    private function getLogFiles()
    {
        $logDir = WRITEPATH . 'logs/';
        $files = [];
        
        if (is_dir($logDir)) {
            $items = scandir($logDir);
            foreach ($items as $item) {
                if (preg_match('/^log-(\d{4}-\d{2}-\d{2})\.log$/', $item, $matches)) {
                    $files[] = [
                        'filename' => $item,
                        'date' => $matches[1],
                        'size' => filesize($logDir . $item),
                        'modified' => filemtime($logDir . $item)
                    ];
                }
            }
        }

        // Sort by date descending (newest first)
        usort($files, function($a, $b) {
            return strcmp($b['date'], $a['date']);
        });

        return $files;
    }

    /**
     * Get log content for specific date with filters
     * @param string $date Date in Y-m-d format
     * @param array|null $filters Array of log levels to filter (e.g., ['ERROR', 'WARNING'])
     * @param string|null $search Search keyword
     * @param int $lines Number of lines to show
     * @return array
     */
    private function getLogContent($date, $filters = null, $search = null, $lines = 1000)
    {
        $logFile = WRITEPATH . 'logs/log-' . $date . '.log';
        
        if (!file_exists($logFile)) {
            return [
                'content' => "File log untuk tanggal {$date} tidak ditemukan.",
                'stats' => [
                    'total' => 0,
                    'info' => 0,
                    'error' => 0,
                    'warning' => 0,
                    'debug' => 0
                ],
                'totalLines' => 0
            ];
        }

        // Read file
        $content = file_get_contents($logFile);
        $allLines = explode("\n", $content);
        $totalLines = count($allLines);

        // Get last N lines (default 1000)
        $linesToShow = array_slice($allLines, -$lines);

        // Apply filters
        $filteredLines = [];
        $stats = [
            'total' => 0,
            'info' => 0,
            'error' => 0,
            'warning' => 0,
            'debug' => 0,
            'critical' => 0,
            'alert' => 0,
            'emergency' => 0,
            'notice' => 0
        ];

        foreach ($linesToShow as $line) {
            if (empty(trim($line))) {
                continue;
            }

            // Count by level
            if (preg_match('/^(INFO|ERROR|WARNING|DEBUG|CRITICAL|ALERT|EMERGENCY|NOTICE)\s/', $line, $matches)) {
                $level = strtolower($matches[1]);
                $stats['total']++;
                if (isset($stats[$level])) {
                    $stats[$level]++;
                }
            }

            // Apply filter by level (multiple filters support)
            if ($filters && is_array($filters) && count($filters) > 0) {
                $matched = false;
                foreach ($filters as $filter) {
                    $filterUpper = strtoupper(trim($filter));
                    if (preg_match('/^' . preg_quote($filterUpper, '/') . '\s/', $line)) {
                        $matched = true;
                        break;
                    }
                }
                if (!$matched) {
                    continue;
                }
            }

            // Apply search
            if ($search && stripos($line, $search) === false) {
                continue;
            }

            $filteredLines[] = $line;
        }

        // Format output with color coding
        $formattedContent = '';
        foreach ($filteredLines as $line) {
            $formattedLine = htmlspecialchars($line);
            
            // Add color coding based on log level
            if (preg_match('/^(ERROR|CRITICAL|ALERT|EMERGENCY)\s/', $line)) {
                $formattedContent .= '<div class="log-line log-error">' . $formattedLine . '</div>';
            } elseif (preg_match('/^WARNING\s/', $line)) {
                $formattedContent .= '<div class="log-line log-warning">' . $formattedLine . '</div>';
            } elseif (preg_match('/^INFO\s/', $line)) {
                $formattedContent .= '<div class="log-line log-info">' . $formattedLine . '</div>';
            } elseif (preg_match('/^DEBUG\s/', $line)) {
                $formattedContent .= '<div class="log-line log-debug">' . $formattedLine . '</div>';
            } else {
                $formattedContent .= '<div class="log-line">' . $formattedLine . '</div>';
            }
        }

        return [
            'content' => $formattedContent ?: 'Tidak ada log yang sesuai dengan filter yang dipilih.',
            'stats' => $stats,
            'totalLines' => $totalLines,
            'shownLines' => count($filteredLines),
            'filteredTotal' => count($filteredLines)
        ];
    }

    /**
     * Get log statistics for specific date
     */
    private function getLogStatistics($date)
    {
        $logFile = WRITEPATH . 'logs/log-' . $date . '.log';
        
        if (!file_exists($logFile)) {
            return [
                'total' => 0,
                'info' => 0,
                'error' => 0,
                'warning' => 0,
                'debug' => 0,
                'fileSize' => 0,
                'lastModified' => null
            ];
        }

        $stats = [
            'total' => 0,
            'info' => 0,
            'error' => 0,
            'warning' => 0,
            'debug' => 0,
            'critical' => 0,
            'alert' => 0,
            'emergency' => 0,
            'notice' => 0,
            'fileSize' => filesize($logFile),
            'lastModified' => filemtime($logFile)
        ];

        $content = file_get_contents($logFile);
        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            if (preg_match('/^(INFO|ERROR|WARNING|DEBUG|CRITICAL|ALERT|EMERGENCY|NOTICE)\s/', $line, $matches)) {
                $level = strtolower($matches[1]);
                $stats['total']++;
                if (isset($stats[$level])) {
                    $stats[$level]++;
                }
            }
        }

        return $stats;
    }
}

