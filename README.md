## Composer 
run `composer install`

## Package
run `npm install`

## .Env
copy from `.env.example` and create `.env` file
- ** Change Database connection
- ** Change QUEUE_CONNECTION if you want to use redis or database. 
        => For database run commands below - 
            `php artisan make:queue-table`
            `php artisan migrate`
        => For redis please setup redis on your server and put it on .env redis configuration
- ** Change mail smtp configuration. If you don't want in develop mode then change `MAIL_MAILER` to `log` instead of `smtp`
- ** Change all other configuration like APP_URL, FRONTEND_URL, FILESYSTEM_DISK etc.
- ** run `php artisan key:generate`
- ** run `php artisan o:c`

## Run Application
Run this command in your terminal
- ** run `php artisan migrate`
- ** run `php artisan serve`
- ** run `php artisan queue:listen` -> For database or redis queue
- ** run `npm run dev` - for development only
- ** run `npm run build` then run `npm run start` for production only



## Code Structure
We are following laravel default code structure like Facade, Singleton etc including solid principle.
- ** The application bootstrapping from `bootstrap/app.php` where all middleware and routes are initialized and all exceptions are handled here.
- ** The application configured from `config` folder located in root directory. Here we set all default configuration of the application.
- ** The application Service Provider managed from `bootstrap/providers.php` folder


## Authenticate process
- ** When User Registered get an OTP mail to verify his/her account. Without verification User can't perform any action. They can find their otp to provided email address or in develop mode if you are using MAIL_MAILER=log in .env file then you will find your email in `storage/logs/mail.log` file.
- ** When User Logged then if not verified then go to verification page for account verification.
- ** To reset password User will get an otp mail similarlly as account verification otp mail Then provide OTP and other info's user can change their password.
