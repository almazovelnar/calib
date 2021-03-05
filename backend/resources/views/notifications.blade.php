@extends('layout')

@section('content')
    <div class="content">
        <div class="container pd-x-0 pd-lg-x-10 pd-xl-x-0">
            <div class="media d-block d-lg-flex">
                <div class="media-body">
                    <div class="timeline-group tx-13">
                        <br><br><br>
                        @if(auth()->user()->hasPermissionFor('notification_delete'))
                            <form action="{{ route('clear-notifications') }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <input type="submit" class="btn btn-danger" value="Delete all notifications">
                            </form>
                        @endif
                        <br><br><br>

                        @foreach(auth()->user()->notifications as $notification)
                            <div class="timeline-item">
                                <div class="timeline-time">{{ $notification->created_at->format('d M H:i') }}</div>
                                <div class="timeline-body {{ $notification->read_at ? '' : 'red' }}">
                                    @if($notification->read_at)
                                        <p>{{ $notification->data['text'] }}</p>
                                    @else
                                        <p><b>{{ $notification->data['text'] }}</b></p>
                                    @endif
                                </div><!-- timeline-body -->
                            </div><!-- timeline-item -->

                            @php($notification->markAsRead())
                        @endforeach
                    </div><!-- timeline-group -->
                </div><!-- media-body -->
            </div><!-- media -->
        </div><!-- container -->
    </div><!-- content -->
@endsection
