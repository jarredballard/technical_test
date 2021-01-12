@extends('layouts.app')

@section('content')

<ul>

    @if (strlen(trim($results)) < 1)

        @foreach (json_decode($results) as $key => $rows)

            <h2> {{$key}} </h2>

            <li> 
                @foreach ($rows as $row)
                <p>
                    @foreach ($row as $field)

                        {{$field}}

                    @endforeach
                </p>
                @endforeach
            </li>
        @endforeach

    @else
        <h1>No results</h1>     
    @endif

</ul>

@endsection