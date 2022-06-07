@extends('layouts.master')
@section('title', 'Project Details')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="d-flex">   
                <div id="tableContainer">
                    <div class="bg-warning text-white p-2 border" style="position: relative">
                        <span style="font-size: 18px">
                            Project Details
                        </span>
                        <br>
                        <span style="font-size: 14px">
                            <a href="{{ route('projects') }}" style="font-stretch: condensed; color: inherit">
                                Back to List
                            </a> 
                            @if(session('LoggedUserRole') == 'Admin' || session('LoggedUserRole') == 'Project manager')
                            | 
        {{--                    <a href="{{ route('projects.editProjectForm', [$project->id]) }}" style="font-stretch: condensed; color: inherit">Edit</a>| --}}
                            <span class="dropdown" style="position: static; width: 100%">
                                <a data-bs-toggle="dropdown" role="button" id="editProject" style="font-stretch: condensed; color: inherit; text-decoration: underline">
                                    Edit
                                </a>
                                <div class="dropdown-menu">
                                    <form action="{{ route('editProject', [$project->id]) }}" method="post">
                                    {{ csrf_field() }}
                                        <div class='container'>
                                            <div class="row pt-3 pb-1">
                                                <div class="col">
                                                    <input type="hidden" id="pid" name="pid" value="{{ $project->id }}">
                                                    <label for="pname"><b>Project name</b></label>
                                                    <br>
                                                    <input type="text" id="pname" name="pname" onfocus="this.value=''" value="{{ $project->name }}">
                                                    <div class="text-danger">
                                                        @error('pname') 
                                                            {{ "The project name field is required." }}
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <label for="pdescription"><b>Project description</b></label>
                                                    <br>
                                                    <textarea id="pdescription" name="pdescription" rows="3" cols="23" style="overflow-y: scroll" onfocus="this.value=''">{{ $project->description }}</textarea>
                                                    <div class="text-danger">
                                                        @error('pdescription')
                                                            {{ "The project description field is required." }}
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="px-4 py-3">
                                            @if (session('LoggedUserId') != 3)
                                            <button type="submit">Update</button>
                                            @else
                                            <button type="submit" disabled>Update</button>
                                            @endif
                                        </div>
                                    </form>
                                </div>                                     
                            @endif
                        </span>
                    </div>
                    <div class='container-fluid'>
                        <div class='row pt-3'>
                            <p class="col-auto pe-5">
                                <b>Project name</b>
                                <br>
                                {{ $project->name }}
                            </p>
                            <p class="col">
                                <b>Project description</b>
                                <br>
                                {{ $project->description }}
                            </p>
                        </div>
                    </div>                 
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">                        
            <div class="d-flex"> 
                <div id="tableContainer">  
                    <div class="bg-warning text-white p-2 border">
                        <span style="font-size: 18px">
                            Assigned Personnel
                        </span>
                        <br>
                        <span style="font-size: 14px">
                            Current users on this project
                        </span>
                    </div>                                        
                    <div>
                        <div class="d-flex justify-content-between">
                            <div class="p-2">
                                <form action="{{ route('projects.show', [$project->id]) }}" method="get">
                                    <input type="hidden" name="currentQuery" value="{{ $searchText }}">
                                    <span>
                                        Show
                                    </span> 
                                    <select style="width: 50px" id="entriesPerPage" name="entriesPerPage" onchange="this.form.submit()">
                                        <option value="5" {{ ( $project_personnels->perPage() == "5") ? "selected" : "" }}>
                                            5
                                        </option>
                                        <option value="10" {{ ( $project_personnels->perPage() == "10") ? "selected" : "" }}>
                                            10
                                        </option>
                                        <option value="15" {{ ( $project_personnels->perPage() == "15") ? "selected" : "" }}>
                                            15
                                        </option>
                                    </select>
                                    <span>
                                        entries
                                    </span> 
                                </form>
                            </div>
                            <div class="p-2">
                                <form action="{{ route('projects.show', [$project->id]) }}" method="get">
                                    <input type="text" name="query"  onfocus="this.value=''" value="{{ $searchText }}">
                                    <button type="submit">
                                        <i class="material-icons align-bottom">search</i>
                                    </button>
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
                                @forelse ($project_personnels as $project_personnel)
                                <tr>
                                    <td>{{ $project_personnel->first_name }} {{ $project_personnel->last_name }}</td>
                                    <td>{{ $project_personnel->email }}</td>
                                    <td>{{ $project_personnel->role }}</td>
                                </tr>
                                @empty
                                    <td colspan="3" align="center">
                                        No data available.
                                    </td>
                                @endforelse 
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">
                                        Showing {{ $project_personnels->firstItem() ?? 0 }} to {{ $project_personnels->lastItem() ?? 0 }} of {{ $project_personnels->total() }} entries
                                    </td>
                                    <td class="absolute">{{ $project_personnels->links() }}</td>
                                </tr>
                            </tfoot>    
                        </table>
                    </div>                                    
                </div>   
            </div>                         
        </div>    
        <div class="col">                        
            <div class="d-flex"> 
                <div id="tableContainer">                                      
                    <div class="bg-warning text-white p-2 border">
                        <span style="font-size: 18px">
                            Tickets for this Project
                        </span>
                        <br>
                        <span style="font-size: 14px">
                            Condensed ticket details
                        </span>
                    </div>                                        
                    <div>
                        <div class="d-flex justify-content-between">
                            <div class="p-2">
                                <form action="{{ route('projects.show', [$project->id]) }}" method="get">
                                    <input type="hidden" name="currentQuery_tableTickets" value="{{ $searchText_tableTickets }}">
                                    <span>
                                        Show
                                    </span> 
                                    <select style="width: 50px" id="entriesPerPage_tableTickets" name="entriesPerPage_tableTickets" onchange="this.form.submit()">
                                        <option value="5" {{ ( $tickets->perPage() == "5") ? "selected" : "" }}>
                                            5
                                        </option>
                                        <option value="10" {{ ( $tickets->perPage() == "10") ? "selected" : "" }}>
                                            10
                                        </option>
                                        <option value="15" {{ ( $tickets->perPage() == "15") ? "selected" : "" }}>
                                            15
                                        </option>
                                    </select>
                                    <span>
                                        entries
                                    </span> 
                                </form>
                            </div>
                            <div class="p-2">
                                <form action="{{ route('projects.show', [$project->id]) }}" method="get">
                                    <input type="text" name="query_tableTickets"  onfocus="this.value=''" value="{{ $searchText_tableTickets }}">
                                    <button type="submit">
                                        <i class="material-icons align-bottom">search</i>
                                    </button>
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
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tickets as $ticket)
                                <tr>
                                    <td>{{ $ticket->title }}</td>
                                    <td>{{ $ticket->submitter['first_name'] }} {{ $ticket->submitter['last_name'] }}</td>
                                    <td>{{ $ticket->developer['first_name'] }} {{ $ticket->developer['last_name'] }}</td>
                                    <td>{{ $ticket->status }}</td>
                                    <td>{{ $ticket->created_at }}</td>
                                </tr>
                                @empty
                                    <td colspan="5" align="center">
                                        No data available.
                                    </td>
                                @endforelse                                            
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4">
                                        Showing {{ $tickets->firstItem() ?? 0 }} to {{ $tickets->lastItem() ?? 0 }} of {{ $tickets->total() }} entries
                                    </td>
                                    <td class="absolute">{{ $tickets->links() }}</td>
                                </tr>
                            </tfoot> 
                        </table>
                    </div>                                     
                </div>   
            </div>
        </div> 
    </div>       
</div>
@if ($errors->any())
    <script>
    $(document).ready(function() {
        $('#editProject').dropdown('toggle');
    });
    </script>
@endif      
@endsection