<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class InstallApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'application:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will install the application & set up DB';

    /**
     * Create a new command instance.
     *
     * @return void|mixed
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line("You can use Ctrl+C to exit the installer any time.\n");
        $this->createDatabase();
        $this->setupSuperAdmin();
        $this->migrate();
        $this->seed();
        $this->setupResetPasswordEmail();
        $this->setUpKey();
        $this->runApp();
    }

    /**
     * This method creates the database by taking inputs from the user.
     *
     * @return void
     */
    private function createDatabase()
    {
        if (!$this->testDbConnection()) {
            // return;
        }

        $this->line("You need to choose a database type.");

        install_database:


        $connection = 'mysql';
        $host = null;
        $port = null;
        $database = null;
        $username = null;
        $password = null;

        $available_connections = array_keys(config('database.connections'));
        $connection = $this->choice('Choose a connection type', $available_connections);

        $defaultPort =  3306;
        $host = $this->ask('Database host', 'localhost');
        $port = $this->ask('Database port', $defaultPort);
        $database = $this->ask('Database name', "projects_archive_api");
        $username = $this->ask('Database username', 'project_archive_api');
        $password = $this->secret('Database password', '');
        $database2 = $this->ask('Teachers database Name');


        $settings = compact('connection', 'host', 'port', 'database', 'username', 'password', 'database2');
        $this->updateEnvironmentFile($settings);

        if (!$this->testDbConnection()) {
            $this->error('Could not connect to database.');
            goto install_database;
        }
    }

    /**
     * This method is to test the DB connection.
     *
     * @return boolean
     */
    private function testDbConnection()
    {
        $this->line('Checking DB connection.');

        try {
            DB::connection(DB::getDefaultConnection())->reconnect();
        } catch (\Exception $e) {
            return false;
        }

        $this->info('Database connection working.');
        return true;
    }

    /**
     * Updates the environment file with the given database settings.
     *
     * @param  string  $settings
     * @return void
     */
    private function updateEnvironmentFile($settings)
    {
        $env_path = base_path('.env');
        DB::purge(DB::getDefaultConnection());

        foreach ($settings as $key => $value) {
            $key = 'DB_' . strtoupper($key);
            $line = $value ? ($key . '=' . $value) : $key;
            putenv($line);
            file_put_contents($env_path, preg_replace(
                '/^' . $key . '.*/m',
                $line,
                file_get_contents($env_path)
            ));
        }

        config()->offsetSet("database", include(config_path('database.php')));
    }

    /**
     * Migrate the Database.
     *
     * @return void
     */
    private function migrate()
    {
        $this->line("\nStarting DB Migration...");
        $this->call('migrate');
    }

    /**
     * Seeds the Database.
     *
     * @return void
     */
    private function seed()
    {
        $this->line("\nStarting DB Seeding...");
        $this->call('db:seed');
    }

    /**
     * Sets up the application key.
     *
     * @return void
     */
    private function setUpKey()
    {
        $this->call('key:generate');
        $this->info("\nApplication installation completed!");
    }

    private function setupResetPasswordEmail()
    {
        $env_path = base_path('.env');
        $password = $this->ask('Enter the secret key of this email nahrainunivpas@gmail.com');
        $key = 'MAIL_PASSWORD';
        $line = $password ? ($key . '=' . $password) : $key;
        putenv($line);
        file_put_contents($env_path, preg_replace(
            '/^' . $key . '.*/m',
            $line,
            file_get_contents($env_path)
        ));
    }


    private function setupSuperAdmin()
    {
        $user_factory_path = base_path('database/factories/UserFactory.php');
        $super_email = $this->ask('Enter super Admin email', 'amjed.altaweel.88@gmail.com');
        $super_password = $this->ask('Enter super Admin password', 'computerAdmin12345');
        $password_line = 'password' . '=>' . "Hash::make('" . $super_password . "'),";
        $email_line = 'email' . '=>' . $super_email . ',';
        file_put_contents($user_factory_path, preg_replace(
            '/^' . 'password' . '.*/m',
            $password_line,
            file_get_contents($user_factory_path)
        ));
        file_put_contents($user_factory_path, preg_replace(
            '/^' . 'email' . '.*/m',
            $email_line,
            file_get_contents($user_factory_path)
        ));
    }



    private function runApp()
    {
        $ip = $this->ask('computerIp', '192.168.0.103');
        $port = $this->ask('Port', '8000');

        $this->call('serve --host=' . $ip . ' --port=' . $port);
        $this->info("\nApplication is running on http://" . $ip . ":" . $port);
    }
}
