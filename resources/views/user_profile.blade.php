@extends('layouts.master')
@section('title', 'User Profile')
@section('content')

<div class="row gutters-sm">
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center text-center">
                	<img src="{{ URL('storage/images/user_pictures/'.$user->picture) }}" alt="User Image" class="user-image" onerror="this.onerror=null; this.src='{{ URL('storage/images/user_pictures/'.'user_icon.jpg') }}'" width="150">
                	<p class="lead mt-3">{{ $user->first_name }} {{ $user->last_name }}</p>
                	<p class="text-secondary mb-1">{{ $user->role }}</p>
                	<p class="text-muted font-size-sm">{{ $user->province }}, {{ $user->country }}</p>

                    <p><button style="text-decoration: none" data-bs-toggle="collapse" data-bs-target="#uploadPicture">Upload picture?</button></p>
                    <div class="collapse" id="uploadPicture">
                        <form action="{{ route('uploadPicture') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                            <div class="d-flex flex-column align-items-center">
                                <label for="picture">Select your picture:</label>
                                <input class="py-2 w-100" type="file" id="picture" name="picture">
                                @if (session('LoggedUserId') != 17 && session('LoggedUserRole') != null)
                                    <button class="btn btn-secondary" type="submit">Upload</button>
                                @else
                                    <button class="btn btn-secondary" type="submit" disabled>Upload</button>
                                @endif
                                <span class="text-danger">@error('picture') {{ $message }} @enderror</span>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card mb-3">

            <div class="card-body" id="div1" style="display: block">
                <div class="row">
                    <div class="col-sm-3">
                      	<h6 class="mb-0">First Name</h6>
                    </div>
                    <div class="col-sm-9">                    	
                        <input style="border: none" type="text" value="{{ $user->first_name }}" disabled>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <h6 class="mb-0">Last Name</h6>
                    </div>
                    <div class="col-sm-9">
                        <input style="border: none" type="text" value="{{ $user->last_name }}" disabled>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                      	<h6 class="mb-0">Email</h6>
                    </div>
                    <div class="col-sm-9">
                        <input style="border: none" type="text" value="{{ $user->email }}" disabled>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                      	<h6 class="mb-0">Phone</h6>
                    </div>
                    <div class="col-sm-9">
                        <input style="border: none" type="text" value="{{ $user->phone }}" disabled>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                      	<h6 class="mb-0">Mobile</h6>
                    </div>
                    <div class="col-sm-9">
                        <input style="border: none" type="text" value="{{ $user->mobile }}" disabled>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <h6 class="mb-0">City</h6>
                    </div>
                    <div class="col-sm-9">
                        <input style="border: none" type="text" value="{{ $user->city }}" disabled>      
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <h6 class="mb-0">Province</h6>
                    </div>
                    <div class="col-sm-9">
                        <input style="border: none" type="text" value="{{ $user->province }}" disabled>      
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <h6 class="mb-0">Country</h6>
                    </div>
                    <div class="col-sm-9">
                        <input style="border: none" type="text" value="{{ $user->country }}" disabled>      
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                      	<a class="btn btn-secondary" id="link1" href="#">Edit</a>
                    </div>
                </div>
            </div>

            <div class="card-body" id="div2" style="display: none">
                <form action="{{ route('editUserProfile' )}}" method="post">
                {{ csrf_field() }}      
                    <div class="row">
                        <div class="col-sm-3">
                            <h6 class="mb-0">First Name</h6>
                        </div>
                        <div class="col-sm-9">
                            <input class="text-secondary" type="text" id="firstname" name="firstname" onfocus="this.value=''" value="{{ $user->first_name }}">
                        </div>
                        <span class="text-danger">@error('firstname') {{ 'The first name field is required.' }} @enderror</span>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Last Name</h6>
                        </div>
                        <div class="col-sm-9">
                            <input class="text-secondary" type="text" id="lastname" name="lastname" onfocus="this.value=''" value="{{ $user->last_name }}">
                        </div>
                        <span class="text-danger">@error('lastname') {{ 'The last name field is required.' }} @enderror</span>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Email</h6>
                        </div>
                        <div class="col-sm-9">
                            <input class="text-secondary" type="email" id="email" name="email" onfocus="this.value=''" value="{{ $user->email }}">
                        </div>
                        <span class="text-danger">@error('email') {{ $message }} @enderror</span>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Phone</h6>
                        </div>
                        <div class="col-sm-9">
                            <input class="text-secondary" type="tel" id="phone" name="phone" onfocus="this.value=''" value="{{ $user->phone }}">
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Mobile</h6>
                        </div>
                        <div class="col-sm-9">
                            <input class="text-secondary" type="tel" id="mobile" name="mobile" onfocus="this.value=''" value="{{ $user->mobile }}">
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <h6 class="mb-0">City</h6>
                        </div>
                        <div class="col-sm-9">
                            <input class="text-secondary" type="text" id="city" name="city" onfocus="this.value=''" value="{{ $user->city }}">      
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Province</h6>
                        </div>
                        <div class="col-sm-9">
                            <input class="text-secondary" type="text" id="province" name="province" onfocus="this.value=''" value="{{ $user->province }}">      
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Country</h6>
                        </div>
                        <div class="col-sm-9">
                            <input class="text-secondary" type="text" id="country" name="country" onfocus="this.value=''" value="{{ $user->country }}">      
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            @if (session('LoggedUserId') != 17 && session('LoggedUserRole') != null)
                                <button class="btn btn-secondary" type="submit" id="link2">Update</button>
                            @else
                                <button class="btn btn-secondary" type="submit" id="link2" disabled>Update</button>
                            @endif
                        </div>
                    </div>
                </form> 
            </div>


        </div>
    </div>

 

</div>

<script type="text/javascript">
    let div1 = document.getElementById('div1');
    let div2 = document.getElementById('div2');
    let link1 = document.getElementById('link1');
//    let link2 = document.getElementById('link2');

    link1.addEventListener("click", function () {
      div1.style.display = "none";
      div2.style.display = "block";
    })

//    link2.addEventListener("click", function () {
//      div2.style.display = "none";
//      div1.style.display = "block";
//    })
</script>

@endsection