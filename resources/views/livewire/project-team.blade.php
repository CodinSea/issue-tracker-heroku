<div class="container-fluid">
     <div class="row px-5 py-3">
          <div class="col-auto"  style="max-width: 280px">
               <label for="projects">
                    Select the project:
               </label>
               <div class="pb-3">
                    <select wire:model="pid" id="projects" name="selected_project" form="project_assignment"> 
                         <option value=""> 
                              -- Select Project -- 
                         </option>
                         @forelse ($projects as $project)
                         <option value="{{ $project->id }}">
                              {{ $project->name }}
                         </option>
                         @empty
                         <option disabled>
                              No assigned projects
                         </option>                         
                         @endforelse
                    </select>
                    <br>
                    <span class="text-danger">
                         @error('selected_project') 
                              {{ 'The project field is required.' }} 
                         @enderror
                    </span>  
               </div>
               @if (session('LoggedUserRole') == 'Admin')   
               <label for="project_managers">
                    Select the manager:
               </label>
               <div class="pb-3">
                    <select id="project_managers" name="selected_project_manager" form="project_assignment">
                         <option disabled selected value> 
                              -- Select Manager/None -- 
                         </option> 
                         @foreach ($managers as $manager)
                         <option value="{{ $manager->id }}" {{ (old('selected_project_manager') == $manager->id) ? "selected" : "" }}>
                              {{ $manager->first_name }} {{ $manager->last_name }}
                         </option>
                         @endforeach
                    </select>
                    <br>
                    <span class="text-danger">
                         @error('selected_project_manager') 
                              {{ $message }} 
                         @enderror
                    </span>
               </div>
               @endif
               <label for="developers">
                    Select the developers:
               </label>
               <div class="pb-3">
                    <select id="developers" name="selected_developers[]" form="project_assignment" multiple> 
                         <option disabled selected value> 
                              -- Select Developer(s)/None -- 
                         </option>
                         @foreach ($developers as $developer)
                         <option value="{{ $developer->id }}" {{ (collect(old('selected_developers'))->contains($developer->id)) ? "selected" : "" }}>
                              {{ $developer->first_name }} {{ $developer->last_name }}
                         </option>
                         @endforeach    
                    </select>
                    <br>
                    <span class="text-danger">
                         @error('selected_developers.*') 
                              {!! $message !!} 
                         @enderror
                    </span>
               </div>
               <label for="submitters">
                    Select the submitters:
               </label>
               <div class="pb-3">
                    <select id="submitters" name="selected_submitters[]" form="project_assignment" multiple> 
                         <option disabled selected value> 
                              -- Select Submitter(s)/None -- 
                         </option>
                         @foreach ($submitters as $submitter)
                         <option value="{{ $submitter->id }}" {{ (collect(old('selected_submitters'))->contains($submitter->id)) ? "selected" : "" }}>
                              {{ $submitter->first_name }} {{ $submitter->last_name }}
                         </option>
                         @endforeach
                    </select>
                    <br>
                    <span class="text-danger">
                         @error('selected_submitters.*') 
                              {!! $message !!} 
                         @enderror
                    </span>          
               </div>
               <div>
                    <form id="project_assignment" action="{{ route('assignProject') }}" method="post"> 
                    {{ csrf_field() }}
                         @if (session('LoggedUserId') != 17)
                              <button style="float: right" type="submit">Assign</button> 
                         @else
                              <button style="float: right" type="submit" disabled>Assign</button> 
                         @endif   
               <!--      <button wire:click="assignProject">Assign</button>    -->     </form>
               </div>
          </div>
          <div class="col-auto p-5">
               @if (count($projectManagers) != 0 || count($projectDevelopers) != 0 || count($projectSubmitters) != 0)
               <div class="pb-1">
                    <h5><b>Project Team</b></h5>
               </div> 
               <div class="d-flex">   
                    @foreach ($projectManagers as $projectManager)
                    <div class="d-flex p-2">
                         <div class="d-flex">
                              <img src="{{ URL('storage/images/user_pictures/'.$projectManager->picture) }}" alt="User Image" onerror="this.onerror=null; this.src='{{ URL('storage/images/user_pictures/'.'user_icon.jpg') }}'" class="rounded-circle" width="60">
                         </div>
                         <div class="d-flex flex-column justify-content-center px-3 border rounded">
                              <span>
                                   {{ $projectManager->first_name }} {{ $projectManager->last_name }} 
                                   <button wire:click="remove({{ $projectManager->id }})">
                                        <i class="fa fa-remove"></i>
                                   </button>
                              </span>
                              {{ $projectManager->role }}
                         </div>
                    </div>
                    @endforeach
               </div>   
               <div class="d-flex"> 
                    @foreach ($projectDevelopers as $projectDeveloper)
                    <div class="d-inline-flex p-2">
                         <div class="d-flex">
                              <img src="{{ URL('storage/images/user_pictures/'.$projectDeveloper->picture) }}" alt="User Image" onerror="this.onerror=null; this.src='{{ URL('storage/images/user_pictures/'.'user_icon.jpg') }}'" class="rounded-circle" width="60">
                         </div>
                         <div class="d-flex flex-column justify-content-center px-3 border rounded">
                              <span>
                                   {{ $projectDeveloper->first_name }} {{ $projectDeveloper->last_name }} 
                                   <button wire:click="remove({{ $projectDeveloper->id }})">
                                        <i class="fa fa-remove"></i>
                                   </button>
                              </span>
                              {{ $projectDeveloper->role }}
                         </div>
                    </div>
                    @endforeach 
               </div>
               <div class="d-flex"> 
                    @foreach ($projectSubmitters as $projectSubmitter)
                    <div class="d-inline-flex p-2">
                         <div class="d-flex">
                              <img src="{{ URL('storage/images/user_pictures/'.$projectSubmitter->picture) }}" alt="User Image" onerror="this.onerror=null; this.src='{{ URL('storage/images/user_pictures/'.'user_icon.jpg') }}'" class="rounded-circle" width="60">
                         </div>
                         <div class="d-flex flex-column justify-content-center px-3 border rounded">
                              <span>
                                   {{ $projectSubmitter->first_name }} {{ $projectSubmitter->last_name }} 
                                   <button wire:click="remove({{ $projectSubmitter->id }})">
                                        <i class="fa fa-remove"></i>
                                   </button>
                              </span>
                              {{ $projectSubmitter->role }}
                         </div>
                    </div>
                    @endforeach
               </div>
               @endif   
          </div>
     </div>
</div>