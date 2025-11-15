<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Log\Handlers\FileHandler;

class Logger extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Error Logging Threshold
     * --------------------------------------------------------------------------
     *
     * You can enable error logging by setting a threshold over zero. The
     * threshold determines what gets logged. Any values below or equal to the
     * threshold will be logged.
     *
     * Threshold options are:
     *
     * - 0 = Disables logging, Error logging TURNED OFF
     * - 1 = Emergency Messages - System is unusable
     * - 2 = Alert Messages - Action Must Be Taken Immediately
     * - 3 = Critical Messages - Application component unavailable, unexpected exception.
     * - 4 = Runtime Errors - Don't need immediate action, but should be monitored.
     * - 5 = Warnings - Exceptional occurrences that are not errors.
     * - 6 = Notices - Normal but significant events.
     * - 7 = Info - Interesting events, like user logging in, etc.
     * - 8 = Debug - Detailed debug information.
     * - 9 = All Messages
     *
     * You can also pass an array with threshold levels to show individual error types
     *
     *     array(1, 2, 3, 8) = Emergency, Alert, Critical, and Debug messages
     *
     * For a live site you'll usually enable Critical or higher (3) to be logged otherwise
     * your log files will fill up very fast.
     * 
     * Production Setting:
     * - Option 1: Use threshold 7 to log all up to INFO level (recommended for monitoring)
     * - Option 2: Use array [1, 2, 3, 4, 7] to log Emergency, Alert, Critical, Error, and Info only
     * 
     * Note: Using INFO level in production will increase log file size significantly.
     * Consider setting up log rotation to automatically delete old logs.
     *
     * @var int|list<int>
     */
    public $threshold = (ENVIRONMENT === 'production') ? 7 : 9;

    /**
     * --------------------------------------------------------------------------
     * Date Format for Logs
     * --------------------------------------------------------------------------
     *
     * Each item that is logged has an associated date. You can use PHP date
     * codes to set your own date formatting
     */
    public string $dateFormat = 'Y-m-d H:i:s';

    /**
     * --------------------------------------------------------------------------
     * Log Retention (Auto Delete Old Logs)
     * --------------------------------------------------------------------------
     *
     * Number of days to keep log files before automatic deletion.
     * Set to 0 to disable automatic deletion (logs will never be deleted).
     * 
     * Recommended settings:
     * - Development: 7-14 days
     * - Production: 30-90 days (depending on storage capacity)
     * 
     * Note: This setting only applies if log cleanup is implemented via cron job
     * or scheduled task. CodeIgniter 4 does not have built-in log rotation.
     * 
     * To enable automatic log cleanup:
     * 1. Setup cron job: 0 2 * * * php /path/to/spark log:cleanup
     * 2. Or use logrotate tool: /etc/logrotate.d/codeigniter
     * 3. Or create scheduled task to run cleanup script
     * 
     * @var int Number of days to retain logs (0 = never delete)
     */
    public int $retentionDays = (ENVIRONMENT === 'production') ? 30 : 7;

    /**
     * --------------------------------------------------------------------------
     * Log Handlers
     * --------------------------------------------------------------------------
     *
     * The logging system supports multiple actions to be taken when something
     * is logged. This is done by allowing for multiple Handlers, special classes
     * designed to write the log to their chosen destinations, whether that is
     * a file on the getServer, a cloud-based service, or even taking actions such
     * as emailing the dev team.
     *
     * Each handler is defined by the class name used for that handler, and it
     * MUST implement the `CodeIgniter\Log\Handlers\HandlerInterface` interface.
     *
     * The value of each key is an array of configuration items that are sent
     * to the constructor of each handler. The only required configuration item
     * is the 'handles' element, which must be an array of integer log levels.
     * This is most easily handled by using the constants defined in the
     * `Psr\Log\LogLevel` class.
     *
     * Handlers are executed in the order defined in this array, starting with
     * the handler on top and continuing down.
     *
     * @var array<class-string, array<string, int|list<string>|string>>
     */
    public array $handlers = [
        /*
         * --------------------------------------------------------------------
         * File Handler
         * --------------------------------------------------------------------
         */
        FileHandler::class => [
            // The log levels that this handler will handle.
            'handles' => [
                'critical',
                'alert',
                'emergency',
                'debug',
                'error',
                'info',
                'notice',
                'warning',
            ],

            /*
             * The default filename extension for log files.
             * An extension of 'php' allows for protecting the log files via basic
             * scripting, when they are to be stored under a publicly accessible directory.
             *
             * NOTE: Leaving it blank will default to 'log'.
             */
            'fileExtension' => '',

            /*
             * The file system permissions to be applied on newly created log files.
             *
             * IMPORTANT: This MUST be an integer (no quotes) and you MUST use octal
             * integer notation (i.e. 0700, 0644, etc.)
             */
            'filePermissions' => 0644,

            /*
             * Logging Directory Path
             *
             * By default, logs are written to WRITEPATH . 'logs/'
             * Specify a different destination here, if desired.
             */
            'path' => '',
        ],

        /*
         * The ChromeLoggerHandler requires the use of the Chrome web browser
         * and the ChromeLogger extension. Uncomment this block to use it.
         */
        // 'CodeIgniter\Log\Handlers\ChromeLoggerHandler' => [
        //     /*
        //      * The log levels that this handler will handle.
        //      */
        //     'handles' => ['critical', 'alert', 'emergency', 'debug',
        //                   'error', 'info', 'notice', 'warning'],
        // ],

        /*
         * The ErrorlogHandler writes the logs to PHP's native `error_log()` function.
         * Uncomment this block to use it.
         */
        // 'CodeIgniter\Log\Handlers\ErrorlogHandler' => [
        //     /* The log levels this handler can handle. */
        //     'handles' => ['critical', 'alert', 'emergency', 'debug', 'error', 'info', 'notice', 'warning'],
        //
        //     /*
        //     * The message type where the error should go. Can be 0 or 4, or use the
        //     * class constants: `ErrorlogHandler::TYPE_OS` (0) or `ErrorlogHandler::TYPE_SAPI` (4)
        //     */
        //     'messageType' => 0,
        // ],
    ];
}
