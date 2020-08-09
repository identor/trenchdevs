<?php

namespace App\Http\Controllers;

use App\AwsSnsLog;
use Illuminate\Http\Request;

class AwsController extends Controller
{
    public function sns(Request $request){
        $sns = new AwsSnsLog();
        $sns->identifier = "sns";
        $sns->headers = json_encode($request->header());
        $sns->raw_json = json_encode($request->all());
        $sns->ip = $request->ip();
        $sns->saveOrFail();
    }
}
