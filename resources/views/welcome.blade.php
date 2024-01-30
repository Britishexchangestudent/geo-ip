<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GeoIP App</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>
    <h1>GeoIP Information</h1>

    @if(session('message'))
        <p>{{ session('message') }}</p>
    @endif

    @if($errors->has('ip'))
        <p style="color: red;">{{ $errors->first('ip') }}</p>
    @endif

    <form id="geoipForm" method="post" action="{{ route('geoip.query') }}">
        @csrf
        <label for="ip">Enter IP Address:</label>
        <input type="text" name="ip" id="ip" value="{{ old('ip', $userIp) }}">
        <button type="submit">Submit</button>
    </form>

    @if($geoip = Cache::get('geoip'))
        <h2>GeoIP Information:</h2>
        <p>IP Address: {{ $geoip['ip'] }}</p>
        <p>Country Code: {{ $geoip['country_code'] }}</p>
        <p>Country Name: {{ $geoip['country_name'] }}</p>

        <script>
            $(document).ready(function() {
                $('#ip').val('{{ $geoip["ip"] }}');
            });
        </script>
        @endif
        
</body>
</html>

