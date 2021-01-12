    
    @extends('layouts.app')

    @section('content')
          
        <h2>{{$title}}</h2>

        
            <Section>                        
                <form action="{{route('GetInvoices')}}" method="post">

                    @csrf

                    <label>Date range <input type="date" name="StartDate"/> - <input type="date" name="EndDate"/> </label>

                    <label>Status
                        
                        <select id="Inv_Status" name="Inv_Status">

                            <option selected>All</option>
            
                            @foreach ($Query as $row)
                                    <option> {{$row->status}} </option> 
                            @endforeach
            
                        </select>  
                    
                    </label>

                    <label>Location

                        <select name="Inv_location">

                            <option selected>All</option>
            
                            @foreach ($Query as $row)
                                    <option value="{{$row->LocationId}}"> {{$row->Location}} </option> 
                            @endforeach
            
                        </select>   
                    </label>
                            
                    <input type="submit" value="Go" />

                </form>

            </Section>

            <Section>

                <h2>Invoice amount by loction</h2>

                <form action="{{route('Getamountbylocation')}}" method="post">

                    @csrf
            
                    <select name="Inv_location">
                        
                        <option selected>All</option>

                        @foreach ($Query as $row)
                                <option value="{{$row->LocationId}}"> {{$row->Location}} </option> 
                        @endforeach

                    </select>

                    <input type="submit" value="Go" />
                </form>

            </section>


    @endsection