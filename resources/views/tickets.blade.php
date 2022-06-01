@extends('layouts.master')
@section('title', 'Tickets')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-auto">
            <div class="d-flex-column ps-5 pt-3">
                <p>
                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#createNewTicket" aria-expanded="false" aria-controls="createNewTicket">
                        CREATE NEW TICKET
                    </button>
                </p>
                <div class="collapse" id="createNewTicket">
                    <form action="{{ route('createNewTicket') }}" method="post">
                    {{ csrf_field() }}
                        <div class="container-fluid">
                            <div class="row pb-1">
                                <div class="col-auto">
                                    <label for="tproject">Project:</label><br>
                                    <select id="tproject" name="tproject"> 
                                        @foreach ($projects as $project)
                                        <option value="{{$project->id}}" {{ (old('tproject') == $project->id) ? "selected" : "" }}>{{$project->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if(session('LoggedUserRole') == 'Admin' || session('LoggedUserRole') == 'Project manager')
                                <div class="col-auto">
                                    <label for="tdeveloper">Assigned developer:</label><br>
                                    <select id="tdeveloper" name="tdeveloper"> 
                                        @foreach ($developers as $developer)
                                        <option value="{{$developer->id}}" {{ (old('tdeveloper') == $developer->id) ? "selected" : "" }}>{{ $developer->first_name }} {{ $developer->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                            </div>
                            <div class="row pb-1">                               
                                <div class="col-auto">
                                    <label for="ttitle">Ticket title:</label><br>
                                    <input type="text" id="ttitle" name="ttitle" value="{{ old('ttitle') }}">
                                    <div class="text-danger">
                                        @error('ttitle') 
                                            {{ "The title field is required." }} 
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <label for="ttype">Ticket type:</label><br>
                                    <select id="ttype" name="ttype">
                                        <option {{ (old('ttype') == "Bug report") ? "selected" : "" }}>Bug report</option>
                                        <option {{ (old('ttype') == "Feature request") ? "selected" : "" }}>Feature request</option>
                                    </select>
                                </div>                                  
                            </div>
                            <div class="row pb-1">
                                <div class="col-auto">
                                    <label for="tdescription">Ticket description:</label><br>
                                    <textarea id="tdescription" name="tdescription" rows="3" cols="23" style="overflow-y: scroll">{{ old('tdescription') }}</textarea>                                    
                                    <div class="text-danger">
                                        @error('tdescription') 
                                            {{ "The description field is required." }}
                                        @enderror
                                    </div>
                                </div>                                
                                <div class="col-auto">
                                    <label for="tpriority">Ticket priority:</label><br>
                                    <select id="tpriority" name="tpriority">
                                        <option {{ (old('tpriority') == "High") ? "selected" : "" }}>High</option>
                                        <option {{ (old('tpriority') == "Medium") ? "selected" : "" }}>Medium</option>
                                        <option {{ (old('tpriority') == "Low") ? "selected" : "" }}>Low</option>
                                    </select>
                                </div>
                            </div>                    
                            <input type="hidden" name="tstatus" value="Open">      
                            <div class="row p-3">
                                <div class="col">
                                    @if (session('LoggedUserId') != 17 && session('LoggedUserRole') != null)
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
            <div class="d-flex">
                <div id="tableContainer">    
                    <div class="bg-warning text-white p-2 border">
                        <span style="font-size: 18px">Your Tickets</span><br>
                        <span style="font-size: 14px">All the tickets in your database</span>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between">
                            <div class="p-2">
                                <form action="{{ route('tickets') }}" method="get">
                                    <input type="hidden" name="currentQuery" value="{{ $searchText }}">
                                    <span>Show</span> 
                                    <select style="width: 50px" id="entriesPerPage" name="entriesPerPage" onchange="this.form.submit()">
                                        <option value="5" {{ ( $tickets->perPage() == "5") ? "selected" : "" }}>5</option>
                                        <option value="10" {{ ( $tickets->perPage() == "10") ? "selected" : "" }}>10</option>
                                        <option value="15" {{ ( $tickets->perPage() == "15") ? "selected" : "" }}>15</option>
                                    </select>
                                    <span>entries</span> 
                                </form>
                            </div>
                            <div class="p-2">
                                <form action="{{ route('tickets') }}" method="get">
                                    <input type="text" name="query"  onfocus="this.value=''" value="{{ $searchText }}">
                                    <button type="submit"><i class="material-icons align-bottom">search</i></button>
                                </form>
                            </div>
                        </div>         
                        <table class="table table-condensed table-hover">
                            <thead>
                                <tr>
                                    <th>@sortablelink('title', 'Ticket Title')</th>
                                    <th>@sortablelink('description', 'Description')</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tickets as $ticket)
                                <tr>
                                    <td>{{$ticket->title}}</td>
                                    <td>{{$ticket->description}}</td>
                                    <td><a href="{{ route('tickets.show', [$ticket->id]) }}">Details</a></td>
                                </tr>
                                @empty
                                    <td colspan="3" align="center">No data available.</td>
                                @endforelse  
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">Showing {{ $tickets->firstItem() ?? 0 }} to {{ $tickets->lastItem() ?? 0 }} of {{ $tickets->total() }} entries</td>
                                    <td class="absolute">{{ $tickets->onEachSide(1)->links() }}</td>
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
        $('#createNewTicket').collapse('show');
    });
    </script>
@endif 
@endsection
