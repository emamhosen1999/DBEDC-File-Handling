<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PDO;
use PDOException;

class InstallationService
{
    private array $errors = [];
    private array $warnings = [];

    public function checkRequirements(): array
    {
        $requirements = [
            'php_version' => [
                'name' => 'PHP Version',
                'required' => '8.1',
                'current' => PHP_VERSION,
                'status' => version_compare(PHP_VERSION, '8.1', '>='),
            ],
            'pdo' => [
                'name' => 'PDO Extension',
                'required' => true,
                'current' => extension_loaded('pdo'),
                'status' => extension_loaded('pdo'),
            ],
            'pdo_mysql' => [
                'name' => 'PDO MySQL Extension',
                'required' => true,
                'current' => extension_loaded('pdo_mysql'),
                'status' => extension_loaded('pdo_mysql'),
            ],
            'mbstring' => [
                'name' => 'Mbstring Extension',
                'required' => true,
                'current' => extension_loaded('mbstring'),
                'status' => extension_loaded('mbstring'),
            ],
            'json' => [
                'name' => 'JSON Extension',
                'required' => true,
                'current' => extension_loaded('json'),
                'status' => extension_loaded('json'),
            ],
            'openssl' => [
                'name' => 'OpenSSL Extension',
                'required' => true,
                'current' => extension_loaded('openssl'),
                'status' => extension_loaded('openssl'),
            ],
            'ctype' => [
                'name' => 'Ctype Extension',
                'required' => true,
                'current' => extension_loaded('ctype'),
                'status' => extension_loaded('ctype'),
            ],
            'bcmath' => [
                'name' => 'BCMath Extension',
                'required' => true,
                'current' => extension_loaded('bcmath'),
                'status' => extension_loaded('bcmath'),
            ],
            'curl' => [
                'name' => 'cURL Extension',
                'required' => true,
                'current' => extension_loaded('curl'),
                'status' => extension_loaded('curl'),
            ],
            'fileinfo' => [
                'name' => 'Fileinfo Extension',
                'required' => true,
                'current' => extension_loaded('fileinfo'),
                'status' => extension_loaded('fileinfo'),
            ],
            'tokenizer' => [
                'name' => 'Tokenizer Extension',
                'required' => true,
                'current' => extension_loaded('tokenizer'),
                'status' => extension_loaded('tokenizer'),
            ],
            'xml' => [
                'name' => 'XML Extension',
                'required' => true,
                'current' => extension_loaded('xml'),
                'status' => extension_loaded('xml'),
            ],
            'gd' => [
                'name' => 'GD Extension (for image processing)',
                'required' => true,
                'current' => extension_loaded('gd'),
                'status' => extension_loaded('gd'),
            ],
            'zip' => [
                'name' => 'Zip Extension',
                'required' => true,
                'current' => extension_loaded('zip'),
                'status' => extension_loaded('zip'),
            ],
        ];

        $allPassed = true;
        foreach ($requirements as $key => $req) {
            if (!$req['status']) {
                $allPassed = false;
                $this->errors[] = $req['name'] . ' is required but not available.';
            }
        }

        return [
            'passed' => $allPassed,
            'requirements' => $requirements,
        ];
    }

    public function testDatabaseConnection(string $host, string $port, string $name, string $user, ?string $pass): array
    {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;charset=utf8mb4',
                $host,
                $port
            );

            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            // Test if database exists
            $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$name'");
            $exists = $stmt->fetch();

