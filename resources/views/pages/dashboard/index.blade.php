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

<div class="row">
    <div class="col-sm-6 col-xl-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Revenue</h5>
                <div id="apex1"></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card stat-widget">
            <div class="card-body">
                <h5 class="card-title">Social Media</h5>
                <div class="transactions-list">
                    <div class="tr-item">
                        <div class="tr-company-name">
                            <div class="tr-icon tr-card-icon tr-card-bg-primary text-primary">
                                <i data-feather="thumbs-up"></i>
                            </div>
                            <div class="tr-text">
                                <h4>New post reached 7k+ likes</h4>
                                <p>02 March</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="transactions-list">
                    <div class="tr-item">
                        <div class="tr-company-name">
                            <div class="tr-icon tr-card-icon tr-card-bg-info text-info">
                                <i data-feather="twitch"></i>
                            </div>
                            <div class="tr-text">
                                <h4>Developer AMA is now live</h4>
                                <p>01 March</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="transactions-list">
                    <div class="tr-item">
                        <div class="tr-company-name">
                            <div class="tr-icon tr-card-icon tr-card-bg-danger text-danger">
                                <i data-feather="instagram"></i>
                            </div>
                            <div class="tr-text">
                                <h4>52 unread messages</h4>
                                <p>23 February</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="transactions-list">
                    <div class="tr-item">
                        <div class="tr-company-name">
                            <div class="tr-icon tr-card-icon tr-card-bg-warning text-warning">
                                <i data-feather="shopping-bag"></i>
                            </div>
                            <div class="tr-text">
                                <h4>2 new orders from shop page</h4>
                                <p>17 February</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="transactions-list">
                    <div class="tr-item">
                        <div class="tr-company-name">
                            <div class="tr-icon tr-card-icon tr-card-bg-info text-info">
                                <i data-feather="twitter"></i>
                            </div>
                            <div class="tr-text">
                                <h4>Hashtag #circl is trending</h4>
                                <p>03 February</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('custom-scripts')
        <!-- Javascripts -->
        <script src="{{asset('assets/plugins/jquery/jquery-3.4.1.min.js')}}"></script>
        <script src="https://unpkg.com/@popperjs/core@2"></script>
        <script src="{{asset('assets/plugins/bootstrap/js/bootstrap.min.js')}}"></script>
        <script src="https://unpkg.com/feather-icons"></script>
        <script src="{{asset('assets/plugins/perfectscroll/perfect-scrollbar.min.js')}}"></script>
        <script src="{{asset('assets/js/main.min.js')}}"></script>
@endpush









