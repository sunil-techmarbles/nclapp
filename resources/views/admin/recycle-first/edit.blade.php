@extends('layouts.appadminlayout')
@section('title', 'Edit Recycle')
@section('content')
<div class="container">
	@if (!empty($allRecords))
        <table class="table">
            <thead>
                <tr>
                    <th>Pallet</th>
                    <th>Scrap Category</th>
                    <th>Lbs.Gross</th>
                    <th>Lbs. Tare</th>
                    <th>Price/Lb</th>
                    <th>Total Price</th>
                    <th>P/G/I</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($allRecords as $key => $record)
                    <tr>
                        <td class="id">{{ $key+1 }}</td>
                        <td class="category">{{trim($record['category'])}}</td>
                        <td class="lgross">{{ $record['lgross'] }}</td>
                        <td class="ltare">{{ $record['ltare'] }}</td>
                        <td class="price">{{ $record['price'] }}</td>
                        <td class="total_price">{{ $record['total_price'] }}</td>
                        <td class="pgi">{{ $record['pgi'] }}</td>
                        <td>
                        	<!--onclick="del_confirm({{$record['id']}},'deleterecyclerecord','Recycle Record')"-->
							<a href="javascript:void(0)" class="recycle-record-edit" data-record_id="{{ $record['id'] }}">
                            	<img src="{{URL('/assets/images/edit.png')}}" class="icons" title="Edit">
							</a>
							<a href="javascript:void(0)" onclick="del_confirm({{$record['id']}},'deleterecyclerecord','Recycle Record')" data-record_id="{{ $record['id'] }}" class="btn-link recyclerecord-delete">
								<img src="{{URL('/assets/images/del.png')}}" class="icons" title="Delete">
							</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @include('admin.recycle-first.modal', ['categories' => $categories])
    @endif
</div>
@endsection