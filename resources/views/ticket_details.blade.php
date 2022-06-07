@extends('layouts.master')
@section('title', 'Ticket Details')
@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-xxl">
			<div class="d-flex">	
				<div id="tableContainer">  
					<div class="bg-warning text-white p-2">
						<span style="font-size: 18px">
							Ticket Details
						</span>
						<br>						
						<span style="font-size: 14px">
							<a href="{{ route('tickets') }}" style="font-stretch: condensed; color:  inherit">
								Back to List
							</a> |

		{{--					<a href="{{ route('tickets.editTicketForm', [$ticket->id]) }}" style="font-stretch: condensed; color: inherit">Edit Ticket</a>  --}}

		                    <span class="dropdown" style="position: static; width: 100%">
		                        <a data-bs-toggle="dropdown" role="button"  data-bs-offset="-86, 8" id="editTicket" style="font-stretch: condensed; color: inherit; text-decoration: underline">
		                        	Edit
		                        </a>
		                        <div class="dropdown-menu">   
		                        	<form action="{{ route('editTicket', [$ticket->id]) }}" method="post">
		                            {{ csrf_field() }}
		                            	<div class='container'>
											<div class="row pt-3 pb-1">
												<div class="col">
													<input type="hidden" id="tid" name="tid" value="{{ $ticket->id }}">
													<label for="ttitle"><b>Ticket title</b></label>
													<br>				
													<input type="text" id="ttitle" name="ttitle" onfocus="this.value=''" value="{{ $ticket->title }}">
													<div class="text-danger">
														@error('ttitle') 
															{{ "The ticket title field is required." }}
														@enderror
	                        						</div>
                        						</div>
												<div class="col">
													<label for="tdescription"><b>Ticket description</b></label>
													<br>
													<textarea id="tdescription" name="tdescription" rows="3" cols="23" style="overflow-y: scroll" onfocus="this.value=''">{{ $ticket->description }}</textarea>
													<div class="text-danger">
														@error('tdescription') 
															{{ "The ticket description field is required." }}
														@enderror
                        							</div> 
                        						</div>
                        					</div>
                        					<div class="row pb-1">           
                        						<div class="col">
													<label><b>Project</b></label>
													<br>
													<div style="width: 250px">
														{{ $ticket->project['name'] }}
													</div>
												</div>
												<div class="col">
													<label><b>Submitter</b></label>
													<br>
													<div style="width: 250px">
														{{ $ticket->submitter['first_name'] }} {{ $ticket->submitter['last_name'] }}
													</div>
												</div>
											</div>
											<div class="row pb-1">
												<div class="col">
													<label for="ttype"><b>Ticket type</b></label>
													<br>
													<select id="ttype" name="ttype">
														<option value="Bug report" {{ ( $ticket->type == "Bug report") ? "selected" : "" }}>
															Bug report
														</option>
														<option value="Feature request" {{ ( $ticket->type == "Feature request") ? "selected" : "" }}>
															Feature request
														</option>
													</select>
												</div>	
												<div class="col">
													<label for="tpriority"><b>Ticket priority</b></label>
													<br>
													<select id="tpriority" name="tpriority">
														<option value="High" {{ ( $ticket->priority == "High") ? "selected" : "" }}>
															High
														</option>
														<option value="Medium" {{ ( $ticket->priority == "Medium") ? "selected" : "" }}>
															Medium
														</option>
														<option value="Low" {{ ( $ticket->priority == "Low") ? "selected" : "" }}>
															Low
														</option>
													</select>
												</div>
											</div>
											<div class="row pb-1">
												@if(session('LoggedUserRole') == 'Admin' || session('LoggedUserRole') == 'Project manager' || (session('LoggedUserRole') == 'Developer' && $ticket->developer_id == session('LoggedUserId')))
												<div class="col">
													<label><b>Ticket status</b></label>
													<br>
													<select id="tstatus" name="tstatus">
														<option value="Open" {{ ( $ticket->status == "Open") ? "selected" : "" }}>
															Open
														</option>
														<option value="In progress" {{ ( $ticket->status == "In progress") ? "selected" : "" }}>
															In progress
														</option>
														<option value="In review" {{ ( $ticket->status == "In review") ? "selected" : "" }}>
															In review
														</option>
														<option value="Closed" {{ ( $ticket->status == "Closed") ? "selected" : "" }}>
															Closed
														</option>
													</select>
												</div>
												@else
												<div class="col"></div>
												@endif
		                    					@if(session('LoggedUserRole') == 'Admin' || session('LoggedUserRole') == 'Project manager')
												<div class="col">
													<label><b>Assigned developer</b></label>
													<br>						
													<select id="tdeveloper" name="tdeveloper"> 
						                            	@foreach ($developers as $developer)
						                            	<option value="{{ $developer->id }}" {{ ( $ticket->developer_id == $developer->id) ? "selected" : "" }}>
						                            		{{ $developer->first_name }} {{ $developer->last_name }}
						                            	</option>
						                            	@endforeach
						                        	</select>
						                        </div>
						                        @else
						                        <div class="col"></div>
						                        @endif
						                    </div>
						                    <div class="row pb-1">
												<div class="col">
													<label><b>Created</b></label>
													<br>
													<div style="width: 250px">
														{{ $ticket->created_at }}
													</div>
												</div>
												<div class="col">
													<label><b>Updated</b></label>
													<br>
													<div style="width: 250px">
														{{ $ticket->updated_at }}
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
		                    </span>
						</span>
					</div>
					<div class="container">
						<div class="row pt-3 pb-2">
							<div class="col">
								<label><b>Ticket title</b></label>
								<br>
								{{ $ticket->title }}
							</div>
							<div class="col">
								<label><b>Ticket description</b></label>
								<br>
								{{ $ticket->description }}
							</div>
						</div>
						<div class="row pb-2">
							<div class="col">
								<label><b>Project</b></label>
								<br>
								{{ $ticket->project['name'] }}
							</div>
							<div class="col">
								<label><b>Submitter</b></label>
								<br>
								{{ $ticket->submitter['first_name'] }} {{ $ticket->submitter['last_name'] }}
							</div>
						</div>
						<div class="row pb-2">
							<div class="col">
								<label><b>Ticket type</b></label>
								<br>
								{{ $ticket->type }}
							</div>
							<div class="col">
								<label><b>Ticket priority</b></label>
								<br>
								{{ $ticket->priority }}
							</div>
						</div>
						<div class="row pb-2">
							<div class="col">
								<label><b>Ticket status</b></label>
								<br>
								{{ $ticket->status }}
							</div>					
							<div class="col">
								<label><b>Assigned developer</b></label>
								<br>
								{{ $ticket->developer['first_name'] }} {{ $ticket->developer['last_name'] }}
							</div>
						</div>
						<div class="row pb-3">
							<div class="col">
								<label><b>Created</b></label>
								<br>
								{{ $ticket->created_at }}
							</div>
							<div class="col">
								<label><b>Updated</b></label>
								<br>
								{{ $ticket->updated_at }}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div> 

		<div class="col-xxl">  
			<div class="d-flex">	
				<div id="tableContainer">								
					<div class="bg-warning text-white p-2 border">
						<span style="font-size: 18px">
							Ticket History
						</span>
						<br>
						<span style="font-size: 14px">
							All history information for this ticket
						</span>
					</div>
	                <div>
	                    <div class="d-flex justify-content-between">
	                        <div class="p-2">
	                            <form action="{{ route('tickets.show', [$ticket->id]) }}" method="get">
	                                <input type="hidden" name="currentQuery" value="{{ $searchText }}">
	                                <span>
	                                	Show
	                                </span> 
	                                <select style="width: 50px" id="entriesPerPage" name="entriesPerPage" onchange="this.form.submit()">
	                                    <option value="5" {{ ( $histories->perPage() == "5") ? "selected" : "" }}>
	                                    	5
	                                    </option>
	                                    <option value="10" {{ ( $histories->perPage() == "10") ? "selected" : "" }}>
	                                    	10
	                                    </option>
	                                    <option value="15" {{ ( $histories->perPage() == "15") ? "selected" : "" }}>
	                                    	15
	                                    </option>
	                                </select>
	                                <span>
	                                	entries
	                                </span> 
	                            </form>
	                        </div>
	                        <div class="p-2">
	                            <form action="{{ route('tickets.show', [$ticket->id]) }}" method="get">
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
	                                <th>@sortablelink('property', 'Property')</th>
	                                <th>@sortablelink('old_value', 'Old Value')</th>
	                                <th>@sortablelink('new_value', 'New Value')</th>
	                                <th>@sortablelink('updated_at', 'DateChanged')</th>
	                            </tr>
	                        </thead>
	                        <tbody>
	                            @forelse ($histories as $history)
	                            <tr>
	                                <td>{{ $history->property }}</td>
	                                <td>{{ $history->old_value }}</td>
	                                <td>{{ $history->new_value }}</td>
	                                <td>{{ $history->updated_at }}</td>
	                            </tr>
	                            @empty
	                            	<td colspan="4" align="center">
	                            		No data available.
	                            	</td>
	                            @endforelse 
	                        </tbody>
	                        <tfoot>
	                            <tr>
	                                <td colspan="2">
	                                	Showing {{ $histories->firstItem() ?? 0 }} to {{ $histories->lastItem() ?? 0 }} of {{ $histories->total() }} entries
	                                </td>
	                                <td class="absolute">{{ $histories->links() }}</td>
	                            </tr>
	                        </tfoot>    
	                    </table>
	                </div>                        
				</div>   
			</div> 
		</div>	
	</div>
	<div class="row">
		<div class="col-xxl">
			<div class="d-flex-column">
				<label class="p-2 pt-5" for="commentform">
					Add a comment?
				</label>
				<form class="px-2 py-3" id="commentform" name="commentform" action="{{ route('addComment') }}" method="post">
				{{ csrf_field() }}
					<input type="hidden" id="tid" name="tid" value="{{ $ticket->id }}">
					<input type="hidden" id="tcommenterid" name="tcommenterid" value="{{ session('LoggedUserId') }}">
					<input type="text" id="tcomment" name="tcomment">
					@if (session("LoggedUserId") != 3)
						<input type="submit" value="ADD" style="background-color: darkturquoise; color: white">
					@else
						<input type="submit" value="ADD" style="background-color: darkturquoise; color: white" disabled>
					@endif
				</form>
			</div>
			<div class="d-flex">	
				<div id="tableContainer">								
					<div class="bg-warning text-white p-2 border">
						<span style="font-size: 18px">
							Ticket Comments
						</span>
						<br>
						<span style="font-size: 14px">
							All comments for this ticket
						</span>
					</div>
	                <div>
	                    <div class="d-flex justify-content-between">
	                        <div class="p-2">
	                            <form action="{{ route('tickets.show', [$ticket->id]) }}" method="get">
	                                <input type="hidden" name="currentQuery_Comments" value="{{ $searchText_Comments }}">
	                                <span>
	                                	Show
	                                </span> 
	                                <select style="width: 50px" id="entriesPerPage_Comments" name="entriesPerPage_Comments" onchange="this.form.submit()">
	                                    <option value="5" {{ ( $comments->perPage() == "5") ? "selected" : "" }}>
	                                    	5
	                                    </option>
	                                    <option value="10" {{ ( $comments->perPage() == "10") ? "selected" : "" }}>
	                                    	10
	                                    </option>
	                                    <option value="15" {{ ( $comments->perPage() == "15") ? "selected" : "" }}>
	                                    	15
	                                    </option>
	                                </select>
	                                <span>
	                                	entries
	                                </span> 
	                            </form>
	                        </div>
	                        <div class="p-2">
	                            <form action="{{ route('tickets.show', [$ticket->id]) }}" method="get">
	                                <input type="text" name="query_Comments"  onfocus="this.value=''" value="{{ $searchText_Comments }}">
	                                <button type="submit">
	                                	<i class="material-icons align-bottom">search</i>
	                                </button>
	                            </form>
	                        </div>
	                    </div>  
	                    <table class="table table-condensed table-hover">
	                        <thead>
	                            <tr>
	                                <th>@sortablelink('last_name_commenter', 'Commenter')</th>
	                                <th>@sortablelink('remark', 'Message')</th>
	                                <th>@sortablelink('created_at', 'Created')</th>
	                            </tr>
	                        </thead>
	                        <tbody>
	                            @forelse ($comments as $comment)
	                            <tr>
	                                <td>{{ $comment->commenter['first_name'] }} {{ $comment->commenter['last_name'] }}</td>
	                                <td>{{ $comment->remark }}</td>
	                                <td>{{ $comment->created_at }}</td>
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
	                                	Showing {{ $comments->firstItem() ?? 0 }} to {{ $comments->lastItem() ?? 0 }} of {{ $comments->total() }} entries
	                                </td>
	                                <td class="absolute">{{ $comments->links() }}</td>
	                            </tr>
	                        </tfoot>    
	                    </table>
	                </div>                        		
				</div>   
			</div> 			
		</div>

		<div class="col-xxl">
			<div class="d-flex-column">
				<label class="p-2 pt-5" for="attachmentform">
					Add an attachment?
				</label>
				<form class="px-2 pb-2" id="attachmentform" name="attachmentform" action="{{ route('uploadAttachment') }}" method="post" enctype="multipart/form-data">
				{{ csrf_field() }}
					<input type="hidden" id="tid" name="tid" value="{{ $ticket->id }}">
					<input type="hidden" id="tuploaderid" name="tuploaderid" value="{{ session('LoggedUserId') }}">
					<div class="d-flex justify-content-around">
						<div class="d-flex flex-column">
							<label for="tfile">
								Select File:
							</label>
							<input type="file" id="tfile" name="tfile" style="width: 80px">
						</div>
						<div class="d-flex-column">
							<label for="tfiledescription">
								Add a description:
							</label>
							<div class="d-flex">
								<input type="text" id="tfiledescription" name="tfiledescription" style="width: auto">
								@if (session('LoggedUserId') != 3)
									<input type="submit" value="UPLOAD FILE" style="background-color: darkturquoise; color: white">
								@else
									<input type="submit" value="UPLOAD FILE" style="background-color: darkturquoise; color: white" disabled>
								@endif
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="d-flex">	
				<div id="tableContainer">								
					<div class="bg-warning text-white p-2 border">
						<span style="font-size: 18px">
							Ticket Attachments
						</span>
						<br>
						<span style="font-size: 14px">
							All files attached to this ticket
						</span>
					</div>
	                <div>
	                    <div class="d-flex justify-content-between">
	                        <div class="p-2">
	                            <form action="{{ route('tickets.show', [$ticket->id]) }}" method="get">
	                                <input type="hidden" name="currentQuery_Attachments" value="{{ $searchText_Attachments }}">
	                                <span>
	                                	Show
	                                </span> 
	                                <select style="width: 50px" id="entriesPerPage_Attachments" name="entriesPerPage_Attachments" onchange="this.form.submit()">
	                                    <option value="5" {{ ( $attachments->perPage() == "5") ? "selected" : "" }}>
	                                    	5
	                                    </option>
	                                    <option value="10" {{ ( $attachments->perPage() == "10") ? "selected" : "" }}>
	                                    	10
	                                    </option>
	                                    <option value="15" {{ ( $attachments->perPage() == "15") ? "selected" : "" }}>
	                                    	15
	                                    </option>
	                                </select>
	                                <span>
	                                	entries
	                                </span> 
	                            </form>
	                        </div>
	                        <div class="p-2">
	                            <form action="{{ route('tickets.show', [$ticket->id]) }}" method="get">
	                                <input type="text" name="query_Attachments"  onfocus="this.value=''" value="{{ $searchText_Attachments }}">
	                                <button type="submit">
	                                	<i class="material-icons align-bottom">search</i>
	                                </button>
	                            </form>
	                        </div>
	                    </div>  
	                    <table class="table table-condensed table-hover">
	                        <thead>
	                            <tr>
	                            	<th>@sortablelink('attachment', 'File')</th>
	                                <th>@sortablelink('last_name_uploader', 'Uploader')</th>
	                                <th>@sortablelink('description', 'Notes')</th>
	                                <th>@sortablelink('created_at', 'Created')</th>
	                            </tr>
	                        </thead>
	                        <tbody>
	                            @forelse ($attachments as $attachment)
	                            <tr>
	                            	<td>
	                            		<a href="{{ route('downloadAttachment', [$attachment->id]) }}">{{ $attachment->path }}
	                            		</a>
	                            	</td>
	                                <td>{{ $attachment->uploader['first_name'] }} {{ $attachment->uploader['last_name'] }}</td>
	                                <td>{{ $attachment->description }}</td>
	                                <td>{{ $attachment->created_at }}</td>
	                            </tr>
	                            @empty
	                            	<td colspan="4" align="center">
	                            		No data available.
	                            	</td>
	                            @endforelse 
	                        </tbody>
	                        <tfoot>
	                            <tr>
	                                <td colspan="2">
	                                	Showing {{ $attachments->firstItem() ?? 0 }} to {{ $attachments->lastItem() ?? 0 }} of {{ $attachments->total() }} entries
	                                </td>
	                                <td class="absolute">{{ $attachments->links() }}</td>
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
        $('#editTicket').dropdown('toggle');
    });
    </script>
@endif   
@endsection