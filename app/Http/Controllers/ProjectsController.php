<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\Project;
use App\Models\ProjectPersonnel;
use App\Models\User;
use App\Models\Ticket;
use App\Notifications\ProjectAssignmentNotification;
use App\Notifications\ProjectNameChangeNotification;
use Illuminate\Support\Facades\Notification;
/*use Illuminate\Support\Facades\DB;*/
//use App\Http\Requests\AssignProjectRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Rules\AlreadyHasOne;
use App\Rules\AlreadyAssigned;
use Illuminate\Support\Facades\Gate;

class ProjectsController extends Controller
{
    public function projectAssignment() 
    {
        return view('project_assignment', ['pid'=> '']);
    }

    public function projectAssignmentWithProjectId($projectId) 
    {
        if (session('LoggedUserRole') != 'Admin') {
            $projectIds = ProjectPersonnel::where('user_id', session('LoggedUserId'))
                                                    ->pluck('project_id')
                                                    ->toArray();
            if (!in_array($projectId, $projectIds)) {
                abort(403);
            }
        }
        
        return view('project_assignment', ['pid'=> $projectId]);
    }

    public function assignProject(Request $request) 
    {     
        $validator = Validator::make($request->all(), [
            'selected_project' => 'required',
            'selected_project_manager' => [new AlreadyHasOne(request()->input('selected_project'))],
            'selected_developers.*' => [new AlreadyAssigned(request()->input('selected_project'))],
            'selected_submitters.*' => [new AlreadyAssigned(request()->input('selected_project'))],
        ]);
 
        if ($request->selected_project == null) {
            return redirect()->route('projectAssignment')
                        ->withErrors($validator)
                        ->withInput();
        } elseif ($request->selected_project != null && $validator->fails()) {
            return redirect()->route('projectAssignmentWithProjectId', ['projectId' => $request->selected_project])
                        ->withErrors($validator)
                        ->withInput();            
        } else {   
            if ($request->has(['selected_project_manager'])) {
                $newProjectPersonnel = new ProjectPersonnel;
                $newProjectPersonnel->project_id = $request->selected_project;
                $newProjectPersonnel->user_id = $request->selected_project_manager;
                $newProjectPersonnel->save();

                $toBeNotified = [$request->selected_project_manager];
            } else {
                $toBeNotified = [];
            }

            if ($request->has(['selected_developers'])) {
                foreach ($request->selected_developers as $selected_developer) {
                    $newProjectPersonnel = new ProjectPersonnel;
                    $newProjectPersonnel->project_id = $request->selected_project;
                    $newProjectPersonnel->user_id = $selected_developer;
                    $newProjectPersonnel->save();
                    
                    $toBeNotified = Arr::prepend($toBeNotified, $selected_developer);
                }
            }

            if ($request->has(['selected_submitters'])) {
                foreach ($request->selected_submitters as $selected_submitter) {
                    $newProjectPersonnel = new ProjectPersonnel;
                    $newProjectPersonnel->project_id = $request->selected_project;
                    $newProjectPersonnel->user_id = $selected_submitter;
                    $newProjectPersonnel->save();
                    
                    $toBeNotified = Arr::prepend($toBeNotified, $selected_submitter);
                }
            }

            $usersToBeNotified = User::whereIn('id', $toBeNotified)->get();
            $projectName = Project::select('name')
                                    ->where('id', $request->selected_project)
                                    ->first();
            Notification::send($usersToBeNotified, new ProjectAssignmentNotification($projectName->name));

            return redirect()->route('projectAssignmentWithProjectId', ['projectId' => $request->selected_project]);
        }
    }

    public function index(Request $request) 
    {
        if(session('LoggedUserRole') == 'Admin') {
            $project_ids = Project::select('id');
        } else {
            $project_ids = ProjectPersonnel::select('project_id')
                                            ->where('user_id', session('LoggedUserId'));
        }

        if(isset($_GET['entriesPerPage'])) {
            $entriesPerPage = $_GET['entriesPerPage'];
            if(isset($_GET['currentQuery'])) {
                $currentQuery = $_GET['currentQuery'];
            } else {
                $currentQuery = "";
            }
        } else {
            $entriesPerPage = 5;
            $currentQuery = "";
        }

        if(isset($_GET['query'])) {
            $searchText = $_GET['query'];
            $projects = Project::whereIn('id', $project_ids)
                                    ->where(function ($query) use ($searchText) {
                                        $query->where('name','LIKE','%'.$searchText.'%')
                                              ->orWhere('description','LIKE','%'.$searchText.'%');
                                    })
                                    ->sortable()
                                    ->paginate($entriesPerPage)
                                    ->withQueryString();

            return view('projects')->with(compact('projects'))->with('searchText', $searchText);
        } else {          
            $projects = Project::whereIn('id', $project_ids)
                                    ->where(function ($query) use ($currentQuery) {
                                        $query->where('name','LIKE','%'.$currentQuery.'%')  ->orWhere('description','LIKE','%'.$currentQuery.'%');
                                    })
                                    ->sortable()
                                    ->paginate($entriesPerPage)
                                    ->withQueryString();

            return view('projects')->with(compact('projects'))->with('searchText', $currentQuery);
        }
    }

