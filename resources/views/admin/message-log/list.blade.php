@extends('layouts.appadminlayout')
@section('title', 'Running List')
@section('content')
<div class="container">
	<table id="message-logs" class="table table-striped table-condensed table-hover">
        <thead>
            <tr>
                <th>Type</th>
                <th>Message</th>
                <th>Created At</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($messageLogs as $i)
                <tr style="font-weight: bold">
    				<td>{{$i["type"]}}</td>
                    <td>{{$i["message"]}}</td>
                    <td>{{\Carbon\Carbon::parse($i["created_at"])->format('j F, Y')}}</td>
                    <td>{{$i["status"]}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection