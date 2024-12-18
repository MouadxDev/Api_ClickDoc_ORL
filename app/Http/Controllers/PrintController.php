<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Entite;
use App\Models\User;

class PrintController extends Controller
{
	public function certificat($type,$id,$docteur){
		$patient = Patient::where('uid','=',$id)->first();
		$user = User::find($docteur);
		if($user->role=='docteur')
		{
			$docteur = $user;
		}
		else {
			$docteur = User::where('entity_id','=',$user->entity_id)->where('role','=','doctor')->first();
		}
		$entite = Entite::find($docteur->entity_id);
		if($type=='aptitude'){
			return view('certificat-aptitude',['patient'=>$patient,'docteur'=>$docteur,'entite'=>$entite]);
		}
		if($type=='repos'){
			return view('certificat-medical',['patient'=>$patient,'docteur'=>$docteur,'entite'=>$entite]);
		}
	}
}
