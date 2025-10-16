@include('layouts.app', [
    'slot' => $slot,
    'header' => isset($header) ? $header : null,
])
