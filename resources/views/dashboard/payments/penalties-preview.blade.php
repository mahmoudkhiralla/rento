@extends('dashboard.layouts.app')

@section('title', 'معاينة الغرامة أو التعويض')

@section('content')
    <div class="penalties-container">
        <div class="main-section" style="padding:24px;">
            @include('dashboard.payments.partials.penalties-modal')
            @include('dashboard.payments.partials.penalties-styles')
            @include('dashboard.payments.partials.penalties-scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    openPenaltyModal({{ $id }});
                });
            </script>
        </div>
    </div>
@endsection