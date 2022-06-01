<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Ticket;

class HomeController extends Controller
{
    public function index() {        
        $distinctStatusCount = Ticket::all()->countBy('status')->all();
        $ticketsByStatus = [];

        if (count($distinctStatusCount) > 0){
            foreach($distinctStatusCount as $key => $value) {                
                array_push($ticketsByStatus, [$key, $value]);
            }            
        }

        $distinctPriorityCount = Ticket::all()->countBy('priority')->all();
        $ticketsByPriority = [];

        if (count($distinctPriorityCount) > 0){
            foreach($distinctPriorityCount as $key => $value) {                
                array_push($ticketsByPriority, [$key, $value]);
            }            
        }
        
        $distinctTypeCount = Ticket::all()->countBy('type')->all();
        $ticketsByType = [];

        if (count($distinctTypeCount) > 0){
            foreach($distinctTypeCount as $key => $value) {                
                array_push($ticketsByType, [$key, $value]);
            }            
        }
        
        return view('dashboard')->with('ticketsByStatus', $ticketsByStatus)
                                ->with('ticketsByPriority', $ticketsByPriority)
                                ->with('ticketsByType', $ticketsByType);
    }

}
