<?php

namespace App\Console\Commands;

use App\Api\Config;
use App\Api\Curl;
use App\Api\MultiLogin;
use App\DeleteTask;
use App\Domain;
use App\Jobs\CopyFilesWP;
use App\Jobs\CreateNginxConf;
use App\Jobs\DeleteByRange;
use App\RemoveTask;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * rsync -chavzP --stats root@:/var/www/html/ /home/aliaksandr/PhpstormProjects/ImeiParser-new/
     * @return mixed
     */
    public function handle()
    {
        // /var/
        // /home
        // /root
        // /etc

        $domainNames = [];

        $domainTxt = file_get_contents("domains.txt");

        $lines = explode("\n", $domainTxt);

        foreach ($lines as $line)
        {
            $domainNames[] = trim($line);
        }

        foreach ($domainNames as $domainName)
        {
            $domain = Domain::where('domain', $domainName)->first();

            if(!$domain) throw new \Exception('Domain not found ' . $domainName);

            dispatch(new CreateNginxConf($domain, false));

            echo "\n";
        }

        echo "Done";
        exit;
        $removeTasks = RemoveTask::all();

        $domains = [];

        foreach ($removeTasks as $removeTask)
        {
            $items = json_decode($removeTask->domains, true);

            foreach ($items as $item)
            {
                $domains[] = $item;
            }
        }

        $result = "";

        foreach ($domains as $domain)
        {
            $result = $result . $domain . "\n";
        }

        foreach ($domains as $domain)
        {
            $cmd = "rm -rf /var/www/" . $domain . "/";

            exec($cmd);
        }

        //file_put_contents("deleted_domains.txt", $result);

        exit;
        Curl::Start();

        $lines = explode(" ", file_get_contents('restore.txt'));

        $domains = [];

        foreach ($lines as $line)
        {
            $line = trim($line);

            $line = strtolower($line);

            if($line)
                $domains[] = $line;
        }

        $domains = array_unique($domains);

        $allTables = DB::connection('root')->select('SELECT table_name FROM information_schema.tables');

        $tables = [];

        foreach ($allTables as $table)
        {
            if(preg_match('|_options|isUS', $table->table_name))
                $tables[] = $table->table_name;
        }

        $result = [];

        foreach ($tables as $table)
        {
            $item = DB::connection('root')->table($table)->where('option_name', 'siteurl')->first();

            if($item)
            {
                if($item->option_value)
                {
                    foreach ($domains as $key => $domain)
                    {
                        $parts = parse_url(strtolower($item->option_value));

                        $domainFind = $parts['host'];
                        $domainFind = str_replace('www.', '', $domainFind);

                        if($domainFind == $domain)
                        {
                            $prefix = explode("_", $table);

                            if($domain == 'firmvenushut.com') $domain = 'firmVenushut.com';

                            $result[] = [
                                'domain' => $domain,
                                'table' => $table,
                                'path' => '/var/www/' . $domain . '/',
                                'full_url' => 'http://' . $domain,
                                'wp_config' => '/var/www/' . $domain . '/wp-config.php',
                                'prefix' => $prefix[0] . '_'
                            ];

                            unset($domains[$key]);
                            break;
                        }
                    }
                }
            }
        }

        foreach ($result as $item)
        {
            $url = $item['full_url'];

            $post = [
                'a' => 'SelfRemove',
                'c' => $item['path'],
                'p1' => 'yes',
                'p2' => '',
                'p3' => '',
                'charset' => 'Windows-1251'
            ];

            $page = Curl::PostQuery($url, $post);

            $wp_config = Storage::get('wp_config.conf');
            $wp_config = str_replace('%prefix%', $item['prefix'], $wp_config);

            file_put_contents($item['wp_config'], $wp_config);
            chmod($item['wp_config'], 644);

            echo $item['domain'] . "\n";
        }

        print_r($domains);

        exit;


        foreach ($result as $item)
        {
            # Delete script

            $url = $item['full_url'];

            $post = [
                'a' => 'SelfRemove',
                'c' => $item['path'],
                'p1' => 'yes',
                'p2' => '',
                'p3' => '',
                'charset' => 'Windows-1251'
            ];

            $page = Curl::PostQuery($url, $post);
        }

        exit;
        $prefixes = [];

        $from = 14; $to = 19;
        $prefix = 'wpn';

        for ($i = $from; $i <= $to; $i = $i + 1)
        {
            $prefixes[] = $prefix . $i . '_';
        }


        $from = 548; $to = 584;
        $prefix = 'wpx';

        for ($i = $from; $i <= $to; $i = $i + 1)
        {
            $prefixes[] = $prefix . $i . '_';
        }


        foreach ($prefixes as $prefix)
        {
            $table = $prefix . 'options';

            try {
                echo $prefix . ' ' . $table . ' ';

                $result = DB::connection('root')->table($table)->where('option_name', 'siteurl')->first();

                if (!$result) throw new \Exception('Siteurl not found');

                $siteUrl = $result->option_value;

                echo $siteUrl;

                $parts = parse_url($siteUrl);

                if(!isset($parts['host'])) throw new \Exception('Host not found');

                $domain = $parts['host'];

                $config_path = '/var/www/' . $domain . '/wp-config.php';

                echo " " . $config_path . "\n";
            }
            catch (\Exception $e)
            {
                echo "Error\n";
            }
        }

        exit;

        $domains = Domain::all();

        echo $domains->count();
        exit;

        foreach ($domains as $domain)
        {
            echo $domain->domain . " ";

            dispatch(new CreateNginxConf($domain, false));

            echo "\n";
        }

        exit;

        $domains = Domain::all();

        $k = 0;

        foreach ($domains as $domain) {
            $path = '/etc/nginx/sites-enabled/' . $domain->domain;

            //if(file_exists($path)) unlink($path);
        }

        $domains = explode(" ", file_get_contents("domains.txt"));

        foreach ($domains as $key => $domain)
        {
            $parts = parse_url($domain);

            if(isset($parts['host'])) $domains[$key] = $parts['host'];

            $domains[$key] = str_replace('www.', '', $domains[$key]);
        }

        foreach ($domains as $domain)
        {
            $domain = trim($domain);

            $domain_obj = Domain::where('domain', $domain)->first();

            if($domain_obj) {
                dispatch(new CreateNginxConf($domain_obj));
            }
                /*
            $directory = '/var/www/' . $domain . '/';

            $command = 'chmod -R 755 ' . $directory;
            exec($command, $out);

            $command = 'chown -R www-data:www-data ' . $directory;
            exec($command, $out);

            $command = 'chown -R www-data:www-data ' . $directory . '*';
            exec($command, $out);
            */

        }

        exit;

        $domains = Domain::all();

        $k = 0;

        foreach ($domains as $domain) {
            $dir = $domain->getDirectory();

            if (file_exists($dir)) {
                echo "\n" . $dir . "\n";

                //dispatch(new CreateNginxConf($domain));

                $replace_file = 'index.php';
                $wp_dir = '/root/wordpress/wordpress/';

                $from = $wp_dir . $replace_file;

                $to = $domain->getDirectory() . $replace_file;

                if (file_exists($to)) {
                    file_put_contents($to, file_get_contents($from));
                }

                $delete_file = $domain->getDirectory() . 'k!ll3r.html';

                if (file_exists($delete_file)) {
                    unlink($delete_file);
                }

                $k = $k + 1;

                //if($k > 1) exit;
            }
        }

        echo "\nCount: " . $k . "\n";
        //dispatch(new CreateNginxConf($domain));

        exit;
        $domain = Domain::inRandomOrder()->first();

        dispatch(new CreateNginxConf($domain));
    }

    public function createConfigs($domain)
    {
        $commands = [
            "groupadd " . $domain,
            "useradd -g " . $domain . " " . $domain
        ];

        foreach ($commands as $command)
        {
            exec($command);
        }

        $path = '/etc/php5/fpm/pool.d/' . $domain . '.conf';
        $php_fpm = Storage::get('php-fpm.conf');
        $php_fpm = str_replace('%domain%', $domain, $php_fpm);

        file_put_contents($path, $php_fpm);

        exit;
    }
}
