<?php

namespace App\Http\Controllers;

use App\Models\tbl_serverconfig_cabletv;
use App\Models\tbl_serverconfig_education;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EducationController extends Controller
{
    public function listAll()
    {
        $datas = tbl_serverconfig_education::get()->makeHidden(['amount','plan_id','server']);
        return response()->json([
            'status' => true,
            'message' => 'Fetched successfully',
            'data' => $datas,
        ]);
    }


    public function purchase(Request $request)
    {
        $input = $request->all();
        $rules = array(
            "networkID" => "required",
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['status' => false, 'message' => implode(",", $validator->errors()->all()), 'error' => $validator->errors()->all()]);
        }

        $airtimes = tbl_serverconfig_education::where([['id', $input['networkID']], ['status',1]])->first();

        if(!$airtimes){
            return response()->json([
                'status' => false,
                'message' => "Network ID not valid or available",
            ], 200);
        }


        Transaction::create([
            "title" => $airtimes->name." Education",
            "amount" => $airtimes->amount,
            "reference" => rand(),
            "remark" => "Successful",
            "server" => "0",
            "server_response" => "{'status':'success'}",
        ]);


        return response()->json([
            'status' => true,
            'message' => "Transaction successful",
        ], 200);
    }

}
