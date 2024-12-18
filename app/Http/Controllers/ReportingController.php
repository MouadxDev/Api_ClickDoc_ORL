<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\RendezVous as ModelRendezVous;
use DB;

class ReportingController extends Controller
{
    public function reports($type){
	
		$reports=[];
		
		if(str_contains($type,'dates'))
		{
			$reports['date'] = Patient::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
						->groupBy('date')
						->get();
		}
		
		if(str_contains($type,'demographics'))
		{
			$reports['demo']['male'] = Patient::where('sex','=','M')->get()->count();
			$reports['demo']['females'] = Patient::where('sex','=','F')->get()->count();
		}
		
		if(str_contains($type,'plage-horaire'))
		{
			$reports['horaires'] = ModelRendezVous::select(DB::raw('heure'), DB::raw('COUNT(*) as count'))
						->groupBy('heure')
						->orderBy('count','desc')
						->get();
		}
		
		if(str_contains($type,'rdv-annule')){
			$reports['canceled'] = ModelRendezVous::select(DB::raw('statut'), DB::raw('COUNT(*) as count'))
						->groupBy('statut')
						->whereIn('statut',['canceled','postponed'])
						->get();
		}
		
		return $reports;
			
	}
}
