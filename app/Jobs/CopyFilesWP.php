<?php

namespace App\Jobs;

use App\Domain;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CopyFilesWP extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    protected $domain;

    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user_group = env('CPANEL_USER');

        /*
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
        */

        $to_dir = $this->domain->getDirectory();

        $domain = $this->domain;

        $domain = preg_replace('|^www\.|isUS', '', $domain);

        /*
        if(!file_exists($to_dir))
        {
            mkdir($to_dir);

            $command = 'chmod -R 755 ' . $domain->getDirectory();
            exec($command, $out);

            $command = 'chown -R ' . $user_group . ':' . $user_group . ' ' . $domain->getDirectory();
            exec($command, $out);

            $command = 'chown -R ' . $user_group . ':' . $user_group . ' ' . $domain->getDirectory() . '*';
            exec($command, $out);
        }
        */

        //$this->domain->copy_status = 2;
        //$this->domain->save();
        if($this->domain->cloudflare_status == 1 and $this->domain->cpanel_status == 1)
        {

        }
        else
        {
            $this->domain->copy_status = 3;
            $this->domain->save();

            return true;
        }

        try {
            $from_dir = env('WP_DIR');

            if(!is_dir($to_dir)) throw new \Exception('Directory is file');

            if (!$this->is_dir_empty($to_dir)) throw new \Exception('Directory not empty');

            $cmd = 'rsync -a ' . $from_dir . ' ' . $to_dir;

            $result = $this->execute($cmd);

            if ($result['code'] != 0) throw new \Exception('Copy files problem');

            //exec('chown -R www-data ' . $to_dir);

            $this->domain->copy_status = 1;


            $command = 'chmod -R 755 ' . $domain->getDirectory();
            echo "\n" . $command . "\n";
            exec($command, $out);

            $command = 'chown -R ' . $domain . ':' . $user_group . ' ' . $domain->getDirectory();
            echo "\n" . $command . "\n";
            exec($command, $out);

            $command = 'chown -R ' . $domain . ':' . $user_group . ' ' . $domain->getDirectory() . '*';
            echo "\n" . $command . "\n";
            exec($command, $out);

        } catch (\Exception $e) {
            $this->domain->copy_status = -1;
            $this->domain->copy_response = $e->getMessage();
        }

        $this->domain->save();
    }

    protected function is_dir_empty($dir)
    {
        if (!is_readable($dir)) return NULL;
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != ".." && $entry != "cgi-bin" && $entry != '.htaccess' && $entry != '.well-known') {
                return FALSE;
            }
        }
        return TRUE;
    }

    protected function execute($cmd, $workdir = null)
    {

        if (is_null($workdir)) {
            $workdir = __DIR__;
        }

        $pipes = [];

        $descriptorspec = array(
            0 => array("pipe", "r"),  // stdin
            1 => array("pipe", "w"),  // stdout
            2 => array("pipe", "w"),  // stderr
        );

        $process = proc_open($cmd, $descriptorspec, $pipes, $workdir, null);

        stream_set_blocking($pipes[0], false);
        stream_set_blocking($pipes[1], false);

        for ($i = 0; $i < 500; $i = $i + 1) {
            fread($pipes[1], 20000);
            fread($pipes[2], 20000);
            $status = proc_get_status($process);

            print_r($status);

            if ($status['running'] === FALSE) break;

            sleep(1);
        }

        if ($status['running'] !== FALSE) throw new \Exception('Too long');

        $stdout = fread($pipes[1], 20000);
        fclose($pipes[1]);

        $stderr = fread($pipes[2], 20000);
        fclose($pipes[2]);

        return [
            'code' => $status['exitcode'],
            'out' => trim($stdout),
            'err' => trim($stderr),
        ];
    }
}
