@extends('layouts.master')
@section('title', 'Project Assignment')
@section('content')
@livewire('project-team', ['pid'=> $pid])
@endsection