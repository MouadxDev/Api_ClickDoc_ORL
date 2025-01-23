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

		if(str_contains($type, 'consultations')) {
			$reports['consultations'] = ModelRendezVous::select('type', DB::raw('COUNT(*) as count'))
				->groupBy('type')
				->get();
		}
		if (str_contains($type, 'billing')) {
			$reports['billing'] = [
				'total_revenue' => DB::table('factures')->sum('amount'),
				'payments_received' => DB::table('payments')->sum('amount'),
				'pending_invoices' => DB::table('factures')->where('statut', 'non payé')->count(),
				'monthly_trends' => DB::table('factures')
					->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(amount) as total'))
					->groupBy('month')
					->orderBy('month')
					->get(),
			];
		}
		if (str_contains($type, 'patients')) {
			$reports['patients'] = [
				// Total number of patients
				'total' => DB::table('patients')->count(),
				
				// Patients created within the last month
				'new' => DB::table('patients')
					->whereDate('created_at', '>=', now()->subMonth())
					->count(),
				
				// Patients created before the last month
				'regular' => DB::table('patients')
					->whereDate('created_at', '<', now()->subMonth())
					->count(),
			];
		}

		if(str_contains($type, 'pathologies')) {
			$reports['pathologies'] = DB::table('diagnoses')
				->select('diagnosis', DB::raw('COUNT(*) as count'))
				->groupBy('diagnosis')
				->orderBy('count', 'desc')
				->take(10) // top 10
				->get();
		}

		return $reports;
			
	}
}
