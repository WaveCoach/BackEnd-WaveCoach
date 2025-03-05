@extends('layouts.default')

@section('content')
<div class="row">
    <div class="col-md-6 col-xl-3">
        <div class="card stat-widget">
            <div class="card-body">
                <h5 class="card-title">Location</h5>
                <h2>{{$location}}</h2>
                <p>From last week</p>
                <div class="progress">
                    <div class="progress-bar bg-info progress-bar-striped" role="progressbar"
                        style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-widget">
            <div class="card-body">
                <h5 class="card-title">Inventory</h5>
                <h2>{{$inventory}}</h2>
                <p>Orders in waitlist</p>
                <div class="progress">
                    <div class="progress-bar bg-success progress-bar-striped" role="progressbar"
                        style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-widget">
            <div class="card-body">
                <h5 class="card-title">Coaches</h5>
                <h2>{{$coaches}}</h2>
                <p></p>
                <div class="progress">
                    <div class="progress-bar bg-danger progress-bar-striped" role="progressbar"
                        style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-widget">
            <div class="card-body">
                <h5 class="card-title">MasterCoaches</h5>
                <h2>{{$mastercoach}}</h2>
                <p>Orders in waitlist</p>
                <div class="progress">
                    <div class="progress-bar bg-primary progress-bar-striped" role="progressbar"
                        style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-widget">
            <div class="card-body">
                <h5 class="card-title">Student</h5>
                <h2>{{$student}}</h2>
                <p>Orders in waitlist</p>
                <div class="progress">
                    <div class="progress-bar bg-primary progress-bar-striped" role="progressbar"
                        style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-widget">
            <div class="card-body">
                <h5 class="card-title">Schedule</h5>
                <h2>{{$schedule}}</h2>
                <p>Orders in waitlist</p>
                <div class="progress">
                    <div class="progress-bar bg-primary progress-bar-striped" role="progressbar"
                        style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection











