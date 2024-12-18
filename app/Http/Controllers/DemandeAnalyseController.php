<?php

namespace App\Http\Controllers;

use App\Models\DemandeAnalyse;
use App\Models\User;
use Illuminate\Http\Request;

class DemandeAnalyseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return DemandeAnalyse::join("consultations as c","consultation_id","=","c.id")
        ->join("analyses as a","analyse_id","=","a.id")
        ->select("demande_analyses.*","c.doctor_id","a.libelle")
        ->where("doctor_id","=",auth()->user()->id)
        ->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $model = new DemandeAnalyse();
        $model -> consultation_id = request()->consultation_id; 
        $model -> analyse_id = request()->analyse_id; 
        $model -> state = "soumise"; 
        $model -> save() ;

        return $model;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return DemandeAnalyse::join("analyses as a","a.id",'=',"demande_analyses.analyse_id")
        ->where("demande_analyses.consultation_id","=",$id)
        ->select("a.libelle","demande_analyses.*")
        ->get();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if(auth()->user()->role=="Admin" OR auth()->user()->role=="doctor")
            $doctor = auth()->user() ;
        else 
            $doctor = User::where("entity_id",'=',auth()->user()->entity_id)->whereIn("role",["Admin","doctor"]) -> first();
        return DemandeAnalyse::join("analyses as a","a.id",'=',"demande_analyses.analyse_id")
        ->join("consultations as c","c.id","=","demande_analyses.consultation_id")
        ->where("c.patient_id","=",$id)
        ->where("c.doctor_id","=",$doctor->id)
        ->select("a.libelle","demande_analyses.*")
        ->get();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $model = DemandeAnalyse::find($id);
        $model -> lab_id = request()->lab_id;
        $model -> state = request()->state;
        $model -> document = request()->document;
        $model -> save();

        return $model;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $model = DemandeAnalyse::find($id);
        $model -> delete();
         return ["message"=>"Supprimé avec succès"];
    }
}