            return [
                'success' => true,
                'database_exists' => $exists !== false,
            ];
        } catch (PDOException $e) {
            $this->errors[] = 'Database connection failed: ' . $e->getMessage();
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function createDatabase($rootHost, $rootPort, $rootUser, $rootPass, $dbName, $dbUser, $dbPass)
    {
        try {
            \Log::info("Creating database: host=$rootHost, port=$rootPort, user=$rootUser, dbName=$dbName");
            
            $dsn = sprintf(
                'mysql:host=%s;port=%s;charset=utf8mb4',
                $rootHost,
                $rootPort
            );

            $pdo = new PDO($dsn, $rootUser, $rootPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            // Drop database if exists to ensure clean installation
            \Log::info("Dropping database if exists: $dbName");
            $pdo->exec("DROP DATABASE IF EXISTS `$dbName`");

            // Create database
            \Log::info("Creating database: $dbName");
            $pdo->exec("CREATE DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // Create user if doesn't exist
            $pdo->exec("CREATE USER IF NOT EXISTS '$dbUser'@'$rootHost' IDENTIFIED BY '$dbPass'");

            // Grant privileges
            $pdo->exec("GRANT ALL PRIVILEGES ON `$dbName`.* TO '$dbUser'@'$rootHost'");
            $pdo->exec("FLUSH PRIVILEGES");

            \Log::info("Database created successfully");
            return ['success' => true];
        } catch (PDOException $e) {
            \Log::error("Failed to create database: " . $e->getMessage());
            $this->errors[] = 'Failed to create database: ' . $e->getMessage();
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function runMigrations($rootHost = null, $rootPort = null, $rootUser = null, $rootPass = null, $dbName = null)
    {
        try {
            // Drop and recreate database using root credentials if provided
            if ($rootHost && $rootUser && $dbName) {
                $pdo = new PDO(
                    sprintf('mysql:host=%s;port=%s;charset=utf8mb4', $rootHost, $rootPort),
                    $rootUser,
                    $rootPass ?? '',
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );

                // Drop database if exists
                $pdo->exec("DROP DATABASE IF EXISTS `$dbName`");
                
                // Create database
                $pdo->exec("CREATE DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            }

            // Now run migrations
            $exitCode = Artisan::call('migrate', ['--force' => true]);

            if ($exitCode !== 0) {
                $output = Artisan::output();
                throw new \Exception("Migration failed with exit code $exitCode: $output");
            }

            return ['success' => true];
        } catch (\Exception $e) {
            $this->errors[] = 'Failed to run migrations: ' . $e->getMessage();
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function createAdminUser(string $email, string $password, string $name): array
    {
        try {
            // Check if admin user already exists
            $existingUser = DB::table('users')->where('email', $email)->first();
            if ($existingUser) {
                // Update existing user
                DB::table('users')->where('email', $email)->update([
                    'name' => $name,
                    'password' => bcrypt($password),
                    'role' => 'ADMIN',
                    'is_active' => true,
                    'updated_at' => now(),
                ]);
                return ['success' => true];
            }

            // Create new admin user
            $passwordHash = bcrypt($password);
            $ulid = \Symfony\Component\Uid\Ulid::generate();

            DB::table('users')->insert([
                'id' => $ulid,
                'google_id' => 'admin-' . $ulid,
                'email' => $email,
                'password' => $passwordHash,
                'name' => $name,
                'role' => 'ADMIN',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return ['success' => true];
        } catch (\Exception $e) {
            $this->errors[] = 'Failed to create admin user: ' . $e->getMessage();
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function writeEnvFile(array $data): array
    {
        try {
            $envPath = base_path('.env');
            $envExamplePath = base_path('.env.example');

            // Start with .env.example if it exists, otherwise create new
            if (File::exists($envExamplePath)) {
                $envContent = File::get($envExamplePath);
            } else {
                $envContent = '';
            }

            // Generate APP_KEY if not present
            if (!preg_match('/^APP_KEY=/m', $envContent)) {
                $appKey = 'base64:' . base64_encode(random_bytes(32));
                $envContent .= "\nAPP_KEY={$appKey}";
            }

            // Ensure SESSION_DRIVER is set to file for installation
            if (!preg_match('/^SESSION_DRIVER=/m', $envContent)) {
                $envContent .= "\nSESSION_DRIVER=file";
            } else {
                $envContent = preg_replace('/^SESSION_DRIVER=.*/m', 'SESSION_DRIVER=file', $envContent);
            }

            // Replace or add each key
            foreach ($data as $key => $value) {
                $key = strtoupper($key);
                $pattern = "/^{$key}=.*/m";
                $replacement = "{$key}={$value}";

                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                } elseif (is_string($value) && (str_contains($value, ' ') || str_contains($value, '='))) {
                    $value = '"' . $value . '"';
                }

                if (preg_match($pattern, $envContent)) {
                    $envContent = preg_replace($pattern, "{$key}={$value}", $envContent);
                } else {
                    $envContent .= "\n{$key}={$value}";
                }
            }

            // Remove installation flag if it exists (for clean re-installation)
            if (preg_match('/^INSTALLED=/m', $envContent)) {
                $envContent = preg_replace('/^INSTALLED=.*/m', '', $envContent);
            }

            File::put($envPath, $envContent);
            chmod($envPath, 0600);

            return ['success' => true];
        } catch (\Exception $e) {
            $this->errors[] = 'Failed to write .env file: ' . $e->getMessage();
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function seedSettings(array $settingsData): array
    {
        try {
            // Seed branding settings
            $brandingSettings = [
                ['company_name', $settingsData['company_name'] ?? 'DBEDC File Tracker', 'branding', 'string', true, 'Company name displayed in the application'],
                ['company_logo', $settingsData['company_logo'] ?? '', 'branding', 'string', true, 'Company logo URL'],
                ['primary_color', $settingsData['primary_color'] ?? '#667eea', 'branding', 'string', true, 'Primary theme color'],
                ['secondary_color', $settingsData['secondary_color'] ?? '#764ba2', 'branding', 'string', true, 'Secondary theme color'],
            ];

            // Seed OAuth settings
            $oauthSettings = [
                ['google_client_id', $settingsData['google_client_id'] ?? '', 'auth', 'string', false, 'Google OAuth Client ID'],
                ['google_client_secret', $settingsData['google_client_secret'] ?? '', 'auth', 'string', false, 'Google OAuth Client Secret'],
                ['google_redirect_uri', $settingsData['app_url'] . '/auth/google/callback', 'auth', 'string', false, 'Google OAuth Redirect URI'],
                ['wechat_app_id', $settingsData['wechat_app_id'] ?? '', 'auth', 'string', false, 'WeChat OAuth App ID'],
                ['wechat_app_secret', $settingsData['wechat_app_secret'] ?? '', 'auth', 'string', false, 'WeChat OAuth App Secret'],
                ['wechat_redirect_uri', $settingsData['app_url'] . '/auth/wechat/callback', 'auth', 'string', false, 'WeChat OAuth Redirect URI'],
            ];

            // Seed email settings
            $emailSettings = [
                ['mail_host', $settingsData['mail_host'] ?? 'smtp.gmail.com', 'email', 'string', false, 'SMTP host'],
                ['mail_port', $settingsData['mail_port'] ?? 587, 'email', 'integer', false, 'SMTP port'],
                ['mail_secure', $settingsData['mail_secure'] ?? 'tls', 'email', 'string', false, 'SMTP secure protocol'],
                ['mail_from_email', $settingsData['mail_from_email'] ?? 'noreply@dhakabypass.com', 'email', 'string', false, 'From email address'],
                ['mail_from_name', $settingsData['mail_from_name'] ?? 'DBEDC File Tracker', 'email', 'string', false, 'From name'],
                ['mail_username', $settingsData['mail_username'] ?? '', 'email', 'string', false, 'SMTP username'],
                ['mail_password', $settingsData['mail_password'] ?? '', 'email', 'string', false, 'SMTP password'],
            ];

            // Seed workflow settings
            $workflowSettings = [
                ['escalation_days', 3, 'workflow', 'integer', false, 'Days before task escalation'],
                ['reminder_days', 2, 'workflow', 'integer', false, 'Days before deadline reminder'],
                ['review_months', 6, 'workflow', 'integer', false, 'Months before file review'],
            ];

            // Insert all settings
            $allSettings = array_merge($brandingSettings, $oauthSettings, $emailSettings, $workflowSettings);

            foreach ($allSettings as $setting) {
                DB::table('settings')->updateOrInsert(
                    ['setting_key' => $setting[0]],
                    [
                        'id' => \Symfony\Component\Uid\Ulid::generate(),
                        'setting_value' => $setting[1],
                        'setting_group' => $setting[2],
                        'data_type' => $setting[3],
                        'is_public' => $setting[4],
                        'description' => $setting[5],
                        'updated_at' => now(),
                    ]
                );
            }

            return ['success' => true];
        } catch (\Exception $e) {
            $this->errors[] = 'Failed to seed settings: ' . $e->getMessage();
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function checkFilePermissions(): array
    {
        $paths = [
            '.env' => base_path('.env'),
            'storage' => base_path('storage'),
            'storage/logs' => base_path('storage/logs'),
            'storage/cache' => base_path('storage/cache'),
            'storage/uploads' => base_path('storage/uploads'),
        ];

        $results = [];
        foreach ($paths as $name => $path) {
            $exists = File::exists($path);
            $writable = File::isWritable($path) || (!$exists && File::isWritable(dirname($path)));
            $results[$name] = [
                'path' => $path,
                'writable' => $writable,
                'exists' => $exists,
            ];
        }

        return $results;
    }

    public function setFilePermissions(): array
    {
        try {
            $success = true;

            $permissions = [
                base_path('storage') => 0755,
                base_path('storage/logs') => 0755,
                base_path('storage/cache') => 0755,
                base_path('storage/uploads') => 0755,
                base_path('storage/framework') => 0755,
                base_path('bootstrap/cache') => 0755,
            ];

            foreach ($permissions as $path => $perm) {
                if (!File::exists($path)) {
                    File::makeDirectory($path, $perm, true);
                }
                if (!chmod($path, $perm)) {
                    $this->warnings[] = "Could not set permissions for $path";
                    $success = false;
                }
            }

            return ['success' => $success];
        } catch (\Exception $e) {
            $this->errors[] = 'Failed to set file permissions: ' . $e->getMessage();
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function markInstalled(): array
    {
        try {
            $lockFile = storage_path('installed.lock');
            File::put($lockFile, date('Y-m-d H:i:s'));

            // Also add to .env
            $envPath = base_path('.env');
            $envContent = File::get($envPath);
            if (!str_contains($envContent, 'INSTALLED=')) {
                $envContent .= "\nINSTALLED=true";
                File::put($envPath, $envContent);
            }

            return ['success' => true];
        } catch (\Exception $e) {
            $this->errors[] = 'Failed to mark as installed: ' . $e->getMessage();
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function isInstalled(): bool
    {
        $lockFile = storage_path('installed.lock');
        if (File::exists($lockFile)) {
            return true;
        }

        $envPath = base_path('.env');
        if (File::exists($envPath)) {
            $envContent = File::get($envPath);
            return str_contains($envContent, 'INSTALLED=true');
        }

        return false;
    }

    public function generateAppKey(): array
    {
        try {
            Artisan::call('key:generate', ['--force' => true]);
            return ['success' => true];
        } catch (\Exception $e) {
            $this->errors[] = 'Failed to generate app key: ' . $e->getMessage();
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }

    public function clearErrors(): void
    {
        $this->errors = [];
        $this->warnings = [];
    }
}