    public function createNewProject(Request $request) {
        $request->validate([
            'pname' => 'required',
            'pdescription' => 'required'
        ]);

        $newProject = new Project;
        $newProject->name = $request->pname;
        $newProject->description = $request->pdescription;
        $newProject->save();

        return redirect('/projects');
    }

    public function deleteProject(Request $request) {
        $deleted = Project::find($request->pid)->delete();

        return redirect('/projects');
    }

    public function show(Request $request, $id) {
        $project = Project::where('id', $id)->first();  
        $user_ids = ProjectPersonnel::select('user_id')
                        ->where('project_id', $id);

        if(isset($_GET['entriesPerPage'])) {
            $entriesPerPage = $_GET['entriesPerPage'];
            if(isset($_GET['currentQuery'])) {
                $currentQuery = $_GET['currentQuery'];
            } else {
                $currentQuery = "";
            }
        } else {
            $entriesPerPage = 5;
            $currentQuery = "";
        }

        if(isset($_GET['entriesPerPage_tableTickets'])) {
            $entriesPerPage_tableTickets = $_GET['entriesPerPage_tableTickets'];
            if(isset($_GET['currentQuery_tableTickets'])) {
                $currentQuery_tableTickets = $_GET['currentQuery_tableTickets'];
            } else {
                $currentQuery_tableTickets = "";
            }
        } else {
            $entriesPerPage_tableTickets = 5;
            $currentQuery_tableTickets = "";
        }

        if(isset($_GET['query'])) {
            $searchText = $_GET['query'];
            $project_personnels = User::whereIn('id', $user_ids)
                                        ->where(function ($query) use ($searchText) {
                                            $query->where('first_name','LIKE','%'.$searchText.'%')
                                                  ->orWhere('last_name','LIKE','%'.$searchText.'%')
                                                  ->orWhere('email','LIKE','%'.$searchText.'%')
                                                  ->orWhere('role','LIKE','%'.$searchText.'%');
                                        })
                                        ->sortable()
                                        ->paginate($entriesPerPage)
                                        ->withQueryString();                            
            $tickets = Ticket::where('project_id', $id)
                               ->where(function ($query) use ($currentQuery_tableTickets){
                                   $query->where('title','LIKE','%'.$currentQuery_tableTickets.'%')
                                         ->orWhere('status','LIKE','%'.$currentQuery_tableTickets.'%')
                                         ->orWhere('created_at','LIKE','%'.$currentQuery_tableTickets.'%')
                                         ->orwhereHas('submitter', function ($query) use ($currentQuery_tableTickets) {
                                                $query->where('first_name', 'LIKE','%'.$currentQuery_tableTickets.'%')
                                                      ->orWhere('last_name', 'LIKE','%'.$currentQuery_tableTickets.'%');
                                            })
                                            ->orwhereHas('developer', function ($query) use ($currentQuery_tableTickets) {
                                                $query->where('first_name', 'LIKE','%'.$currentQuery_tableTickets.'%')
                                                      ->orWhere('last_name', 'LIKE','%'.$currentQuery_tableTickets.'%');
                                            });  
                                })
                               ->sortable()
                               ->paginate($entriesPerPage_tableTickets, ['*'], 'tickets_page')
                               ->withQueryString();

            return view('project_details')->with(compact('project', 'project_personnels', 'tickets'))->with('searchText', $searchText)->with('searchText_tableTickets', "");
        } elseif(isset($_GET['query_tableTickets'])) {
            $project_personnels = User::whereIn('id', $user_ids)
                                        ->where(function ($query) use ($currentQuery) {
                                            $query->where('first_name','LIKE','%'.$currentQuery.'%')
                                                  ->orWhere('last_name','LIKE','%'.$currentQuery.'%')
                                                  ->orWhere('email','LIKE','%'.$currentQuery.'%')
                                                  ->orWhere('role','LIKE','%'.$currentQuery.'%');
                                        })
                                        ->sortable()
                                        ->paginate($entriesPerPage)
                                        ->withQueryString();
            $searchText_tableTickets = $_GET['query_tableTickets'];
            $tickets = Ticket::where('project_id', $id)
                               ->where(function ($query) use ($searchText_tableTickets) {
                                    $query->where('title','LIKE','%'.$searchText_tableTickets.'%')
                                            ->orWhere('status','LIKE','%'.$searchText_tableTickets.'%')
                                            ->orWhere('created_at','LIKE','%'.$searchText_tableTickets.'%')
                                            ->orwhereHas('submitter', function ($query) use ($searchText_tableTickets) {
                                                $query->where('first_name', 'LIKE','%'.$searchText_tableTickets.'%')
                                                      ->orWhere('last_name', 'LIKE','%'.$searchText_tableTickets.'%');
                                            })
                                            ->orwhereHas('developer', function ($query) use ($searchText_tableTickets) {
                                                $query->where('first_name', 'LIKE','%'.$searchText_tableTickets.'%')
                                                      ->orWhere('last_name', 'LIKE','%'.$searchText_tableTickets.'%');
                                            });                                          
                                })                                
                               ->sortable()
                               ->paginate($entriesPerPage_tableTickets, ['*'], 'tickets_page')
                               ->withQueryString();

            return view('project_details')->with(compact('project', 'project_personnels', 'tickets'))->with('searchText', "")->with('searchText_tableTickets', $searchText_tableTickets);
        } else {  
            $project_personnels = User::whereIn('id', $user_ids)
                                        ->where(function ($query) use ($currentQuery) {
                                            $query->where('first_name','LIKE','%'.$currentQuery.'%')
                                                  ->orWhere('last_name','LIKE','%'.$currentQuery.'%')
                                                  ->orWhere('email','LIKE','%'.$currentQuery.'%')
                                                  ->orWhere('role','LIKE','%'.$currentQuery.'%');
                                        })
                                        ->sortable()
                                        ->paginate($entriesPerPage)
                                        ->withQueryString();     
            $tickets = Ticket::where('project_id', $id)
                               ->where(function ($query) use ($currentQuery_tableTickets) {
                                    $query->where('title','LIKE','%'.$currentQuery_tableTickets.'%')
                                          ->orWhere('status','LIKE','%'.$currentQuery_tableTickets.'%')
                                          ->orWhere('created_at','LIKE','%'.$currentQuery_tableTickets.'%')
                                          ->orwhereHas('submitter', function ($query) use ($currentQuery_tableTickets) {
                                                $query->where('first_name', 'LIKE','%'.$currentQuery_tableTickets.'%')
                                                      ->orWhere('last_name', 'LIKE','%'.$currentQuery_tableTickets.'%');
                                            })
                                            ->orwhereHas('developer', function ($query) use ($currentQuery_tableTickets) {
                                                $query->where('first_name', 'LIKE','%'.$currentQuery_tableTickets.'%')
                                                      ->orWhere('last_name', 'LIKE','%'.$currentQuery_tableTickets.'%');
                                            });  
                                })
                               ->sortable()
                               ->paginate($entriesPerPage_tableTickets, ['*'], 'tickets_page')
                               ->withQueryString();

            return view('project_details')->with(compact('project', 'project_personnels', 'tickets'))->with('searchText', $currentQuery)->with('searchText_tableTickets', $currentQuery_tableTickets);
        }

//        return view('project_details', [
//            'project' => Project::where('id', $id)->first(),  
//            'project_personnels' => User::whereIn('id', $user_ids)->get(),
//            'tickets' => Ticket::where('project_id', $id)->get()
//        ]);         
    }

