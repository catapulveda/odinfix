<?php

namespace App\Jobs;

use App\Domain;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class CreateNginxConf extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $domain;
    protected $restart;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Domain $domain, $restart = true)
    {
        $this->domain = $domain;
        $this->restart = $restart;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $domain = $this->domain;

        try {
            $conf = Storage::get('nginx.conf');

            $conf = str_replace('%path%', $this->domain->getDirectory(), $conf);
            $conf = str_replace('%domain%', '.' . $this->domain->domain, $conf);
            $conf = str_replace('%domain_sock%', $this->domain->domain, $conf);

            $conf_path = env('NGINX_PATH') . $this->domain->domain;

            /*
            echo "\n\nNGINX\n\n";
            echo $conf;
            echo "\n\n----------------\n\n";
            */

            file_put_contents($conf_path, $conf);

            $user_group = $this->domain->domain;
            if(strlen($user_group) > 30)
            {
                $user_group = substr($user_group, 0, 25);
            }

            $commands = [
                "groupadd " . $user_group,
                "useradd -g " . $user_group . " " . $user_group
            ];

            foreach ($commands as $command)
            {
                exec($command);
            }

            $path = '/etc/php/7.0/fpm/pool.d/' . $this->domain->domain . '.conf';
            $php_fpm = Storage::get('php-fpm.conf');
            $php_fpm = str_replace('%domain%', $this->domain->domain, $php_fpm);
            $php_fpm = str_replace('%user_group%', $user_group, $php_fpm);


            /*
            echo "\n\nPHP-FPM\n\n";
            echo $php_fpm;
            echo "\n\n----------------\n\n";
            */

            file_put_contents($path, $php_fpm);
            //unlink($path);

            $domain->cpanel_status = 1;

            $command = 'chmod -R 755 ' . $domain->getDirectory();
            exec($command, $out);

            $command = 'chown -R ' . $user_group . ':' . $user_group . ' ' . $domain->getDirectory();
            exec($command, $out);

            $command = 'chown -R ' . $user_group . ':' . $user_group . ' ' . $domain->getDirectory() . '*';
            exec($command, $out);

            //$command = 'chmod -R 700 ' . $domain->getDirectory() . 'wp-config.php';
            //exec($command, $out);

            /*
            $command = 'chmod -R 755 ' . $domain->getDirectory();
            exec($command, $out);

            $command = 'chown ' . $domain->domain . ':' . $domain->domain . ' ' . $domain->getDirectory();
            exec($command, $out);

            $command = 'chown ' . $domain->domain . ':' . $domain->domain . ' ' . $domain->getDirectory() . '*';
            exec($command, $out);

            $command = 'chmod -R 700 ' . $domain->getDirectory() . 'wp-config.php';
            exec($command, $out);
            */

            exec('service nginx reload');
            exec('service php7.0-fpm reload');

            /*
            if($this->restart) {
                exec('service nginx reload');
                exec('service php5-fpm reload');

                sleep(1);
            }
            */

            echo "OK";
        }
        catch (\Exception $e)
        {
            echo "Error: " . $e->getMessage();

            $domain->cpanel_response = $e->getMessage();
            $domain->cpanel_status = -1;
        }

        $domain->save();
    }
}
