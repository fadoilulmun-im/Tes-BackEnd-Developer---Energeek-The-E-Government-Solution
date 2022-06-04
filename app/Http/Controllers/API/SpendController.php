<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\spend;
use Illuminate\Http\Request;
use Validator;
use App\Models\SpendDetail;

class SpendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $spends = Spend::with('details')->whereHas('details')->where('user_id', auth()->user()->id)->get();

        return response()->json([
            'message' => 'successfully get all spend data',
            'status' => 'OK',
            'data' => $spends,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'year' => 'required|numeric',
            'month' => 'required|numeric|between:1,12',
            'day' => 'required|numeric|between:1,31',
            'total' => 'required|numeric',
            'description' => 'string',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Failed to create spend',
                'status' => 'error',
                'data' => $validator->errors(),
            ], 400);       
        }

        $check = SpendDetail::where('day', $request->day)
                    ->whereRelation('spend', 'year', $request->year)
                    ->whereRelation('spend', 'month', $request->month)
                    ->first();
        
        if($check){
            $check->spend;
            return response()->json([
                'message' => 'The data for the date in the month and year has already been created',
                'status' => 'error',
                'data' => $check,
            ], 400);
        }

        $spend = Spend::where('year', $request->year)->where('month', $request->month)->first();
        
        if(!$spend){
            $spend = Spend::create([
                'year' => $request->year,
                'month' => $request->month,
                'user_id' => auth()->user()->id,
            ]);
        }

        $create = $spend->details()->create([
            'day' => $request->day,
            'total' => $request->total,
            'description' => $request->description,
        ]);
        $create->spend;
        return response()->json([
            'message' => 'Spend successfully created',
            'status' => 'OK',
            'data' => $create,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\spend  $spend
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $spend = Spend::with('details')->find($id);

        if(!$spend){
            return response()->json([
                'message' => 'Spend data not found',
                'status' => 'error',
            ], 404);
        }

        return response()->json([
            'message' => 'successfully get spend data',
            'status' => 'OK',
            'data' => $spend,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\spend  $spend
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $spendDetail = SpendDetail::find($id);
        if(!$spendDetail){
            return response()->json([
                'message' => 'Spend detail data not found',
                'status' => 'error',
            ], 404);
        }

        $validator = Validator::make($request->all(),[
            'year' => 'required|numeric',
            'month' => 'required|numeric|between:1,12',
            'day' => 'required|numeric|between:1,31',
            'total' => 'required|numeric',
            'description' => 'string',
        ]);
        if($validator->fails()){
            return response()->json([
                'message' => 'Failed to update spend',
                'status' => 'error',
                'data' => $validator->errors(),
            ], 400);       
        }

        $check = SpendDetail::where('day', $request->day)
                    ->whereRelation('spend', 'year', $request->year)
                    ->whereRelation('spend', 'month', $request->month)
                    ->where('id', '!=', $id)
                    ->first();
        
        if($check){
            $check->spend;
            return response()->json([
                'message' => 'The data for the date in the month and year has already exists',
                'status' => 'error',
                'data' => $check,
            ], 400);
        }
        
        $spendDetail->day = $request->day;
        $spendDetail->total = $request->total;
        $spendDetail->description = $request->description;

        if($spendDetail->spend->year != $request->year || $spendDetail->spend->month != $request->month){
            $spend = Spend::where('year', $request->year)->where('month', $request->month)->first();
            if(!$spend){
                $spend = Spend::create([
                    'year' => $request->year,
                    'month' => $request->month,
                    'user_id' => auth()->user()->id,
                ]);
            }

            $spendDetail->spend_id = $spend->id;
        }

        $spendDetail->save();

        return response()->json([
            'message' => 'Spend successfully updated',
            'status' => 'OK',
            'data' => $spendDetail,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\spend  $spend
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $spendDetail = SpendDetail::with('spend')->find($id);

        if(!$spendDetail){
            return response()->json([
                'message' => 'Spend detail data not found',
                'status' => 'error',
            ], 404);
        }

        $spendDetail->delete();

        $spend = Spend::find($spendDetail->spend_id);
        if(!$spend->details->count()){
            $spend->delete();
        }

        return response()->json([
            'message' => 'Spend detail data successfully deleted',
            'status' => 'OK',
            'data' => $spendDetail,
        ]);
    }

    public function alltotal()
    {
        $spends = Spend::whereHas('details')->where('user_id', auth()->user()->id)->get();
        $spends->makeVisible(['total_per_month']);
        $spends->makeHidden(['details']);

        return response()->json([
            'message' => 'successfully get all spend data with total',
            'status' => 'OK',
            'data' => $spends,
        ]);
    }

    public function oneTotal($id)
    {
        $spend = Spend::find($id);

        if(!$spend){
            return response()->json([
                'message' => 'Spend data not found',
                'status' => 'error',
            ], 404);
        }

        $spend->makeVisible(['total_per_month']);
        $spend->makeHidden(['details']);
        return response()->json([
            'message' => 'successfully get one spend data with total',
            'status' => 'OK',
            'data' => $spend,
        ]);
    }
}
