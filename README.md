# Project setup

1. Clone this repository:


    `git clone <git-repository>`

    `cd <repository-folder>`


2. Install dependencies:


    `composer install`

3. Set up environment:

   - Copy `.env.example` file to `.env`


    `cp .env.example .env # example in linux`


4. Generate application key:


    `php artisan key:generate`

5. Configure database:
    
   - Create database `db_si_rt` on your MySQL

    - Configure database connection in `.env` 


       `DB_CONNECTION=mysql`
      
       `DB_HOST=<your-db-host>`
       
       `DB_PORT=<your-db-port>`
       
       `DB_DATABASE=db_si_rt`
      
       `DB_USERNAME=<your-username>`
       
       `DB_PASSWORD=<your-password>`

6. Ensure storage and app URLs are correct


    `APP_URL=http://127.0.0.1:8000`
   
    `FILESYSTEM_DISK=public`

7. Configure your origin:


    `SANCTUM_STATEFUL_DOMAINS="http://localhost:5173" # example in react vite`

    `SESSION_DRIVER=cookie # for SPA authentication`

    `SESSION_DOMAIN=http://localhost:5173`

8. Run database migrations and Database seeds:


    `php artisan migrate --seed`

9. Link storage for image uploads:


    `php artisan storage:link`

10. Clear config cache (optional, for debugging .env issues):


    `php artisan config:clear`

11. Serve the application:


    `php artisan serve`
