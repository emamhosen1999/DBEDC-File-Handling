# DBEDC File Tracker

A modern Laravel-based file tracking system for managing letters, tasks, and documents with departmental workflow automation.

## Features

- **Letter Management**: Track incoming/outgoing letters with reference numbers, stakeholders, and deadlines
- **Task Management**: Create and assign tasks with status tracking and priority levels
- **Department Hierarchy**: Organize users into departments with parent-child relationships
- **User Authentication**: Multi-provider authentication (Google, WeChat, Email) via Laravel Breeze
- **Real-time Notifications**: In-app notifications with push notification support
- **Activity Logging**: Track all user actions for audit purposes
- **PWA Support**: Progressive Web App with offline functionality
- **Modern UI**: Nebula Glass design system with glassmorphism effects
- **API Endpoints**: RESTful API for mobile and third-party integrations
- **Scheduled Tasks**: Automated deadline reminders and database backups

## Tech Stack

- **Backend**: Laravel 13.7, PHP 8.2+
- **Frontend**: Livewire 4.x, Alpine.js, Tailwind CSS
- **Database**: MySQL with ULID primary keys
- **Authentication**: Laravel Breeze with Sanctum API tokens
- **Design**: Nebula Glass (custom glassmorphism design system)
- **PWA**: Service Worker with offline caching

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ and NPM
- MySQL 8.0+

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd dbedc-file-tracker-laravel
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database in `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=dbedc_file_tracker
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

7. **Start development server**
   ```bash
   php artisan serve
   ```

## Legacy Data Migration

To migrate data from the legacy PHP application:

1. **Add legacy database credentials to `.env`**
   ```env
   LEGACY_DB_HOST=localhost
   LEGACY_DB_PORT=3306
   LEGACY_DB_NAME=file_tracker
   LEGACY_DB_USER=root
   LEGACY_DB_PASSWORD=
   ```

2. **Run migration command**
   ```bash
   php artisan app:migrate-legacy-data
   ```

## Scheduled Tasks

The application includes scheduled tasks for:

- **Deadline Reminders**: Daily at 8:00 AM - Sends notifications for tasks/letters due within 3 days
- **Email Queue Processing**: Every 5 minutes - Processes pending email notifications
- **Database Backup**: Daily at 2:00 AM - Creates compressed database backups

To enable scheduled tasks, add the following cron entry:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## API Endpoints

All API endpoints are protected with `auth:sanctum` middleware:

- **Letters**: `/api/letters` (GET, POST, PUT, DELETE)
- **Tasks**: `/api/tasks` (GET, POST, PUT, DELETE)
- **Departments**: `/api/departments` (GET, POST, PUT, DELETE)
- **Users**: `/api/users` (GET, POST, PUT, DELETE)
- **Stakeholders**: `/api/stakeholders` (GET, POST, PUT, DELETE)
- **Settings**: `/api/settings` (GET, POST, PUT, DELETE)
- **Notifications**: `/api/notifications` (GET, POST, PUT, DELETE)
- **Activities**: `/api/activities` (GET)

## File Storage

File uploads are configured in `config/filesystems.php`:

- **Local Storage**: `storage/app/uploads` (private)
- **Public Storage**: `storage/app/public` (public via `/storage`)

To create the storage link:
```bash
php artisan storage:link
```

## Social Authentication

Configure OAuth providers in `config/services.php`:

- **Google**: Set `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET` in `.env`
- **WeChat**: Set `WECHAT_APP_ID` and `WECHAT_APP_SECRET` in `.env`

## Development

### Running Tests

```bash
php artisan test
```

### Code Style

The project follows Laravel coding standards. Run:

```bash
php artisan pint
```

## Deployment

### Shared Hosting

1. Upload all files to the server
2. Set `APP_ENV=production` in `.env`
3. Run `php artisan optimize`
4. Run `php artisan config:cache`
5. Run `php artisan route:cache`
6. Ensure storage directory is writable
7. Configure cron job for scheduled tasks

### Environment Variables for Production

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_HOST=your-db-host
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password

# OAuth
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
WECHAT_APP_ID=your-wechat-app-id
WECHAT_APP_SECRET=your-wechat-app-secret
```

## Project Structure

```
app/
├── Console/Commands/      # Artisan commands (scheduler, migration)
├── Http/Controllers/
│   ├── Api/              # API controllers
│   └── Auth/             # Authentication controllers
├── Http/Resources/       # API resources
├── Models/               # Eloquent models
├── Services/             # Business logic services
└── Traits/               # Reusable traits (HasUlid)

resources/
├── css/
│   ├── app.css           # Main stylesheet
│   ├── nebula.css        # Nebula Glass effects
│   └── tokens.css        # Design tokens
├── views/
│   ├── components/       # Livewire components
│   └── layouts/          # Blade layouts
└── js/                   # JavaScript files

public/
├── sw.js                 # Service Worker
└── manifest.json         # PWA manifest
```

## License

This project is proprietary software. All rights reserved.

## Support

For support and questions, please contact the development team.
