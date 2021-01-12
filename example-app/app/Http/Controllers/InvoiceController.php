<?php

namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;

    class InvoiceController extends Controller
    {

        public function index()
        {
            $query = DB::select('SELECT distinct(Location) , LocationId  , status FROM interview_tests.GetinvoiceList;');
            return view('welcome', ['Query' => $query, 'title' =>'"routines"']);
        }


        public function GetInvoices(Request $request)
        {

            $filters =[];    

            if(isset($request->StartDate) && isset($request->EndDate))
            {
                array_push($filters, " date BETWEEN = $request->StartDate AND $request->EndDate");          
            }
            else if(isset($request->StartDate))
            {
                array_push($filters, " date >  $request->StartDate"); 
            }
            else if(isset($request->EndDate))
            {
                array_push($filters, " date < $request->EndDate");                
            }

            if($request->Inv_location != 'All')
            {
                array_push($filters," location_id = $request->Inv_location");
            }

            if($request->Inv_Status != 'All')
            {
                array_push($filters," status = '$request->Inv_Status'");
            }

            $i = 0; 

            $where = ' ';

            while(count($filters) > $i)
            {
                if(strlen($filters[$i]) < 1)
                {
                    $i = count($filters);
                    break;
                }

                if($i == 0)
                {                    
                    $where .= "WHERE" . $filters[$i];
                }
                else
                {
                    $where .= " AND" . $filters[$i];
                }

                $i++;
            }

            $query = DB::select("SELECT sum(ammount) as ammount , status , name , date
                FROM (SELECT invoice_headers.id , name , status , invoice_headers.location_id , invoice_headers.date FROM interview_tests.invoice_headers JOIN locations ON Location_id = locations.id) AS `lines`
                JOIN (SELECT value as 'ammount' , invoice_header_id FROM invoice_lines) AS `header` ON header.invoice_header_id = lines.id $where GROUP BY status , name , date order by name"); 

            $resultArray=[];
            
            foreach($query as $row)
            {
                if(!isset($resultArray[$row->name]))
                {
                    $resultArray[$row->name] = [];
                } 

                array_push($resultArray[$row->name] ,[$row->ammount , $row->status , $row->date]);
            }

            return view('result' , ['results' => json_encode($resultArray)]);
            

        }

        public function InvoiceAmmountByLocation(Request $request)
        {

            if($request->Inv_location != 'All')
            {
                $query = DB::select("SELECT id FROM interview_tests.invoice_headers where location_id = $request->Inv_location");
          
                $ids = '';
    
                foreach ($query as $row)
                {
                   $ids .= ',' .$row->id;
                }
                $ids = substr($ids,1);
    
                $query2 = DB::select("SELECT SUM(value) as 'ammount' , status FROM interview_tests.invoice_lines
                join interview_tests.invoice_headers on invoice_headers.id = invoice_lines.invoice_header_id
                WHERE invoice_header_id in ($ids) group by invoice_headers.status");

                $resultArray = [];

                foreach($query2 as $row)
                {
                    if(!isset($resultArray[$row->status]))
                    {
                        $resultArray[$row->status] = [];
                    } 
                    array_push($resultArray[$row->status] ,[$row->ammount]);
                }
            }    
            else
            {
                $query = DB::select("SELECT sum(ammount) as ammount , status , name
                FROM (SELECT invoice_headers.id , name , status FROM interview_tests.invoice_headers JOIN locations ON Location_id = locations.id) AS `lines`
                JOIN (SELECT value as 'ammount' , invoice_header_id FROM invoice_lines) AS `header` ON header.invoice_header_id = lines.id GROUP BY status , name order by name
                ");                

                $resultArray = [];

                foreach($query as $row)
                {
                    if(!isset($resultArray[$row->status]))
                    {
                        $resultArray[$row->status] = [];
                    } 
                    array_push($resultArray[$row->status] ,[$row->ammount , $row->name]);
                }                
            }              
            
            return view('result' , ['results' => json_encode($resultArray)]);
        }
    }