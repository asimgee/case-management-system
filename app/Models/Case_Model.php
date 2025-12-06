<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Case_Model extends Model
{
    use HasFactory;

    protected $table = 'cases';

    protected $fillable = [
        'case_number',
        'case_title',
        'first_party_title',
        'second_party_title',
        'first_party_name',
        'first_party_contact',
        'first_party_cnic',
        'first_party_address',
        'first_party_council',
        'second_party_name',
        'second_party_contact',
        'second_party_cnic',
        'second_party_address',
        'second_party_council',
        'case_type_id',
        'case_remedy_id',
        'court_type_id',
        'court_name',
        'case_status',
        'filing_date',
        'next_date',
        'judgment_date',
        'assigned_lawyer_id',
        'additional_lawyer_id',
        'offending_lawyer',
        'opponent_council',
        'power_of_attorney',
        'next_order',
        'remarks',
        'case_notes',
        'fir_no',
        'fir_year',
        'offence',
        'police_station',
        'other_fir_details',
        'documents',
        'other_parties',
        'user_id'
    ];

    protected $casts = [
        'documents' => 'array',
        'other_parties' => 'array',
        'filing_date' => 'date',
        'next_date' => 'date',
        'judgment_date' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function caseType()
    {
        return $this->belongsTo(CaseType::class);
    }

    public function caseRemedy()
    {
        return $this->belongsTo(CaseRemedy::class);
    }

    public function courtType()
    {
        return $this->belongsTo(CourtType::class);
    }

    public function assignedLawyer()
    {
        return $this->belongsTo(Lawyer::class, 'assigned_lawyer_id');
    }

    public function additionalLawyer()
    {
        return $this->belongsTo(Lawyer::class, 'additional_lawyer_id');
    }

    // Accessors
    public function getFormattedStatusAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->case_status));
    }

    public function getFormattedFilingDateAttribute()
    {
        return $this->filing_date->format('M d, Y');
    }

    public function getCaseTypeNameAttribute()
    {
        return $this->caseType->name;
    }

    public function getCaseRemedyNameAttribute()
    {
        return $this->caseRemedy->name;
    }

    public function getCourtTypeNameAttribute()
    {
        return $this->courtType->name;
    }
      public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'case_id');
    }

    /**
     * Get first party client
     */
    public function firstParty()
    {
        return $this->clients()->firstParty()->first();
    }

    /**
     * Get second party client
     */
    public function secondParty()
    {
        return $this->clients()->secondParty()->first();
    }

    /**
     * Get other parties
     */
    public function otherParties()
    {
        return $this->clients()->otherParties()->get();
    }
}