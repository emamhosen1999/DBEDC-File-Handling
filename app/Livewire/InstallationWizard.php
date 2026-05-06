<?php

namespace App\Livewire;

use App\Services\InstallationService;
use Livewire\Component;
use Livewire\Attributes\Session;
use Livewire\Attributes\Layout;

#[Layout('layouts.install')]
class InstallationWizard extends Component
{
    public int $currentStep = 1;

    #[Session]
    public array $formData = [];

    public array $installationErrors = [];
    public array $requirements = [];
    public array $permissions = [];
    public array $installationProgress = [];

    public bool $useRootCredentials = false;

    // Step 2: Requirements
    public bool $requirementsPassed = false;

    // Step 3: Root DB
    public string $rootHost = 'localhost';
    public string $rootPort = '3306';
    public string $rootUser = 'root';
    public string $rootPass = '';
    public bool $rootTestSuccess = false;
    public string $rootTestError = '';

    // Step 4: App DB
    public string $dbHost = 'localhost';
    public string $dbPort = '3306';
    public string $dbName = 'dbedc_file_handling';
    public string $dbUser = '';
    public string $dbPass = '';
    public bool $dbTestSuccess = false;
    public string $dbTestError = '';

    // Step 5: Admin
    public string $adminName = '';
    public string $adminEmail = '';
    public string $adminPassword = '';
    public string $adminPasswordConfirm = '';

    // Step 6: App Settings
    public string $appUrl = '';

    // Step 7: Branding
    public string $companyName = 'DBEDC File Tracker';
    public string $companyLogo = '';
    public string $primaryColor = '#667eea';
    public string $secondaryColor = '#764ba2';

    // Step 8: Google OAuth
    public string $googleClientId = '';
    public string $googleClientSecret = '';

    // Step 9: WeChat OAuth
    public string $wechatAppId = '';
    public string $wechatAppSecret = '';

    // Step 10: Email
    public string $mailHost = 'smtp.gmail.com';
    public string $mailPort = '587';
    public string $mailSecure = 'tls';
    public string $mailFromEmail = 'noreply@dhakabypass.com';
    public string $mailFromName = 'DBEDC File Tracker';
    public string $mailUsername = '';
    public string $mailPassword = '';

    // Step 12: Installation
    public bool $installationComplete = false;
    public bool $installationFailed = false;
    public string $installationError = '';

    protected InstallationService $installer;

    public function boot(InstallationService $installer)
    {
        $this->installer = $installer;
        $this->checkRequirements();
        $this->appUrl = request()->getSchemeAndHttpHost();
    }

    public function render()
    {
        return view('install.wizard');
    }