    public function editProjectForm($id) { 
        $user_ids = ProjectPersonnel::select('user_id')
                        ->where('project_id', $id);
        $entriesPerPage = 5;
        $searchText = "";
        $searchText_tableTickets = "";
                              
        return view('project_details_edit', [
            'project' => Project::where('id', $id)->first(),  
            'project_personnels' => User::whereIn('id', $user_ids)
                                            ->sortable()
                                            ->paginate($entriesPerPage),
            'tickets' => Ticket::where('project_id', $id)
                                    ->sortable()
                                    ->paginate($entriesPerPage)
        ])->with('searchText', $searchText)->with('searchText_tableTickets', $searchText_tableTickets);
    }   

    public function editProject(Request $request, $id) {
        $request->validate([
            'pname'=>'required',
            'pdescription'=>'required'
        ]);

        $project = Project::find($id);
        $projectOldName = $project->name;
        $project->name = $request->pname;
        $project->description = $request->pdescription;
        $project->save();

        if ($request->has('pname')) {
            $userIds = ProjectPersonnel::select('user_id')
                        ->where('project_id', $project->id);
            $usersToBeNotified = User::whereIn('id', $userIds)->get();
            Notification::send($usersToBeNotified, new ProjectNameChangeNotification($projectOldName, $project->name));
        }

        return redirect()->route('projects.show', [$id]);
    }
}
