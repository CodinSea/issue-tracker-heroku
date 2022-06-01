<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Project;
use App\Models\History;
use App\Models\Comment;
use App\Models\Attachment;
use App\Models\User;
use App\Models\ProjectPersonnel;
use App\Notifications\NewTicketNotification;
use App\Notifications\TicketAssignmentNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class TicketsController extends Controller
{
    public function index(Request $request) {
        if(session('LoggedUserRole') == 'Admin'){
            $projects = Project::all();
        } else {
            $project_ids = ProjectPersonnel::select('project_id')
                                            ->where('user_id', session('LoggedUserId'));
            $projects = Project::whereIn('id', $project_ids)->get();
        }
        
        $developers = User::where('role', 'Developer')->get();

        switch (session('LoggedUserRole')) {
            case 'Admin':
                $ticket_ids = Ticket::select('id');
                break;
            case 'Project manager':
                $project_ids = ProjectPersonnel::select('project_id')
                                                    ->where('user_id', session('LoggedUserId'));
                $ticket_ids = Ticket::select('id')
                                        ->whereIn('project_id', $project_ids);
                break;
            case 'Developer':
                $ticket_ids = Ticket::select('id')
                                        ->where('developer_id', session('LoggedUserId'))
                                        ->orWhere('submitter_id', session('LoggedUserId'));
                break;
            case 'Submitter':
                $ticket_ids = Ticket::select('id')
                                        ->where('submitter_id', session('LoggedUserId'));
                break;
            default:
                $ticket_ids = [];
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
            $tickets = Ticket::whereIn('id', $ticket_ids)
                                    ->where(function ($query) use ($searchText) {
                                        $query->where('title','LIKE','%'.$searchText.'%')
                                              ->orWhere('description','LIKE','%'.$searchText.'%');
                                    })
                                    ->sortable()
                                    ->paginate($entriesPerPage)
                                    ->withQueryString();

            return view('tickets')->with(compact('tickets', 'projects', 'developers'))->with('searchText', $searchText);
        } else {          
            $tickets = Ticket::whereIn('id', $ticket_ids)
                                    ->where(function ($query) use ($currentQuery) {
                                        $query->where('title','LIKE','%'.$currentQuery.'%')  ->orWhere('description','LIKE','%'.$currentQuery.'%');
                                    })
                                    ->sortable()
                                    ->paginate($entriesPerPage)
                                    ->withQueryString();

            return view('tickets')->with(compact('tickets', 'projects', 'developers'))->with('searchText', $currentQuery);
        }

//        $tickets = Ticket::all();
//        $projects = Project::all();
//        return view('tickets', compact('tickets', 'projects'));
    }

    public function createNewTicket(Request $request) {
        $request->validate([
            'ttitle'=>'required',
            'tdescription'=>'required'
        ]);

        $newTicket = new Ticket;
        $newTicket->project_id = $request->tproject;
        $newTicket->developer_id = $request->tdeveloper;
        $newTicket->title = $request->ttitle;
        $newTicket->description = $request->tdescription;
        $newTicket->type = $request->ttype;
        $newTicket->priority = $request->tpriority;
        $newTicket->status = $request->tstatus;
        $newTicket->submitter_id = session('LoggedUserId');        
        $newTicket->save();

        if ($request->has('tdeveloper')) {
            $projectPersonnel = ProjectPersonnel::where('project_id', $request->tproject)
                                                    ->where('user_id', $request->tdeveloper)
                                                    ->first();
            if ($projectPersonnel === null) {
                $newProjectPersonnel = new ProjectPersonnel;
                $newProjectPersonnel->project_id = $request->tproject;
                $newProjectPersonnel->user_id = $request->tdeveloper;
                $newProjectPersonnel->save();

                $userToBeNotified = User::where('id', $request->tdeveloper)
                                    ->get();
                $projectName = Project::select('name')
                                ->where('id', $request->tproject)
                                ->first();
                Notification::send($userToBeNotified, new TicketAssignmentNotification($request->ttitle, $projectName->name));                 
            }
        }

        if ($request->has('tproject')) {
            $userIds = ProjectPersonnel::select('user_id')
                        ->where('project_id', $request->tproject);
            $usersToBeNotified = User::whereIn('id', $userIds)
                                            ->where('role', 'Project manager')
                                            ->orWhere(function($query) {
                                                $query->where('role', 'Admin');
                                            })
                                            ->get();
            $projectName = Project::select('name')
                                ->where('id', $request->tproject)
                                ->first();
            Notification::send($usersToBeNotified, new NewTicketNotification($projectName->name));            
        }

        return redirect('tickets');
    }

    public function show($id) {        
//        return view('ticket_details', [
//            'ticket' => Ticket::where('id', $id)->first(),
//            'histories' => History::where('ticket_id', $id)->get(),
//            'comments' => Comment::where('ticket_id', $id)->get(),
//            'attachments' => Attachment::where('ticket_id', $id)->get()
//        ]);
        $ticket = Ticket::where('id', $id)->first();
//        $projectUserIdsForSelectedTicket = ProjectPersonnel::select('user_id')
//                                                ->where('project_id', $ticket->project_id);
//        $developers = User::whereIn('id', $projectUserIdsForSelectedTicket)
//                                                ->where('role', 'Developer')->get();

        $developers = User::where('role', 'Developer')->get();

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

        if(isset($_GET['entriesPerPage_Comments'])) {
            $entriesPerPage_Comments = $_GET['entriesPerPage_Comments'];
            if(isset($_GET['currentQuery_Comments'])) {
                $currentQuery_Comments = $_GET['currentQuery_Comments'];
            } else {
                $currentQuery_Comments = "";
            }
        } else {
            $entriesPerPage_Comments = 5;
            $currentQuery_Comments = "";
        }

        if(isset($_GET['entriesPerPage_Attachments'])) {
            $entriesPerPage_Attachments = $_GET['entriesPerPage_Attachments'];
            if(isset($_GET['currentQuery_Attachments'])) {
                $currentQuery_Attachments = $_GET['currentQuery_Attachments'];
            } else {
                $currentQuery_Attachments = "";
            }
        } else {
            $entriesPerPage_Attachments = 5;
            $currentQuery_Attachments = "";
        }

        if(isset($_GET['query'])) {
            $searchText = $_GET['query'];
            $histories = History::where('ticket_id', $id)
                                        ->where(function ($query) use ($searchText) {
                                            $query->where('property','LIKE','%'.$searchText.'%')
                                                  ->orWhere('old_value','LIKE','%'.$searchText.'%')
                                                  ->orWhere('new_value','LIKE','%'.$searchText.'%')
                                                  ->orWhere('updated_at','LIKE','%'.$searchText.'%');
                                        })
                                        ->sortable()
                                        ->paginate($entriesPerPage)
                                        ->withQueryString();                            
            $comments = Comment::where('ticket_id', $id)
                               ->where(function ($query) use ($currentQuery_Comments){
                                   $query->where('remark','LIKE','%'.$currentQuery_Comments.'%')
                                         ->orWhere('created_at','LIKE','%'.$currentQuery_Comments.'%')
                                         ->orwhereHas('commenter', function ($query) use ($currentQuery_Comments) {
                                                $query->where('first_name', 'LIKE','%'.$currentQuery_Comments.'%')
                                                      ->orWhere('last_name', 'LIKE','%'.$currentQuery_Comments.'%');
                                            });  
                                })
                               ->sortable()
                               ->paginate($entriesPerPage_Comments, ['*'], 'comments_page')
                               ->withQueryString();

            $attachments = Attachment::where('ticket_id', $id)
                               ->where(function ($query) use ($currentQuery_Attachments){
                                   $query->where('attachment','LIKE','%'.$currentQuery_Attachments.'%')
                                        ->orWhere('description','LIKE','%'.$currentQuery_Attachments.'%')
                                         ->orWhere('created_at','LIKE','%'.$currentQuery_Attachments.'%')
                                         ->orwhereHas('uploader', function ($query) use ($currentQuery_Attachments) {
                                                $query->where('first_name', 'LIKE','%'.$currentQuery_Attachments.'%')
                                                      ->orWhere('last_name', 'LIKE','%'.$currentQuery_Attachments.'%');
                                            });  
                                })
                               ->sortable()
                               ->paginate($entriesPerPage_Attachments, ['*'], 'attachments_page')
                               ->withQueryString();

            return view('ticket_details')->with(compact('ticket', 'developers', 'histories', 'comments', 'attachments'))->with('searchText', $searchText)->with('searchText_Comments', "")->with('searchText_Attachments', "");
        } elseif(isset($_GET['query_Comments'])) {
            $histories = History::where('ticket_id', $id)
                                        ->where(function ($query) use ($currentQuery) {
                                            $query->where('property','LIKE','%'.$currentQuery.'%')
                                                  ->orWhere('old_value','LIKE','%'.$currentQuery.'%')
                                                  ->orWhere('new_value','LIKE','%'.$currentQuery.'%')
                                                  ->orWhere('updated_at','LIKE','%'.$currentQuery.'%');
                                        })
                                        ->sortable()
                                        ->paginate($entriesPerPage)
                                        ->withQueryString();

            $searchText_Comments = $_GET['query_Comments'];
            $comments = Comment::where('ticket_id', $id)
                               ->where(function ($query) use ($searchText_Comments){
                                   $query->where('remark','LIKE','%'.$searchText_Comments.'%')
                                         ->orWhere('created_at','LIKE','%'.$searchText_Comments.'%')
                                         ->orwhereHas('commenter', function ($query) use ($searchText_Comments) {
                                                $query->where('first_name', 'LIKE','%'.$searchText_Comments.'%')
                                                      ->orWhere('last_name', 'LIKE','%'.$searchText_Comments.'%');
                                            });  
                                })
                               ->sortable()
                               ->paginate($entriesPerPage_Comments, ['*'], 'comments_page')
                               ->withQueryString();

            $attachments = Attachment::where('ticket_id', $id)
                               ->where(function ($query) use ($currentQuery_Attachments){
                                   $query->where('attachment','LIKE','%'.$currentQuery_Attachments.'%')
                                        ->orWhere('description','LIKE','%'.$currentQuery_Attachments.'%')
                                         ->orWhere('created_at','LIKE','%'.$currentQuery_Attachments.'%')
                                         ->orwhereHas('uploader', function ($query) use ($currentQuery_Attachments) {
                                                $query->where('first_name', 'LIKE','%'.$currentQuery_Attachments.'%')
                                                      ->orWhere('last_name', 'LIKE','%'.$currentQuery_Attachments.'%');
                                            });  
                                })
                               ->sortable()
                               ->paginate($entriesPerPage_Attachments, ['*'], 'attachments_page')
                               ->withQueryString();

            return view('ticket_details')->with(compact('ticket', 'developers', 'histories', 'comments', 'attachments'))->with('searchText', "")->with('searchText_Comments', $searchText_Comments)->with('searchText_Attachments', "");

        } elseif(isset($_GET['query_Attachments'])) {  
            $histories = History::where('ticket_id', $id)
                                        ->where(function ($query) use ($currentQuery) {
                                            $query->where('property','LIKE','%'.$currentQuery.'%')
                                                  ->orWhere('old_value','LIKE','%'.$currentQuery.'%')
                                                  ->orWhere('new_value','LIKE','%'.$currentQuery.'%')
                                                  ->orWhere('updated_at','LIKE','%'.$currentQuery.'%');
                                        })
                                        ->sortable()
                                        ->paginate($entriesPerPage)
                                        ->withQueryString();

            $comments = Comment::where('ticket_id', $id)
                               ->where(function ($query) use ($currentQuery_Comments){
                                   $query->where('remark','LIKE','%'.$currentQuery_Comments.'%')
                                         ->orWhere('created_at','LIKE','%'.$currentQuery_Comments.'%')
                                         ->orwhereHas('commenter', function ($query) use ($currentQuery_Comments) {
                                                $query->where('first_name', 'LIKE','%'.$currentQuery_Comments.'%')
                                                      ->orWhere('last_name', 'LIKE','%'.$currentQuery_Comments.'%');
                                            });  
                                })
                               ->sortable()
                               ->paginate($entriesPerPage_Comments, ['*'], 'comments_page')
                               ->withQueryString();

            $searchText_Attachments = $_GET['query_Attachments'];
            $attachments = Attachment::where('ticket_id', $id)
                               ->where(function ($query) use ($searchText_Attachments){
                                   $query->where('attachment','LIKE','%'.$searchText_Attachments.'%')
                                        ->orWhere('description','LIKE','%'.$searchText_Attachments.'%')
                                         ->orWhere('created_at','LIKE','%'.$searchText_Attachments.'%')
                                         ->orwhereHas('uploader', function ($query) use ($searchText_Attachments) {
                                                $query->where('first_name', 'LIKE','%'.$searchText_Attachments.'%')
                                                      ->orWhere('last_name', 'LIKE','%'.$searchText_Attachments.'%');
                                            });  
                                })
                               ->sortable()
                               ->paginate($entriesPerPage_Attachments, ['*'], 'attachments_page')
                               ->withQueryString();

            return view('ticket_details')->with(compact('ticket', 'developers', 'histories', 'comments', 'attachments'))->with('searchText', "")->with('searchText_Comments', "")->with('searchText_Attachments', $searchText_Attachments);
        } else {
            $histories = History::where('ticket_id', $id)
                            ->where(function ($query) use ($currentQuery) {
                                $query->where('property','LIKE','%'.$currentQuery.'%')
                                        ->orWhere('old_value','LIKE','%'.$currentQuery.'%')
                                        ->orWhere('new_value','LIKE','%'.$currentQuery.'%')
                                        ->orWhere('updated_at','LIKE','%'.$currentQuery.'%');
                                        })
                            ->sortable()
                            ->paginate($entriesPerPage)
                            ->withQueryString();

            $comments = Comment::where('ticket_id', $id)
                               ->where(function ($query) use ($currentQuery_Comments){
                                   $query->where('remark','LIKE','%'.$currentQuery_Comments.'%')
                                         ->orWhere('created_at','LIKE','%'.$currentQuery_Comments.'%')
                                         ->orwhereHas('commenter', function ($query) use ($currentQuery_Comments) {
                                                $query->where('first_name', 'LIKE','%'.$currentQuery_Comments.'%')
                                                      ->orWhere('last_name', 'LIKE','%'.$currentQuery_Comments.'%');
                                            });  
                                })
                               ->sortable()
                               ->paginate($entriesPerPage_Comments, ['*'], 'comments_page')
                               ->withQueryString();

            $attachments = Attachment::where('ticket_id', $id)
                               ->where(function ($query) use ($currentQuery_Attachments){
                                   $query->where('attachment','LIKE','%'.$currentQuery_Attachments.'%')
                                        ->orWhere('description','LIKE','%'.$currentQuery_Attachments.'%')
                                         ->orWhere('created_at','LIKE','%'.$currentQuery_Attachments.'%')
                                         ->orwhereHas('uploader', function ($query) use ($currentQuery_Attachments) {
                                                $query->where('first_name', 'LIKE','%'.$currentQuery_Attachments.'%')
                                                      ->orWhere('last_name', 'LIKE','%'.$currentQuery_Attachments.'%');
                                            });  
                                })
                               ->sortable()
                               ->paginate($entriesPerPage_Attachments, ['*'], 'attachments_page')
                               ->withQueryString();

            return view('ticket_details')->with(compact('ticket', 'developers', 'histories', 'comments', 'attachments'))->with('searchText', $currentQuery)->with('searchText_Comments', $currentQuery_Comments)->with('searchText_Attachments', $currentQuery_Attachments);
        }

    }

    public function addComment(Request $request) {

        $newComment = new Comment;
        $newComment->ticket_id = $request->tid;
        $newComment->commenter_id = $request->tcommenterid;
        $newComment->remark = $request->tcomment;
        $newComment->save();

        return redirect()->route('tickets.show', [$newComment->ticket_id]);
    }

    public function uploadAttachment(Request $request) {
        $count = Attachment::where('ticket_id', $request->tid)->count();
        $path = $request->tfile->storeAs('attached_files', ($count + 1) . '.' . $request->tfile->extension(), 'public');

        $newAttachment = new Attachment;
        $newAttachment->ticket_id = $request->tid;
        $newAttachment->uploader_id = $request->tuploaderid;
        $newAttachment->attachment = $request->tfile;
        $newAttachment->description = $request->tfiledescription;
        $newAttachment->path = $path;
        $newAttachment->save();

        return redirect()->route('tickets.show', [$newAttachment->ticket_id]);
    }

    public function downloadAttachment($id) {
        $attachment = Attachment::where('id', $id)->first();

        return response()->download(base_path() . "/public/storage/" . $attachment->path);
    }    

    public function editTicketForm($id) {  
        $selectedTicket = Ticket::where('id', $id)->first();
        $projectUserIdsForSelectedTicket = ProjectPersonnel::select('user_id')
                                                ->where('project_id', $selectedTicket->project_id);
        return view('ticket_details_edit', [
            'ticket' => Ticket::where('id', $id)->first(),
            'developers' => User::whereIn('id', $projectUserIdsForSelectedTicket)
                                                ->where('role', 'Developer')->get(),
            'histories' => History::where('ticket_id', $id)->get(),
            'comments' => Comment::where('ticket_id', $id)->get(),
            'attachments' => Attachment::where('ticket_id', $id)->get()
        ]);
    }

    public function editTicket(Request $request, $id) {
        $request->validate([
            'ttitle'=>'required',
            'tdescription'=>'required'
        ]);

        $ticket = Ticket::find($id);
        //dd(User::find($request->tdeveloper)->first_name." ".User::find($request->tdeveloper)->last_name);

        $oldValues['title'] = $ticket->title;
        $oldValues['description'] = $ticket->description;
        if ($ticket->developer_id != null) {
            $oldValues['assigned developer'] = $ticket->developer->first_name." ".$ticket->developer->last_name;
        } else {
            $oldValues['assigned developer'] = null; 
        }
        $oldValues['priority'] = $ticket->priority;
        $oldValues['status'] = $ticket->status;
        $oldValues['type'] = $ticket->type;        

        $ticket->title = $request->ttitle;
        $ticket->description = $request->tdescription;
        $ticket->developer_id = $request->tdeveloper;
        $ticket->priority = $request->tpriority;
        if ($request->tstatus != null) {
            $ticket->status = $request->tstatus;
        }
        $ticket->type = $request->ttype;
        $ticket->save();

        if ($request->has('tdeveloper')) {
            $userToBeNotified = User::where('id', $request->tdeveloper)
                                    ->get();
            $projectName = Project::select('name')
                                ->where('id', $ticket->project_id)
                                ->first();
            Notification::send($userToBeNotified, new TicketAssignmentNotification($ticket->title, $projectName->name));            
        }

        $newValues['title'] = $ticket->title;
        $newValues['description'] = $ticket->description;
        if ($request->tdeveloper != null) {
            $assignedDev = User::find($request->tdeveloper);
            $newValues['assigned developer'] = $assignedDev->first_name." ".$assignedDev->last_name;
        } else {
            $newValues['assigned developer'] = $oldValues['assigned developer'];
        }
        $newValues['priority'] = $ticket->priority;
        $newValues['status'] = $ticket->status;
        $newValues['type'] = $ticket->type;

        foreach($oldValues as $x => $x_value) {
            if ($x_value != $newValues[$x]){
                $newHistory = new History;
                $newHistory->property = ucwords($x);
                $newHistory->old_value = $x_value;
                $newHistory->new_value = $newValues[$x];
                $newHistory->ticket_id = $id;
                $newHistory->save();
            }
        }

        return redirect()->route('tickets.show', [$id]);
    }


}
