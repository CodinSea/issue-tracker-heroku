@extends('layouts.master')
@section('title', 'Ticket Details')
@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-xl border">
			<div class="bg-warning text-white p-2 border">
				<span style="font-size: 18px">Ticket Details</span><br>
				<span style="font-size: 14px">
					<a href="{{ route('tickets') }}" style="font-stretch: condensed; color: inherit">Back to List</a> 
				</span>
			</div>
			<div class="container">
				<form action="{{ route('editTicket', [$ticket->id]) }}" method="post">
				{{ csrf_field() }}
					<div class="row row-cols-2 pt-3">
						<input type="hidden" id="tid" name="tid" value="{{ $ticket->id }}">
						<p><b>Ticket title</b><br>
							<input type="text" id="ttitle" name="ttitle" onfocus="this.value=''" value="{{ $ticket->title }}"></p>
						<p><b>Ticket description</b><br>
							<input type="text" id="tdescription" name="tdescription" onfocus="this.value=''" value="{{ $ticket->description }}"></p>
						<p><b>Assigned developer</b><br>						<select id="tdeveloper" name="tdeveloper"> 
                            	@foreach ($developers as $developer)
                            	<option value="{{ $developer->id }}" {{ ( $ticket->developer_id == $developer->id) ? "selected" : "" }}>{{ $developer->first_name }} {{ $developer->last_name }}</option>
                            	@endforeach
                        	</select>
						<p><b>Submitter</b><br>{{ $ticket->submitter['first_name'] }} {{ $ticket->submitter['last_name'] }}</p>
						<p><b>Project</b><br>{{ $ticket->project['name'] }}</p>
						<p><b>Ticket priority</b><br>
							<select id="tpriority" name="tpriority">
								<option value="High" {{ ( $ticket->priority == "High") ? "selected" : "" }}>High</option>
								<option value="Medium" {{ ( $ticket->priority == "Medium") ? "selected" : "" }}>Medium</option>
								<option value="Low" {{ ( $ticket->priority == "Low") ? "selected" : "" }}>Low</option>
							</select></p>
						<p><b>Ticket status</b><br>
							<select id="tstatus" name="tstatus">
								<option value="Open" {{ ( $ticket->status == "Open") ? "selected" : "" }}>Open</option>
								<option value="In progress" {{ ( $ticket->status == "In progress") ? "selected" : "" }}>In progress</option>
								<option value="In review" {{ ( $ticket->status == "In review") ? "selected" : "" }}>In review</option>
								<option value="Closed" {{ ( $ticket->status == "Closed") ? "selected" : "" }}>Closed</option>
							</select></p>
						<p><b>Ticket type</b><br>
							<select id="ttype" name="ttype">
								<option value="Bug report" {{ ( $ticket->type == "Bug report") ? "selected" : "" }}>Bug report</option>
								<option value="Feature request" {{ ( $ticket->type == "Feature request") ? "selected" : "" }}>Feature request</option>
							</select></p>
						<p><b>Created</b><br>{{ $ticket->created_at }}</p>
						<p><b>Updated</b><br>{{ $ticket->updated_at }}</p>
						<p class="align-self-end">
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
					<span style="font-size: 18px">Ticket History</span><br>
					<span style="font-size: 14px">All history information for this ticket</span>
				</div>							
				<div id="tBodyContainer">     
					<table class="table table-condensed table-hover">
						<thead>
							<tr>
								<th style="background: white; border-style: solid; position: sticky; top: 0;">Property</th>
								<th style="background: white; border-style: solid; position: sticky; top: 0;">Old Value</th>
								<th style="background: white; border-style: solid; position: sticky; top: 0;">New Value</th>
								<th style="background: white; border-style: solid; position: sticky; top: 0;">Date Changed</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($histories as $history)
                            <tr>
                                <td>{{ $history->property }}</td>
                                <td>{{ $history->old_value }}</td>
                                <td>{{ $history->new_value }}</td>
                                <td>{{ $history->updated_at }}</td>
                            </tr>
                            @endforeach
						</tbody>
					</table>
				</div>						
			</div>   
		</div> 
	</div>
	<div class="row">
		<div class="col-xl border">
			<div class="d-flex-column border">
				<label class="p-2 pt-5" for="commentform">Add a comment?</label>
				<form class="px-2 py-3" id="commentform" name="commentform" action="{{ route('addComment') }}" method="post">
				{{ csrf_field() }}
					<input type="hidden" id="tid" name="tid" value="{{ $ticket->id }}">
					<input type="hidden" id="tcommenterid" name="tcommenterid" value="{{ session('LoggedUserId') }}">
					<input type="text" id="tcomment" name="tcomment">
					<input type="submit" value="ADD" style="background-color: darkturquoise; color: white">
				</form>
			</div>
			<div class="d-flex border">	
				<div id="scrollTableContainer">								
					<div class="bg-warning text-white p-2 border">
						<span style="font-size: 18px">Ticket Comments</span><br>
						<span style="font-size: 14px">All comments for this ticket</span>
					</div>							
					<div id="tBodyContainer">     
						<table class="table table-condensed table-hover">
							<thead>
								<tr>
									<th style="background: white; border-style: solid; position: sticky; top: 0;">Commenter</th>
									<th style="background: white; border-style: solid; position: sticky; top: 0;">Message</th>
									<th style="background: white; border-style: solid; position: sticky; top: 0;">Created</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($comments as $comment)
                            	<tr>
                                	<td>{{ $comment->commenter['first_name'] }} {{ $comment->commenter['last_name'] }}</td>
                                	<td>{{ $comment->remark }}</td>
                                	<td>{{ $comment->created_at }}</td>
                            	</tr>
                            	@endforeach
							</tbody>
						</table>
					</div>						
				</div>   
			</div> 			
		</div>

		<div class="col-xl border">
			<div class="d-flex-column border">
				<label class="p-2 pt-5" for="attachmentform">Add an attachment?</label>
				<form class="px-2 pb-2" id="attachmentform" name="attachmentform" action="{{ route('uploadAttachment') }}" method="post">
					<input type="hidden" id="tid" name="tid" value="{{ $ticket->id }}">
					<input type="hidden" id="tuploaderid" name="tuploaderid" value="{{ session('LoggedUserId') }}">
					<div class="d-flex justify-content-around">
						<div class="d-flex flex-column">
							<label for="tfile">Select File:</label>
							<input type="file" id="tfile" name="tfile" style="width: 80px">
						</div>
						<div class="d-flex-column">
							<label for="tfiledescription">Add a description:</label>
							<div class="d-flex">
								<input type="text" id="tfiledescription" name="tfiledescription" style="width: auto">
								<input type="submit" value="UPLOAD FILE" style="background-color: darkturquoise; color: white">
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="d-flex border">	
				<div id="scrollTableContainer">								
					<div class="bg-warning text-white p-2 border">
						<span style="font-size: 18px">Ticket Attachments</span><br>
						<span style="font-size: 14px">All files attached to this ticket</span>
					</div>							
					<div id="tBodyContainer">     
						<table class="table table-condensed table-hover">
							<thead>
								<tr>
									<th style="background: white; border-style: solid; position: sticky; top: 0;">File</th>
									<th style="background: white; border-style: solid; position: sticky; top: 0;">Uploader</th>
									<th style="background: white; border-style: solid; position: sticky; top: 0;">Notes</th>
									<th style="background: white; border-style: solid; position: sticky; top: 0;">Created</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($attachments as $attachment)
                            	<tr>
                                	<td>{{ $attachment->attachment }}</td>
                                	<td>{{ $attachment->uploader['first_name'] }} {{ $attachment->uploader['last_name'] }}</td>
                                	<td>{{ $attachment->description }}</td>
                                	<td>{{ $attachment->created_at }}</td>
                            	</tr>
                            	@endforeach
							</tbody>
						</table>
					</div>						
				</div>   
			</div> 
		</div>
	</div>	 
</div>  
@endsection