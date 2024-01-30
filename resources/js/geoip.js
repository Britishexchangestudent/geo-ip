import $ from 'jquery';

$(document).ready(function() {
    $('#ip').val('{{ $geoip["ip"] }}');
});
