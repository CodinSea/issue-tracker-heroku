<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
//use Illuminate\Http\Request;

class Notifications extends Component
{
    public function markMessageAsRead($id)
    {
        session('LoggedUser')->unreadNotifications->find($id)->markAsRead();
        session()->flash('message', 'The message has been successfully marked read.');
        return redirect('dashboard');  
    }

    public function deleteMessage($id)
    {
        session('LoggedUser')->readNotifications->find($id)->delete();
        session()->flash('message', 'The message has been successfully deleted.');
        return redirect('dashboard');
    }

    public function markAllRead()
    {
        session('LoggedUser')->unreadNotifications->markAsRead();
        session()->flash('message', 'All unread messages have been successfully marked read.');
        return redirect('dashboard'); 
    }

    public function deleteAllReadMessages()
    {
        session('LoggedUser')->readNotifications()->delete();
        session()->flash('message', 'All read messages have been successfully deleted.');
        return redirect('dashboard');
    }

    public function render()
    {
        return view('livewire.notifications')->with('currentUser', User::find(session('LoggedUserId')));
    }
}
