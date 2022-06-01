@extends('layouts.master')
@section('title', 'Project Details')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl border">
            <div class="bg-warning text-white p-2 border">
                <span style="font-size: 18px">Project Details</span><br>
                <span style="font-size: 14px">
                    <a href="{{ route('projects') }}" style="font-stretch: condensed; color: inherit">Back to List</a>
                </span>
            </div>
            <div class='container'>
                <form action="{{ route('editProject', [$project->id]) }}" method="post">
                {{ csrf_field() }}
                    <div class='row row-cols-3 pt-3'>
                        <input type="hidden" id="pid" name="pid" value="{{ $project->id }}">
                        <p><b>Project name</b><br>
                            <input type="text" id="pname" name="pname" onfocus="this.value=''" value="{{ $project->name }}"></p>
                        <p><b>Project description</b><br>
                            <input type="text" id="pdescription" name="pdescription" onfocus="this.value=''" value="{{ $project->description }}"></p>
                        <p class='align-self-end'>
                            <input type="submit" value="Update"></p>
                    </div>
                </form>
            </div>                 
        </div>
    </div>
    <div class="col-xl border">                        
        <div class="d-flex border"> 
            <div id="scrollTableContainer">                                            
                <div class="bg-warning text-white p-2 border">
                    <span style="font-size: 18px">Assigned Personnel</span><br>
                    <span style="font-size: 14px">Current users on this project</span>
                </div>                                        
                <div>
                    <div class="d-flex justify-content-between">
                        <div class="p-2">
                            <form action="{{ route('projects.show', [$project->id]) }}" method="get">
                                <input type="hidden" name="currentQuery" value="{{ $searchText }}">
                                <span>Show</span> 
                                <select style="width: 50px" id="entriesPerPage" name="entriesPerPage" onchange="this.form.submit()">
                                    <option value="5" {{ ( $project_personnels->perPage() == "5") ? "selected" : "" }}>5</option>
                                    <option value="10" {{ ( $project_personnels->perPage() == "10") ? "selected" : "" }}>10</option>
                                    <option value="15" {{ ( $project_personnels->perPage() == "15") ? "selected" : "" }}>15</option>
                                </select>
                                <span>entries</span> 
                            </form>
                        </div>
                        <div class="p-2">
                            <form action="{{ route('projects.show', [$project->id]) }}" method="get">
                                <input type="text" name="query"  onfocus="this.value=''" value="{{ $searchText }}">
                                <button type="submit"><i class="material-icons align-bottom">search</i></button>
                            </form>
                        </div>
                    </div>  
                    <table class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>@sortablelink('last_name', 'User Name')</th>
                                <th>@sortablelink('email', 'Email')</th>
                                <th>@sortablelink('role', 'Role')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($project_personnels as $project_personnel)
                            <tr>
                                <td>{{ $project_personnel->first_name }} {{ $project_personnel->last_name }}</td>
                                <td>{{ $project_personnel->email }}</td>
                                <td>{{ $project_personnel->role }}</td>
                            </tr>
                            @endforeach 
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">Showing {{ $project_personnels->firstItem() }} to {{ $project_personnels->lastItem() }} of {{ $project_personnels->total() }} entries</td>
                                <td>{{ $project_personnels->links() }}</td>
                            </tr>
                        </tfoot>    
                    </table>
                </div>                                    
            </div>   
        </div>                         
    </div>    
    <div class="col-xl border">                        
        <div class="d-flex border"> 
            <div id="scrollTableContainer">                                            
                <div class="bg-warning text-white p-2 border">
                    <span style="font-size: 18px">Tickets for this Project</span><br>
                    <span style="font-size: 14px">Condensed ticket details</span>
                </div>                                        
                <div>
                    <div class="d-flex justify-content-between">
                        <div class="p-2">
                            <form action="{{ route('projects.show', [$project->id]) }}" method="get">
                                <input type="hidden" name="currentQuery_tableTickets" value="{{ $searchText_tableTickets }}">
                                <span>Show</span> 
                                <select style="width: 50px" id="entriesPerPage_tableTickets" name="entriesPerPage_tableTickets" onchange="this.form.submit()">
                                    <option value="5" {{ ( $tickets->perPage() == "5") ? "selected" : "" }}>5</option>
                                    <option value="10" {{ ( $tickets->perPage() == "10") ? "selected" : "" }}>10</option>
                                    <option value="15" {{ ( $tickets->perPage() == "15") ? "selected" : "" }}>15</option>
                                </select>
                                <span>entries</span> 
                            </form>
                        </div>
                        <div class="p-2">
                            <form action="{{ route('projects.show', [$project->id]) }}" method="get">
                                <input type="text" name="query_tableTickets"  onfocus="this.value=''" value="{{ $searchText_tableTickets }}">
                                <button type="submit"><i class="material-icons align-bottom">search</i></button>
                            </form>
                        </div>
                    </div>       
                    <table class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>@sortablelink('title', 'Title')</th>
                                <th>@sortablelink('last_name_submitter', 'Submitter')</th>
                                <th>@sortablelink('last_name_developer', 'Developer')</th>
                                <th>@sortablelink('status', 'Status')</th>
                                <th>@sortablelink('created_at', 'Created')</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tickets as $ticket)
                            <tr>
                                <td>{{ $ticket->title }}</td>
                                <td>{{ $ticket->submitter['first_name'] }} {{ $ticket->submitter['last_name'] }}</td>
                                <td>{{ $ticket->developer['first_name'] }} {{ $ticket->developer['last_name'] }}</td>
                                <td>{{ $ticket->status }}</td>
                                <td>{{ $ticket->created_at }}</td>
                            </tr>
                            @endforeach                                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">Showing {{ $tickets->firstItem() }} to {{ $tickets->lastItem() }} of {{ $tickets->total() }} entries</td>
                                <td>{{ $tickets->links() }}</td>
                            </tr>
                        </tfoot> 
                    </table>
                </div>                                    
            </div>   
        </div>
    </div>              
</div>   
@endsection