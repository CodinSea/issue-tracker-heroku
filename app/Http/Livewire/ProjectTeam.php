<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Models\ProjectPersonnel;
use App\Models\User;
use App\Notifications\ProjectAssignmentNotification;
use App\Notifications\RemovedFromProjectNotification;
use Illuminate\Support\Facades\Notification;

class ProjectTeam extends Component
{
    public $projects;
    public $managers;
    public $developers;
    public $submitters;
    public $pid;
    public $projectManagers;
    public $projectDevelopers;
    public $projectSubmitters;

    public function mount()
    {
        if(session('LoggedUserRole') == 'Admin') {
            $project_ids = Project::select('id');
        } else {
            $project_ids = ProjectPersonnel::select('project_id')
                                            ->where('user_id', session('LoggedUserId'));
        }
        $this->projects = Project::whereIn('id', $project_ids)->get();
        $this->managers = User::where('role', 'Project manager')->get();
        $this->developers = User::where('role', 'Developer')->get();
        $this->submitters = User::where('role', 'Submitter')->get();

        $this->getProjectTeam();
    }

    public function updatedPid()
    {
        $this->getProjectTeam();        
    }

    public function getProjectTeam()
    {
        //dd($this->pid);
        if ($this->pid != '') {
            $userIds = ProjectPersonnel::select('user_id')
                        ->where('project_id', $this->pid);
            $this->projectManagers = User::whereIn('id', $userIds)->where('role', 'Project manager')->get();
            $this->projectDevelopers = User::whereIn('id', $userIds)->where('role', 'Developer')->get();
            $this->projectSubmitters = User::whereIn('id', $userIds)->where('role', 'Submitter')->get();
        } else {
            $this->projectManagers = [];
            $this->projectDevelopers = [];
            $this->projectSubmitters = [];
        }      
    }

    public function remove($id)
    {
        $remove = ProjectPersonnel::where('project_id', $this->pid)
                                        ->where('user_id', $id)
                                        ->delete();
        $this->getProjectTeam();

        $userToBeNotified = User::where('id', $id)->get();
        $projectName = Project::select('name')
                                ->where('id', $this->pid)
                                ->first();
        Notification::send($userToBeNotified, new RemovedFromProjectNotification($projectName->name));         
    }

    public function render()
    {
        return view('livewire.project-team');
    }
}
