<?php

namespace App\Http\Controllers;

use App\Services\InstallationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstallationController extends Controller
{
    protected InstallationService $installer;

    public function __construct(InstallationService $installer)
    {
        $this->installer = $installer;
    }

    public function index(Request $request)
    {
        $step = $request->query('step', 1);
        
        // Clear session when starting fresh at step 1
        if ($step == 1) {
            $request->session()->forget('install_data');
            $request->session()->forget('install_errors');
            $data = [];
            $errors = [];
        } else {
            $data = $request->session()->get('install_data', []);
            $errors = $request->session()->get('install_errors', []);
        }

        // Auto-check requirements for step 2
        if ($step == 2 && !isset($data['requirements'])) {
            $requirementsResult = $this->installer->checkRequirements();
            $data['requirements'] = $requirementsResult['requirements'];
            $data['requirements_passed'] = $requirementsResult['passed'];
            $request->session()->put('install_data', $data);
        }

        return view('install.wizard', compact('step', 'data', 'errors'));
    }

    public function process(Request $request)
    {
        $step = $request->input('step', 1);
        $data = $request->session()->get('install_data', []);
        $errors = [];

        switch ($step) {
            case 2:
                // Check requirements
                $requirementsResult = $this->installer->checkRequirements();
                $data['requirements'] = $requirementsResult['requirements'];
                $data['requirements_passed'] = $requirementsResult['passed'];
                if (!$data['requirements_passed']) {
                    $errors['requirements'] = 'Some requirements are not met.';
                }
                break;

            case 3:
                // Test root database
                $request->validate([
                    'root_host' => 'nullable|string',
                    'root_port' => 'nullable|integer',
                    'root_user' => 'nullable|string',
                    'root_pass' => 'nullable|string',
                ]);

                if ($request->input('use_root_credentials')) {
                    $result = $this->installer->testDatabaseConnection(
                        $request->input('root_host', 'localhost'),
                        $request->input('root_port', '3306'),
                        '',
                        $request->input('root_user', 'root'),
                        $request->input('root_pass', '')
                    );

                    if (!$result['success']) {
                        $errors['root_db'] = $result['error'];
                    } else {
                        $data['root_db'] = [
                            'host' => $request->input('root_host', 'localhost'),
                            'port' => $request->input('root_port', '3306'),
                            'user' => $request->input('root_user', 'root'),
                            'pass' => $request->input('root_pass', ''),
                        ];
                        $data['use_root_credentials'] = true;
                    }
                }
                break;

            case 4:
                // Test application database
                $request->validate([
                    'db_host' => 'required|string',
                    'db_port' => 'required|integer',
                    'db_name' => 'required|string',
                    'db_user' => 'required|string',
                    'db_pass' => 'nullable|string',
                ]);

                $result = $this->installer->testDatabaseConnection(
                    $request->input('db_host'),
                    $request->input('db_port'),
                    $request->input('db_name'),
                    $request->input('db_user'),
                    $request->input('db_pass') ?: ''
                );

                if (!$result['success']) {
                    $errors['db'] = $result['error'];
                } else {
                    $data['database'] = [
                        'host' => $request->input('db_host'),
                        'port' => $request->input('db_port'),
                        'name' => $request->input('db_name'),
                        'user' => $request->input('db_user'),
                        'pass' => $request->input('db_pass'),
                    ];

                    // Create database if using root credentials
                    if (isset($data['use_root_credentials']) && $data['use_root_credentials']) {
                        $createResult = $this->installer->createDatabase(
                            $data['root_db']['host'],
                            $data['root_db']['port'],
                            $data['root_db']['user'],
                            $data['root_db']['pass'],
                            $data['database']['name'],
                            $data['database']['user'],
                            $data['database']['pass']
                        );

                        if (!$createResult['success']) {
                            $errors['db_create'] = $createResult['error'];
                        }
                    }
                }
                break;

            case 5:
                // Admin account
                $request->validate([
                    'admin_name' => 'required|string',
                    'admin_email' => 'required|email',
                    'admin_password' => 'required|min:8',
                    'admin_password_confirm' => 'required|same:admin_password',
                ]);

                $data['admin'] = [
                    'name' => $request->input('admin_name'),
                    'email' => $request->input('admin_email'),
                    'password' => $request->input('admin_password'),
                ];
                break;

            case 6:
                // App settings
                $request->validate([
                    'app_url' => 'required|url',
                ]);

                $data['app'] = [
                    'url' => $request->input('app_url'),
                ];
                break;

            case 7:
                // Branding
                $request->validate([
                    'company_name' => 'required|string',
                ]);

                $data['branding'] = [
                    'company_name' => $request->input('company_name'),
                    'company_logo' => $request->input('company_logo'),
                    'primary_color' => $request->input('primary_color', '#667eea'),
                    'secondary_color' => $request->input('secondary_color', '#764ba2'),
                ];
                break;

            case 8:
                // Google OAuth
                $request->validate([
                    'google_client_id' => 'required|string',
                    'google_client_secret' => 'required|string',
                ]);

                $data['google_oauth'] = [
                    'client_id' => $request->input('google_client_id'),
                    'client_secret' => $request->input('google_client_secret'),
                ];
                break;

            case 9:
                // WeChat OAuth
                $request->validate([
                    'wechat_app_id' => 'required|string',
                    'wechat_app_secret' => 'required|string',
                ]);

                $data['wechat_oauth'] = [
                    'app_id' => $request->input('wechat_app_id'),
                    'app_secret' => $request->input('wechat_app_secret'),
                ];
                break;

            case 10:
                // Email configuration
                $request->validate([
                    'mail_host' => 'required|string',
                    'mail_port' => 'required|integer',
                    'mail_from_email' => 'required|email',
                ]);

                $data['email'] = [
                    'host' => $request->input('mail_host'),
                    'port' => $request->input('mail_port'),
                    'secure' => $request->input('mail_secure', 'tls'),
                    'from_email' => $request->input('mail_from_email'),
                    'from_name' => $request->input('mail_from_name'),
                    'username' => $request->input('mail_username'),
                    'password' => $request->input('mail_password'),
                ];
                break;

            case 11:
                // Check permissions
                $permissions = $this->installer->checkFilePermissions();
                $data['permissions'] = $permissions;
                $allWritable = collect($permissions)->every('writable');
                if (!$allWritable) {
                    $this->installer->setFilePermissions();
                }
                break;

            case 12:
                // Complete installation
                try {
                    // Write .env file
                    $envData = [
                        'DB_CONNECTION' => 'mysql',
                        'DB_HOST' => $data['database']['host'],
                        'DB_PORT' => $data['database']['port'],
                        'DB_DATABASE' => $data['database']['name'],
                        'DB_USERNAME' => $data['database']['user'],
                        'DB_PASSWORD' => $data['database']['pass'],
                        'APP_URL' => $data['app']['url'],
                        'GOOGLE_CLIENT_ID' => $data['google_oauth']['client_id'],
                        'GOOGLE_CLIENT_SECRET' => $data['google_oauth']['client_secret'],
                        'WECHAT_APP_ID' => $data['wechat_oauth']['app_id'],
                        'WECHAT_APP_SECRET' => $data['wechat_oauth']['app_secret'],
                        'MAIL_HOST' => $data['email']['host'],
                        'MAIL_PORT' => $data['email']['port'],
                        'MAIL_ENCRYPTION' => $data['email']['secure'],
                        'MAIL_FROM_ADDRESS' => $data['email']['from_email'],
                        'MAIL_FROM_NAME' => $data['email']['from_name'],
                        'MAIL_USERNAME' => $data['email']['username'],
                        'MAIL_PASSWORD' => $data['email']['password'],
                        'CACHE_STORE' => 'file',
                        'SESSION_DRIVER' => 'file',
                    ];

                    \Log::info('Starting installation: writing .env file');
                    \Log::info('Session data', ['data' => $data]);
                    $envResult = $this->installer->writeEnvFile($envData);
                    if (!$envResult['success']) {
                        throw new \Exception('Failed to write .env file: ' . ($envResult['error'] ?? 'Unknown error'));
                    }

                    // Reload configuration to pick up new .env settings
                    \Log::info('Reloading configuration');
                    config(['database.default' => 'mysql']);
                    config(['database.connections.mysql.host' => $data['database']['host']]);
                    config(['database.connections.mysql.port' => $data['database']['port']]);
                    config(['database.connections.mysql.database' => $data['database']['name']]);
                    config(['database.connections.mysql.username' => $data['database']['user']]);
                    config(['database.connections.mysql.password' => $data['database']['pass'] ?? '']);
                    DB::purge('mysql');
                    DB::reconnect('mysql');

                    \Log::info('Running migrations');
                    $migrateResult = $this->installer->runMigrations();
                    if (!$migrateResult['success']) {
                        throw new \Exception('Migration failed: ' . ($migrateResult['error'] ?? 'Unknown error'));
                    }

                    \Log::info('Creating admin user');
                    $adminResult = $this->installer->createAdminUser(
                        $data['admin']['email'],
                        $data['admin']['password'],
                        $data['admin']['name']
                    );
                    if (!$adminResult['success']) {
                        throw new \Exception('Admin user creation failed: ' . ($adminResult['error'] ?? 'Unknown error'));
                    }

                    \Log::info('Seeding settings');
                    $settingsData = [
                        'branding' => $data['branding'] ?? [],
                        'app_url' => $data['app']['url'] ?? config('app.url'),
                        'google_client_id' => $data['oauth']['google']['client_id'] ?? '',
                        'google_client_secret' => $data['oauth']['google']['client_secret'] ?? '',
                        'wechat_app_id' => $data['oauth']['wechat']['app_id'] ?? '',
                        'wechat_app_secret' => $data['oauth']['wechat']['app_secret'] ?? '',
                    ];
                    $seedResult = $this->installer->seedSettings($settingsData);
                    if (!$seedResult['success']) {
                        throw new \Exception('Settings seeding failed: ' . ($seedResult['error'] ?? 'Unknown error'));
                    }

                    \Log::info('Marking as installed');
                    $markResult = $this->installer->markInstalled();
                    if (!$markResult['success']) {
                        throw new \Exception('Failed to mark as installed: ' . ($markResult['error'] ?? 'Unknown error'));
                    }

                    // Clear session data
                    $request->session()->forget('install_data');
                    $request->session()->forget('install_errors');

                    \Log::info('Installation completed successfully');
                    return redirect()->route('login')->with('success', 'Installation completed successfully!');
                } catch (\Exception $e) {
                    \Log::error('Installation failed: ' . $e->getMessage(), [
                        'exception' => $e,
                        'trace' => $e->getTraceAsString()
                    ]);
                    $errors['installation'] = 'Installation failed: ' . $e->getMessage();
                }
                break;
        }

        if (!empty($errors)) {
            $request->session()->put('install_errors', $errors);
        } else {
            $request->session()->put('install_data', $data);
            $request->session()->forget('install_errors');
        }

        return redirect()->route('install', ['step' => empty($errors) ? $step + 1 : $step]);
    }
}
