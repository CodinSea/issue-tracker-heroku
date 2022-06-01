<div>
    <div class="modal fade" id="notifications" style="font-size: 14px !important">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="border-bottom pb-2">                  
                        <h6 class="modal-title">Unread messages</h6>
                        @forelse ($currentUser->unreadNotifications as $unreadNotification)
                        <div class="container">
                            <div class="row py-1">   
                                <div class="col">
                                    <div>{{ $unreadNotification->data['message'] }}</div>
                                    <div class="text-secondary px-2"><small>{{ $unreadNotification->created_at->diffForHumans() }}</small></div>
                                </div>
                                <div class="col-3 px-0">
                                    <button wire:click="markMessageAsRead('{{ $unreadNotification->id }}')" class="markAsReadButton">Mark as read</button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <a class="text-decoration-none text-secondary">No notifications found.</a>
                        @endforelse
                    </div>
                    <div class="pt-2">
                        <h6 class="modal-title">Read messages</h6>
                        @forelse ($currentUser->readNotifications as $readNotification)
                        <div class="container">
                            <div class="row py-1">   
                                <div class="col">
                                    <div>{{ $readNotification->data['message'] }}</div>
                                    <div class="text-secondary px-2"><small>{{ $readNotification->created_at->diffForHumans() }}</small></div>
                                </div>
                                <div class="col-2 px-0">
                                    <button wire:click="deleteMessage('{{ $readNotification->id }}')">Delete</button>
                                </div>                                
                            </div>
                        </div>
                        @empty
                        <a class="text-decoration-none text-secondary">No notifications found.</a>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button wire:click="markAllRead" type="button" class="btn btn-secondary">Mark all read</button>
                    <button wire:click="deleteAllReadMessages" type="button" class="btn btn-secondary">Delete all read messages</button>
                </div>
            </div>
        </div>
    </div>     
</div> 
@if (session()->has('message'))
    <script>
    $(document).ready(function() {
        $('#notifications').modal('show');
    });
    </script>
@endif      