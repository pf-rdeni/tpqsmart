<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Cleanup old log files based on retention days setting
 * 
 * Usage: php spark log:cleanup
 * 
 * This command will delete log files older than the retention period
 * defined in app/Config/Logger.php
 */
class LogCleanup extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Logging';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'log:cleanup';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Delete old log files based on retention days setting';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'log:cleanup [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--days' => 'Override retention days from config (optional)',
        '--dry-run' => 'Show what would be deleted without actually deleting',
        '--force' => 'Force deletion without confirmation',
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        // Load Logger config
        $logger = config('Logger');
        
        // Get retention days
        $retentionDays = CLI::getOption('days');
        if ($retentionDays === null) {
            // Use reflection to get retentionDays from config
            $reflection = new \ReflectionClass($logger);
            if ($reflection->hasProperty('retentionDays')) {
                $retentionProperty = $reflection->getProperty('retentionDays');
                $retentionProperty->setAccessible(true);
                $retentionDays = $retentionProperty->getValue($logger);
            } else {
                $retentionDays = 0;
            }
        } else {
            $retentionDays = (int)$retentionDays;
        }

        // Check if retention is enabled
        if ($retentionDays <= 0) {
            CLI::write('Log retention is disabled (retentionDays = 0). No files will be deleted.', 'yellow');
            CLI::write('To enable log cleanup, set retentionDays > 0 in app/Config/Logger.php', 'yellow');
            return;
        }

        CLI::write("Starting log cleanup (retention: {$retentionDays} days)...", 'green');

        // Get log directory
        $logDir = WRITEPATH . 'logs/';

        if (!is_dir($logDir)) {
            CLI::error("Log directory not found: {$logDir}");
            return;
        }

        // Calculate cutoff date
        $cutoffDate = strtotime("-{$retentionDays} days");
        $cutoffDateFormatted = date('Y-m-d', $cutoffDate);

        CLI::write("Cutoff date: {$cutoffDateFormatted} (files older than this will be deleted)", 'cyan');

        // Find old log files
        $oldLogs = [];
        $totalSize = 0;
        $items = scandir($logDir);

        foreach ($items as $item) {
            // Match log files: log-YYYY-MM-DD.log
            if (preg_match('/^log-(\d{4}-\d{2}-\d{2})\.log$/', $item, $matches)) {
                $fileDate = strtotime($matches[1]);
                
                if ($fileDate < $cutoffDate) {
                    $filePath = $logDir . $item;
                    $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
                    $age = floor((time() - $fileDate) / 86400); // days old
                    
                    $oldLogs[] = [
                        'filename' => $item,
                        'path' => $filePath,
                        'date' => $matches[1],
                        'age' => $age,
                        'size' => $fileSize,
                    ];
                    
                    $totalSize += $fileSize;
                }
            }
        }

        // Display results
        if (empty($oldLogs)) {
            CLI::write('No old log files found. All log files are within retention period.', 'green');
            return;
        }

        CLI::write("\nFound " . count($oldLogs) . " old log file(s) to delete:", 'yellow');
        CLI::newLine();

        // Display table
        $table = [];
        $table[] = ['Filename', 'Date', 'Age (days)', 'Size'];
        
        foreach ($oldLogs as $log) {
            $table[] = [
                $log['filename'],
                $log['date'],
                $log['age'],
                $this->formatBytes($log['size']),
            ];
        }
        
        CLI::table($table);

        CLI::write("Total size: " . $this->formatBytes($totalSize), 'cyan');
        CLI::newLine();

        // Dry run mode
        if (CLI::getOption('dry-run')) {
            CLI::write('DRY RUN MODE: No files were actually deleted.', 'yellow');
            CLI::write('Run without --dry-run to actually delete these files.', 'yellow');
            return;
        }

        // Confirmation (unless --force)
        if (!CLI::getOption('force')) {
            $confirmed = CLI::prompt('Delete these files?', ['y', 'n'], 'required');
            if (strtolower($confirmed) !== 'y') {
                CLI::write('Operation cancelled.', 'yellow');
                return;
            }
        }

        // Delete files
        $deletedCount = 0;
        $failedCount = 0;

        CLI::write("\nDeleting files...", 'green');

        foreach ($oldLogs as $log) {
            if (file_exists($log['path'])) {
                if (unlink($log['path'])) {
                    CLI::write("  ✓ Deleted: {$log['filename']}", 'green');
                    $deletedCount++;
                } else {
                    CLI::write("  ✗ Failed: {$log['filename']}", 'red');
                    $failedCount++;
                }
            } else {
                CLI::write("  ! Not found: {$log['filename']}", 'yellow');
            }
        }

        // Summary
        CLI::newLine();
        CLI::write("Cleanup completed:", 'green');
        CLI::write("  - Deleted: {$deletedCount} file(s)", 'green');
        
        if ($failedCount > 0) {
            CLI::write("  - Failed: {$failedCount} file(s)", 'red');
        }
        
        CLI::write("  - Space freed: " . $this->formatBytes($totalSize), 'cyan');
    }

    /**
     * Format bytes to human readable format
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

