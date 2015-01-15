<?php

namespace App\Http\Controllers;

use App\Domain;
use App\RemoveTask;
use App\Task;
use Illuminate\Http\Request;

use App\Http\Requests;

class TaskController extends Controller
{
    public function delete()
    {
        return view('delete');
    }

    public function deleteDo(Request $request)
    {
        $this->validate($request, ['domains' => 'required']);

        $lines = explode("\n", $request->input('domains'));

        $domains = [];

        foreach ($lines as $line)
        {
            $line = trim($line);

            if($line)
                $domains[] = $line;
        }

        $domainsToDelete = [];

        foreach ($domains as $domain)
        {
            $domainObj = Domain::where('domain', $domain)->first();

            if($domainObj)
            {
                $domainsToDelete[] = $domain;
            }
        }

        if(count($domainsToDelete) == 0) return redirect()->back()->withErrors(['Empty list']);

        return view('delete_confirmation', ['domainsToDelete' => $domainsToDelete, 'total' => count($domains), 'deleted' => count($domainsToDelete)]);
    }

    public function deleteComplete(Request $request)
    {
        $this->validate($request, ['domains' => 'required']);

        $lines = explode("\n", $request->input('domains'));

        $domains = [];

        foreach ($lines as $line)
        {
            $line = trim($line);

            if($line)
                $domains[] = $line;
        }

        $removeTask = new RemoveTask();
        $removeTask->domains = json_encode($domains);
        $removeTask->status = 0;
        $removeTask->total = count($domains);
        $removeTask->save();

        return redirect('/delete/tasks')->with('msg', 'Delete task was created');
    }

    public function deleteTasks()
    {
        $tasks = RemoveTask::orderBy('id', 'DESC')->get();

        return view('remove_tasks', ['tasks' => $tasks]);
    }

    public function domains()
    {
        $domains = Domain::all();

        $result = [];

        foreach ($domains as $domain)
        {
            $result[] = $domain->domain;
        }

        return view('domains', ['domains' => $domains, 'list' => $result]);
    }

    public function download()
    {
        $domains = Domain::all();

        $result = [];

        foreach ($domains as $domain)
        {
            $result[] = $domain->domain;
        }

        $name = 'domains.txt';

        $txt = implode("\n", $result);

        return response($txt)->header('Content-Type', 'plain/txt')->header('Content-Disposition', sprintf('attachment; filename="%s"', $name))->header('Content-Length', strlen($txt));
    }

    public function index()
    {
        $tasks = Task::orderBy('id', 'DESC')->get();

        return view('tasks', ['tasks' => $tasks]);
    }

    public function create()
    {
        return view('new_task');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:200',
            'domains' => 'required'
        ]);

        $lines = explode("\n", $request->input('domains'));

        $domains = [];
        $errors = [];

        foreach ($lines as $line)
        {
            $line = trim($line);

            if($line) {
                $domain = Domain::validate($line);

                if (substr_count($domain, ".") == 0) $errors[] = 'Invalid domain [' . $domain . ']';
                //if(Domain::where('domain', $domain)->count() > 0) $errors[] = 'Domain ['.$domain.'] already in database';

                $domains[] = $domain;
            }
        }

        if(count($domains) == 0) $errors[] = 'Domains not found';
        if(count($errors) > 0) return redirect()->back()->withErrors($errors);

        $task = new Task();
        $task->name = $request->input('name');
        $task->save();

        foreach ($domains as $domain)
        {
            $domain_obj = new Domain();
            $domain_obj->domain = $domain;
            $domain_obj->task()->associate($task);
            $domain_obj->save();
        }

        return redirect('/tasks')->with('msg', 'Task was created!');
    }

    public function get(Task $task)
    {
        return view('task', ['task' => $task]);
    }
}
