@if( session('status') )
    <div class="alert {{ session('status')['alert_status'] }} alert-dismissible fade show" role="alert">
        <strong>{{ session('status')['msg'] }}</strong>
        <p>
            {!! session('status')['pref'] !!}
        </p>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
