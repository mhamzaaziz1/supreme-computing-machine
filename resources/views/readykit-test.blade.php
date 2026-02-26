@extends('layouts.readykit')

@section('title', 'ReadyKit Integration Test')

@section('contents')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">ReadyKit Integration Successful</h4>
                <p class="card-description">
                    This page is rendered using the <code>layouts.readykit</code> layout.
                </p>
                <div class="alert alert-success">
                    If you see this green alert box, Bootstrap CSS is loading correctly (once assets are built).
                </div>
                <button class="btn btn-primary">Primary Button</button>
            </div>
        </div>
    </div>
</div>
@endsection
