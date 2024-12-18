<?php

namespace App\Http\Controllers;

use App\Models\ActeMedical;
use App\Models\ArticleFacture;
use App\Models\Consultation;
use App\Models\Facture;
use App\Models\User;
use App\Models\WaitingList as ModelWaitingList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FactureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(auth()->user()->role=="Admin" OR auth()->user()->role=="doctor")
            $doctor = auth()->user() ;
        else 
            $doctor = User::where("entity_id",'=',auth()->user()->entity_id)->whereIn("role",["Admin","doctor"]) -> first();

        $factures = Facture::join("consultations as c","c.id",'=',"factures.consultation_id")
        ->where("c.patient_id",'=',request()->patient_id)
        ->where("c.doctor_id",'=',$doctor->id)
        ->whereDate('c.created_at',Carbon::today())
        ->select("factures.*","c.patient_id")
        ->get();

        return $factures;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(auth()->user()->role=="Admin" OR auth()->user()->role=="doctor")
            $doctor = auth()->user() ;
        else 
            $doctor = User::where("entity_id",'=',auth()->user()->entity_id)->whereIn("role",["Admin","doctor"]) -> first();

        $factures = Facture::join("consultations as c",'c.id','=',"factures.consultation_id")
        ->join("patients as p","p.id",'=',"c.patient_id")
        ->where("c.doctor_id",'=',$doctor->id)
        ->whereDate('c.created_at',Carbon::today())
        ->groupBy('c.patient_id')
        ->select("p.*")
        ->get();

        return $factures;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $id = request()->consultation_id;
        $c = Consultation::find($id);

        $waiting = ModelWaitingList::find($c->wl_id);
        $acte = ActeMedical::findOrFail($waiting->type);
        
        $f = new Facture();
        $f -> consultation_id  = $id;
        $f -> amount = 0;
        $f -> save();
        $f -> uid = "F".date("Y")."-".str_pad($f->id, 6, '0', STR_PAD_LEFT);;
        $f -> save();

        

        $doctor_fee = new ArticleFacture();
        $doctor_fee -> facture_id = $f -> id;
        $doctor_fee -> libelle = $acte->libelle;
        $doctor_fee -> prix = $acte -> prix ;
        $doctor_fee -> type = 0 ;
        $doctor_fee -> save();

        $liste = ArticleFacture::where("facture_id",'=',$id)->orderBy("type",'asc')->get();

        return ["liste"=>$liste , "facture"=>$f];

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $f = Facture::where('consultation_id','=',$id)->first();
        // $liste = ArticleFacture::where("facture_id",'=',$id)->orderBy("type",'asc')->get();
        $liste = ArticleFacture::where("facture_id", "=", $f->id)->get();



        return ["liste"=>$liste , "facture"=>$f];

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $impayes = Facture::join("consultations as c",'c.id','=',"factures.consultation_id")
        ->join("users as u",'u.id','=','c.doctor_id')
        ->where("u.entity_id","=",auth()->user()->entity_id)
        ->where("c.patient_id",'=',$id)
        ->where("factures.statut",'=','non payé')
        ->select('factures.*')
        ->get();
        
        $parts = Facture::join("consultations as c",'c.id','=',"factures.consultation_id")
        ->join("users as u",'u.id','=','c.doctor_id')
        ->join("payments as p",'p.facture_id','=','factures.id')
        ->where("u.entity_id","=",auth()->user()->entity_id)
        ->where("c.patient_id",'=',$id)
        ->where("factures.statut",'=','payé partiellement')
        ->groupBy('factures.id')
        ->select('factures.*',DB::raw("SUM(p.amount) as paid"))
        ->get();
        return ["part"=>$parts,"nope"=>$impayes];
    }   

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $f = Facture::find($id);
        if(request()->has("amount"))
        $f -> amount = request()->amount;
        if(request()->has("statut"))
        {
            $f -> statut = request()->statut;
        }
        $f -> save();
        return $f;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
