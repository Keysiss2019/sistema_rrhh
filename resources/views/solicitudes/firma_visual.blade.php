@if(isset($firma) && $firma)
    <img src="data:image/png;base64,{{ base64_encode(is_resource($firma->imagen_path) ? stream_get_contents($firma->imagen_path) : $firma->imagen_path) }}" style="max-height:70px;">
@else
    <span style="color:#ccc;font-size:10px;">{{ $label }}</span>
@endif