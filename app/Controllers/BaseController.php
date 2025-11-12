<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['auth'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();

        // Disable ONLY_FULL_GROUP_BY mode to fix compatibility issues
        // with MySQL 5.7.5+ strict GROUP BY mode
        // This fixes the error: Expression #1 of SELECT list is not in GROUP BY clause
        static $sqlModeSet = false;
        if (!$sqlModeSet) {
            try {
                $db = \Config\Database::connect();
                if (method_exists($db, 'getPlatform') && $db->getPlatform() === 'MySQLi') {
                    // Get current sql_mode
                    $result = $db->query("SELECT @@sql_mode as sql_mode")->getRow();
                    $sqlMode = $result->sql_mode ?? '';

                    // Remove ONLY_FULL_GROUP_BY from sql_mode if present
                    if (strpos($sqlMode, 'ONLY_FULL_GROUP_BY') !== false) {
                        $sqlModeArray = explode(',', $sqlMode);
                        $sqlModeArray = array_map('trim', $sqlModeArray);
                        $sqlModeArray = array_filter($sqlModeArray, function ($mode) {
                            return $mode !== 'ONLY_FULL_GROUP_BY';
                        });
                        $newSqlMode = implode(',', $sqlModeArray);

                        // Set new sql_mode without ONLY_FULL_GROUP_BY for current session
                        $db->query("SET SESSION sql_mode = '" . $db->escapeString($newSqlMode) . "'");
                        $sqlModeSet = true;
                    }
                }
            } catch (\Exception $e) {
                // Silently fail if there's an error setting sql_mode
                // This ensures the application can still run even if sql_mode can't be changed
                log_message('debug', 'Failed to set sql_mode: ' . $e->getMessage());
            }
        }
    }
}