    public function nextStep()
    {
        if ($this->currentStep < 12) {
            // Auto-check permissions when moving to step 11
            if ($this->currentStep == 10) {
                $this->checkPermissions();
            }
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step)
    {
        if ($step >= 1 && $step <= 12) {
            $this->currentStep = $step;
        }
    }

    // Step 2: Check Requirements
    public function checkRequirements()
    {
        $result = $this->installer->checkRequirements();
        $this->requirements = $result['requirements'];
        $this->requirementsPassed = $result['passed'];
    }

    // Step 3: Test Root Database
    public function testRootDatabase()
    {
        $this->rootTestError = '';
        try {
            $result = $this->installer->testDatabaseConnection(
                $this->rootHost,
                $this->rootPort,
                'mysql',
                $this->rootUser,
                $this->rootPass
            );

            if ($result['success']) {
                $this->rootTestSuccess = true;
                $this->formData['root'] = [
                    'host' => $this->rootHost,
                    'port' => $this->rootPort,
                    'user' => $this->rootUser,
                    'pass' => $this->rootPass,
                ];
                $this->nextStep();
            } else {
                $this->rootTestSuccess = false;
                $this->rootTestError = $result['error'] ?? 'Connection failed';
            }
        } catch (\Exception $e) {
            $this->rootTestSuccess = false;
            $this->rootTestError = $e->getMessage();
        }
    }

    // Step 4: Test App Database
    public function testAppDatabase()
    {
        $this->dbTestError = '';
        try {
            $result = $this->installer->testDatabaseConnection(
                $this->dbHost,
                $this->dbPort,
                $this->dbName,
                $this->dbUser,
                $this->dbPass
            );

            if ($result['success']) {
                $this->dbTestSuccess = true;
                $this->formData['database'] = [
                    'host' => $this->dbHost,
                    'port' => $this->dbPort,
                    'name' => $this->dbName,
                    'user' => $this->dbUser,
                    'pass' => $this->dbPass,
                ];
                $this->nextStep();
            } else {
                $this->dbTestSuccess = false;
                $this->dbTestError = $result['error'] ?? 'Connection failed';
            }
        } catch (\Exception $e) {
            $this->dbTestSuccess = false;
            $this->dbTestError = $e->getMessage();
        }
    }

    // Step 5: Save Admin
    public function saveAdmin()
    {
        $this->validate([
            'adminName' => 'required|string',
            'adminEmail' => 'required|email',
            'adminPassword' => 'required|min:8',
            'adminPasswordConfirm' => 'required|same:adminPassword',
        ]);

        $this->formData['admin'] = [
            'name' => $this->adminName,
            'email' => $this->adminEmail,
            'password' => $this->adminPassword,
        ];
        $this->nextStep();
    }

    // Step 6: Save App Settings
    public function saveAppSettings()
    {
        $this->validate([
            'appUrl' => 'required|url',
        ]);

        $this->formData['app'] = [
            'url' => $this->appUrl,
        ];
        $this->nextStep();
    }

    // Step 7: Save Branding
    public function saveBranding()
    {
        $this->validate([
            'companyName' => 'required|string',
        ]);

        $this->formData['branding'] = [
            'company_name' => $this->companyName,
            'company_logo' => $this->companyLogo,
            'primary_color' => $this->primaryColor,
            'secondary_color' => $this->secondaryColor,
        ];
        $this->nextStep();
    }

    // Step 8: Save Google OAuth
    public function saveGoogleOAuth()
    {
        $this->validate([
            'googleClientId' => 'required|string',
            'googleClientSecret' => 'required|string',
        ]);

        $this->formData['google'] = [
            'client_id' => $this->googleClientId,
            'client_secret' => $this->googleClientSecret,
        ];
        $this->nextStep();
    }

    // Step 9: Save WeChat OAuth
    public function saveWeChatOAuth()
    {
        $this->validate([
            'wechatAppId' => 'required|string',
            'wechatAppSecret' => 'required|string',
        ]);

        $this->formData['wechat'] = [
            'app_id' => $this->wechatAppId,
            'app_secret' => $this->wechatAppSecret,
        ];
        $this->nextStep();
    }

    // Step 10: Save Email
    public function saveEmail()
    {
        $this->validate([
            'mailHost' => 'required|string',
            'mailPort' => 'required|integer',
            'mailFromEmail' => 'required|email',
        ]);

        $this->formData['email'] = [
            'host' => $this->mailHost,
            'port' => $this->mailPort,
            'secure' => $this->mailSecure,
            'from_email' => $this->mailFromEmail,
            'from_name' => $this->mailFromName,
            'username' => $this->mailUsername,
            'password' => $this->mailPassword,
        ];
        $this->nextStep();
    }

    // Step 11: Check Permissions
    public function checkPermissions()
    {
        $this->permissions = $this->installer->checkFilePermissions();
        $allWritable = collect($this->permissions)->every('writable');

        if (!$allWritable) {
            $this->installer->setFilePermissions();
            $this->permissions = $this->installer->checkFilePermissions();
        }

        $this->nextStep();
    }

    // Step 12: Complete Installation
    public function completeInstallation()
    {
        $this->installationProgress = [];
        $this->installationFailed = false;
        $this->installationError = '';

        try {
            // 1. Create database if root credentials provided
            if (isset($this->formData['root'])) {
                $this->installationProgress[] = ['step' => 'Creating database...', 'status' => 'in_progress'];
                $root = $this->formData['root'];
                $db = $this->formData['database'];

                $result = $this->installer->createDatabase(
                    $root['host'],
                    $root['port'],
                    $root['user'],
                    $root['pass'],
                    $db['name'],
                    $db['user'],
                    $db['pass']
                );

                if ($result['success']) {
                    $this->installationProgress[] = ['step' => 'Creating database...', 'status' => 'completed'];
                } else {
                    throw new \Exception('Failed to create database: ' . $result['error']);
                }
            }

            // 2. Write .env file
            $this->installationProgress[] = ['step' => 'Writing .env file...', 'status' => 'in_progress'];
            $db = $this->formData['database'];
            $app = $this->formData['app'];
            $google = $this->formData['google'];
            $wechat = $this->formData['wechat'];
            $email = $this->formData['email'];

            $envData = [
                'app_name' => $this->companyName,
                'app_env' => 'production',
                'app_key' => 'base64:' . base64_encode(random_bytes(32)),
                'app_debug' => false,
                'app_url' => $app['url'],
                'db_connection' => 'mysql',
                'db_host' => $db['host'],
                'db_port' => $db['port'],
                'db_database' => $db['name'],
                'db_username' => $db['user'],
                'db_password' => $db['pass'],
                'google_client_id' => $google['client_id'],
                'google_client_secret' => $google['client_secret'],
                'google_redirect_uri' => $app['url'] . '/auth/google/callback',
                'wechat_app_id' => $wechat['app_id'],
                'wechat_app_secret' => $wechat['app_secret'],
                'wechat_redirect_uri' => $app['url'] . '/auth/wechat/callback',
                'mail_mailer' => 'smtp',
                'mail_host' => $email['host'],
                'mail_port' => $email['port'],
                'mail_encryption' => $email['secure'],
                'mail_username' => $email['username'],
                'mail_password' => $email['password'],
                'mail_from_address' => $email['from_email'],
                'mail_from_name' => $email['from_name'],
            ];

            $result = $this->installer->writeEnvFile($envData);
            if ($result['success']) {
                $this->installationProgress[] = ['step' => 'Writing .env file...', 'status' => 'completed'];
            } else {
                throw new \Exception('Failed to write .env file: ' . $result['error']);
            }

            // 3. Run migrations
            $this->installationProgress[] = ['step' => 'Running database migrations...', 'status' => 'in_progress'];
            $result = $this->installer->runMigrations();
            if ($result['success']) {
                $this->installationProgress[] = ['step' => 'Running database migrations...', 'status' => 'completed'];
            } else {
                throw new \Exception('Failed to run migrations: ' . $result['error']);
            }

            // 4. Create admin user
            $this->installationProgress[] = ['step' => 'Creating admin user...', 'status' => 'in_progress'];
            $admin = $this->formData['admin'];
            $result = $this->installer->createAdminUser($admin['email'], $admin['password'], $admin['name']);
            if ($result['success']) {
                $this->installationProgress[] = ['step' => 'Creating admin user...', 'status' => 'completed'];
            } else {
                throw new \Exception('Failed to create admin user: ' . $result['error']);
            }

            // 5. Seed settings
            $this->installationProgress[] = ['step' => 'Seeding application settings...', 'status' => 'in_progress'];
            $branding = $this->formData['branding'];
            $settingsData = array_merge($branding, $google, $wechat, $email, $app);
            $result = $this->installer->seedSettings($settingsData);
            if ($result['success']) {
                $this->installationProgress[] = ['step' => 'Seeding application settings...', 'status' => 'completed'];
            } else {
                throw new \Exception('Failed to seed settings: ' . $result['error']);
            }

            // 6. Set file permissions
            $this->installationProgress[] = ['step' => 'Setting file permissions...', 'status' => 'in_progress'];
            $result = $this->installer->setFilePermissions();
            if ($result['success']) {
                $this->installationProgress[] = ['step' => 'Setting file permissions...', 'status' => 'completed'];
            } else {
                $this->installationProgress[] = ['step' => 'Setting file permissions...', 'status' => 'warning'];
            }

            // 7. Mark as installed
            $this->installationProgress[] = ['step' => 'Finalizing installation...', 'status' => 'in_progress'];
            $result = $this->installer->markInstalled();
            if ($result['success']) {
                $this->installationProgress[] = ['step' => 'Finalizing installation...', 'status' => 'completed'];
            } else {
                throw new \Exception('Failed to mark as installed: ' . $result['error']);
            }

            $this->installationComplete = true;
            $this->currentStep = 12;

            // Clear session data
            $this->formData = [];
            session()->forget('formData');

        } catch (\Exception $e) {
            $this->installationFailed = true;
            $this->installationError = $e->getMessage();
        }
    }
}
