@extends('layouts.master')
@section('title', 'Role Assignment')
@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col"> 
			<form action="{{ route('assignRole') }}" method="post">
			{{ csrf_field() }}
				<div class="container-fluid">	
		  			<div class="row p-3">
						<div class="col-auto">  
							<div>
								<label for="users">
									Select one or more users:
								</label>
								<div class="p-2">
									<select id="users" name="assigned_userlist[]" multiple>
										@foreach ($allUsers as $user)
										<option value="{{ $user->id }}">
											{{ $user->first_name }} {{ $user->last_name }}
										</option>
										@endforeach 
									</select>
									<div class="text-danger">
										@error('assigned_userlist') 
											{{ 'You should select one or more users.' }} 
										@enderror
					                </div>
								</div>
							</div>							
						</div>
						<div class="col-auto"> 
							<div>
								<label for="roles">
									Select the role to assign:
								</label>
								<div class="p-2">
									@if (session('LoggedUserId') != 3)
									<select id="roles" name="assigned_role" onchange="this.form.submit()">
									@else
									<select id="roles" name="assigned_role">
									@endif
										<option disabled selected value> 
											-- Select Role/None -- 
										</option>
										<option>
											Admin
										</option>
										<option>
											Project manager
										</option>
										<option>
											Developer
										</option>
										<option>
											Submitter
										</option>
										<option value="">
											None
										</option> 
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>  
			</form>
		</div>

		<div class="col"> 
			<form action="{{ route('deleteUser') }}" method="post">
			{{ csrf_field() }}
				<div class="container-fluid">	
		  			<div class="row p-3">
						<div class="col-auto">  
							<div>
								<label for="usersToDelete">
									Select one or more users:
								</label>
								<div class="p-2">
									<select id="usersToDelete" name="toBeDeleted_userlist[]" multiple>
										@foreach ($allUsers as $user)
										<option value="{{ $user->id }}">
											{{ $user->first_name }} {{ $user->last_name }}
										</option>
										@endforeach 
									</select>
									<div class="text-danger">
										@error('toBeDeleted_userlist') 
											{{ 'You should select one or more users.' }} 
										@enderror
					                </div>
								</div>
							</div>							
						</div>
						<div class="col-auto"> 
							<div>
								<label for="delete">
									Click to delete the selected users
								</label>
								<div class="p-2">
									@if (session('LoggedUserId') != 3)
                                        <button type="submit" id="delete" onclick="this.form.submit()">Delete</button>
                                    @else
                                        <button type="submit" id="delete" disabled>Delete</button>
                                    @endif	
								</div>
							</div>
						</div>
					</div>
				</div>  
			</form>
		</div>

	</div>
	<div class="row">
		<div class="col">
			<div class="d-flex">
				<div id="tableContainer">								
					<div class="bg-warning text-white p-2">
						<span style="font-size: 18px">
							Your Personnel
						</span>
						<br>
						<span style="font-size: 14px">
							All the users in your database
						</span>
					</div>							
					<div>
						<div class="d-flex justify-content-between">
							<div class="p-2">
								<form action="{{ route('roleAssignment') }}" method="get">
									<input type="hidden" name="currentQuery" value="{{ $searchText }}">
									<span>
										Show
									</span> 
									<select style="width: 50px" id="entriesPerPage" name="entriesPerPage" onchange="this.form.submit()">
										<option value="5" {{ ( $users->perPage() == "5") ? "selected" : "" }}>
											5
										</option>
										<option value="10" {{ ( $users->perPage() == "10") ? "selected" : "" }}>
											10
										</option>
										<option value="15" {{ ( $users->perPage() == "15") ? "selected" : "" }}>
											15
										</option>
									</select>
									<span>
										entries
									</span> 
								</form>
							</div>
							<div class="p-2">
								<form action="{{ route('roleAssignment') }}" method="get">
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
								@forelse ($users as $user)
								<tr>
									<td>{{ $user->first_name }} {{ $user->last_name }}</td>
									<td>{{ $user->email }}</td>
									<td>{{ $user->role }}</td>
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
										Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries
									</td>
									<td class="absolute">{{ $users->links() }}</td>
								</tr>
							</tfoot>						
						</table>  
					</div>						
				</div>    							
			</div>
		</div>
	</div>	
</div>	
@endsection