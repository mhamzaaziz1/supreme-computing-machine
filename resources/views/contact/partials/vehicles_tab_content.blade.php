<div class="tab-pane
    @if(!empty($view_type) &&  $view_type == 'vehicles')
        active
    @else
        ''
    @endif"
    id="vehicles_tab">
    @include('contact.partials.vehicles_tab')
</div>