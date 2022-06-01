@extends('layouts.master')
@section('title', 'Projects')
@section('content')
<div class="container-fluid">
    <div class="row">
        @if(session('LoggedUserRole') == 'Admin')
        <div class="col-auto">
            <div class="d-flex-column ps-5 pt-3">                
                <p>
                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#createNewProject" aria-expanded="false" aria-controls="createNewProject">
                        CREATE NEW PROJECT
                    </button>
                </p>
                <div class="collapse" id="createNewProject">
                    <form action="{{ route('createNewProject') }}" method="post">
                    {{ csrf_field() }}
                        <div class="container-fluid">
                            <div class="row pb-1">
                                <div class="col-auto">                  
                                    <label for="pname">Project name:</label><br>
                                    <input type="text" id="pname" name="pname" value="{{ old('pname') }}">
                                    <div class="text-danger">
                                        @error('pname') 
                                            {{ 'Project name is required.' }}
                                        @enderror                    
                                    </div>                              
                                </div>   
                                <div class="col-auto">
                                    <label for="pdescription">Project description:</label><br>
                                    <textarea id="pdescription" name="pdescription" rows="3" cols="23" style="overflow-y: scroll">{{ old('pdescription') }}</textarea>
                                    <div class="text-danger">    
                                        @error('pdescription') 
                                            {{ 'Project description is required.' }} 
                                        @enderror    
                                    </div>
                                </div>
                            </div>
                            <div class="row p-3">
                                <div class="col">
                                    @if (session('LoggedUserId') != 17)
                                        <button type="submit">Submit</button>
                                    @else
                                        <button type="submit" disabled>Submit</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>               
            </div>
        </div>    
        @endif      
        <div class="col-xxl">                 
            <div class="d-flex">
                <div id="tableContainer">    
                    <div class="bg-warning text-white p-2 border">
                        <span style="font-size: 18px">Your Projects</span><br>
                        <span style="font-size: 14px">All the projects in your database</span>
                    </div>
                    <div>

                        <div class="d-flex justify-content-between">
                            <div class="p-2">
                                <form action="{{ route('projects') }}" method="get">
                                    <input type="hidden" name="currentQuery" value="{{ $searchText }}">
                                    <span>Show</span> 
                                    <select style="width: 50px" id="entriesPerPage" name="entriesPerPage" onchange="this.form.submit()">
                                        <option value="5" {{ ( $projects->perPage() == "5") ? "selected" : "" }}>5</option>
                                        <option value="10" {{ ( $projects->perPage() == "10") ? "selected" : "" }}>10</option>
                                        <option value="15" {{ ( $projects->perPage() == "15") ? "selected" : "" }}>15</option>
                                    </select>
                                    <span>entries</span> 
                                </form>
                            </div>
                            <div class="p-2">
                                <form action="{{ route('projects') }}" method="get">
                                    <input type="text" name="query"  onfocus="this.value=''" value="{{ $searchText }}">
                                    <button type="submit"><i class="material-icons align-bottom">search</i></button>
                                </form>
                            </div>
                        </div>     

                        <table class="table table-condensed table-hover">
                            <thead>
                                <tr>
                                    <th>@sortablelink('name', 'Project Name')</th>
                                    <th>@sortablelink('description', 'Description')</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($projects as $project)
                                <tr>
                                    <td>{{$project->name}}</td>
                                    <td>{{$project->description}}</td>
                                    <td><a href="{{ route('projectAssignmentWithProjectId', ['projectId' => $project->id]) }}">Manage users</a><br><a href="{{ route('projects.show', [$project->id]) }}">Details</a></td>
                                </tr>
                                @empty
                                    <td colspan="3" align="center">No data available.</td>
                                @endforelse                            
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">Showing {{ $projects->firstItem() ?? 0 }} to {{ $projects->lastItem() ?? 0 }} of {{ $projects->total() }} entries</td>
                                    <td class="absolute">{{ $projects->links() }}</td>
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
        $('#createNewProject').collapse('show');
    });
    </script>
@endif   
@endsection
