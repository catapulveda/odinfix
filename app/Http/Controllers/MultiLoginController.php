<?php

namespace App\Http\Controllers;

use App\Api\Config;
use App\Api\MultiLogin;
use App\DeleteTask;
use App\Jobs\DeleteMultiloginItem;
use App\MultiloginItem;
use App\MultiloginTask;
use App\Task;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Storage;

class MultiLoginController extends Controller
{
    public function index()
    {
        $tasks = MultiloginTask::orderBy('id', 'DESC')->get();

        return view('multilogin', ['tasks' => $tasks]);
    }

    public function items(MultiloginTask $task)
    {
        return view('multilogin_items', ['task' => $task]);
    }

    public function create()
    {
        return view('multilogin_new', ['title' => 'New MultiLogin task', 'delete' => 0]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'prefix' => 'required|max:50',
            'from' => 'required|integer|min:0',
            'to' => 'required|integer|min:0'
        ]);

        if ($request->input('from') >= $request->input('to')) return redirect()->back()->withErrors([
            '"To" should be greater than "from"'
        ]);

        $ips = trim($request->input('ips'));

        $lines = explode("\n", $ips);

        $proxy_ips = [];

        foreach ($lines as $line)
        {
            $line = trim($line);

            if($line) $proxy_ips[] = $line;
        }

        if(count($proxy_ips) > 0)
        {
            if(count($proxy_ips) != ($request->input('to') - $request->input('from') + 1)) return redirect()->back()->withErrors(['Number of proxies is not equal to range']);
        }

        $proxies = [];

        try {
            if (count($proxy_ips) > 0) {
                for ($i = 0; $i < count($proxy_ips); $i = $i + 1) {
                    $proxy = self::proxyToArray($proxy_ips[$i]);
                    $proxy['proxyType'] = 'http';

                    $proxies[] = json_encode($proxy);
                }
            }
        }
        catch (\Exception $e)
        {
            return redirect()->back()->withErrors(['IP list problem']);
        }

        $task = new MultiloginTask();
        $task->fill($request->all());
        $task->save();

        $k = 0;

        for($num = $task->from; $num <= $task->to; $num = $num + 1)
        {
            $item = new MultiloginItem();
            $item->name = $task->prefix . $num;
            $item->num = $num;

            if(count($proxies) > 0)
            {
                $item->proxy = $proxies[$k];
            }

            $item->task()->associate($task);
            $item->save();

            $k = $k + 1;
        }

        return redirect('/multilogin')->with('msg', 'Task was created!');
    }

    public static function proxyToArray($str)
    {
        $parts = explode('@', $str);

        if(count($parts) == 2)
        {
            $result = array_merge(self::split($parts[0], ['proxyUser', 'proxyPass']), self::split($parts[1], ['proxyHost', 'proxyPort']));
            return $result;
        }
        else
        {
            return self::split($str, ['proxyHost', 'proxyPort']);
        }
    }

    public static function split($str, $keys)
    {
        $parts = explode(":", $str);

        if(count($parts) != count($keys)) throw new \Exception('Invalid format');

        $result = [];

        for($i = 0; $i < count($parts); $i = $i + 1)
        {
            $result[$keys[$i]] = $parts[$i];
        }

        return $result;
    }

    public function settings()
    {
        return view('settings', ['config' => Config::get()]);
    }

    public function setSettings(Request $request)
    {
        Config::save($request->input('config'));

        return redirect('/multilogin/settings')->with('msg', 'Config was updated!');
    }

    public function delete(MultiloginItem $item)
    {
        try {
            dispatch(new DeleteMultiloginItem($item));

            return redirect()->back()->with('msg', 'Item was deleted!');
        }
        catch (\Exception $e)
        {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    public function deleteTask(MultiloginTask $task)
    {
        if($task->status != 1) return redirect()->back()->withErrors(["Can't delete"]);

        $task->status = -1;
        $task->save();

        return redirect()->back()->with('msg', 'Task was deleted');
    }

    public function deleteByRange()
    {
        $tasks = DeleteTask::orderBy('id', 'DESC')->get();

        return view('multilogin_new', ['title' => 'Multilogin Delete', 'delete' => 1, 'tasks' => $tasks]);
    }

    public function deleteByRangeDo(Request $request)
    {
        $this->validate($request, [
            'prefix' => 'required|max:50',
            'from' => 'required|integer|min:0',
            'to' => 'required|integer|min:0'
        ]);

        if ($request->input('from') >= $request->input('to')) return redirect()->back()->withErrors([
            '"To" should be greater than "from"'
        ]);

        $delete_task = new DeleteTask();
        $delete_task->prefix = $request->input('prefix');
        $delete_task->from = $request->input('from');
        $delete_task->to = $request->input('to');
        $delete_task->status = 0;
        $delete_task->save();

        return redirect()->back()->with('msg', 'Delete task was created!');


        try {
            $input = $request->all();

            $delete_profiles = [];

            for ($i = $input['from']; $i <= $input['to']; $i = $i + 1) {
                $delete_profiles[] = $input['prefix'] . $i;
            }

            $multilogin = new MultiLogin(Config::get('token'));
            $profiles = $multilogin->getProfiles();

            sleep(2);

            $total_deleted = 0;

            foreach ($delete_profiles as $delete_profile) {
                foreach ($profiles as $profile) {
                    if ($delete_profile == $profile['name']) {
                        sleep(2);

                        # Delete
                        $multilogin->removeProfile($profile['sid']);

                        $total_deleted = $total_deleted + 1;

                        $item = MultiloginItem::where('ext_id', $profile['sid'])->first();

                        if ($item) {
                            $item->status = -2;
                            $item->save();
                        }
                    }
                }
            }
        }
        catch (\Exception $e)
        {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }

        return redirect()->back()->with('msg', 'Success! Total delete: ' . $total_deleted);
    }
}
