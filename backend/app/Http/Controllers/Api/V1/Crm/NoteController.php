<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Crm;

use App\Application\Audit\AuditLogger;
use App\Http\Controllers\Controller;
use App\Models\CrmCustomer;
use App\Models\CrmNote;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class NoteController extends Controller
{
    public function index(string $customer)
    {
        $parent=CrmCustomer::query()->where('public_id',$customer)->firstOrFail();
        return response()->json(['data'=>CrmNote::query()->where('customer_id',$parent->id)->with('user:id,name')->latest()->get()->map(fn($n)=>['id'=>$n->public_id,'body'=>$n->body,'user'=>$n->user?->name,'created_at'=>$n->created_at?->toISOString()])]);
    }
    public function store(Request $request,string $customer,AuditLogger $audit)
    {
        abort_unless($request->user()->hasPermission('crm.notes.create'),403);
        $data=$request->validate(['body'=>['required','string','max:5000']]);
        $parent=CrmCustomer::query()->where('public_id',$customer)->firstOrFail();
        $note=CrmNote::query()->create(['customer_id'=>$parent->id,'user_id'=>$request->user()->id,'public_id'=>(string)Str::uuid(),'body'=>$data['body']]);
        $audit->record('crm.note.created',$request,CrmNote::class,$note->id);
        return response()->json(['data'=>['id'=>$note->public_id,'body'=>$note->body,'created_at'=>$note->created_at?->toISOString()]],201);
    }
}
