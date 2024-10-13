<?php

namespace App\Models;

use App\Mail\TransferApproved;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\TransferRequestApproved;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransferRequest extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = [];
    public function citizen()
    {
        return $this->belongsTo(User::class);
    }
    public function fromVillage()
    {
        return $this->belongsTo(Village::class, 'from_village_id');
    }

    public function toVillage()
    {
        return $this->belongsTo(Village::class, 'to_village_id');
    }
    public function chief_approval()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approve()
    {
        $this->update([
            'approval_status' => 'Approved',
            'approved_by' => auth()->id(),
        ]);
        $this->citizen->update([
            'village_id' => $this->to_village_id,
        ]);
        // Create and send the notification
        $this->citizen->notify(new TransferRequestApproved($this));

        // Send email to the user
        Mail::to($this->citizen->email)->send(new TransferApproved($this));

        return $this;
    }
}
