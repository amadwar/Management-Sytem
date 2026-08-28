import { ChangeDetectionStrategy, Component, OnInit, signal } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { HttpErrorResponse } from '@angular/common/http';
import { ApiService } from '../../core/api/api.service';

interface Contact { id:string; name:string; job_title?:string; email?:string; phone?:string; is_primary:boolean }
interface Customer { id:string; name:string; type:string; status:string; company_name?:string; email?:string; phone?:string; description?:string; contacts:Contact[] }
interface Note { id:string; body:string; user?:string; created_at:string }
interface Activity { id:string; type:string; subject:string; description?:string; occurred_at:string; user?:string }

@Component({
  selector:'app-crm-customer-detail', imports:[ReactiveFormsModule],
  template:`
  <div class="page-head"><div><h1>{{customer()?.name || 'Customer'}}</h1><p>Customer profile, contacts and relationship history.</p></div></div>
  @if(errorMessage()){<div class="error">{{errorMessage()}}</div>}
  @if(successMessage()){<div class="success">{{successMessage()}}</div>}
  @if(customer(); as c){
    <div class="grid detail-cards">
      <div class="card"><h3>Profile</h3><div class="detail-grid"><div><span>Type</span><strong>{{c.type}}</strong></div><div><span>Status</span><strong>{{c.status}}</strong></div><div><span>Email</span><strong>{{c.email||'-'}}</strong></div><div><span>Phone</span><strong>{{c.phone||'-'}}</strong></div></div><p>{{c.description||''}}</p></div>
      <div class="card"><h3>Contacts</h3>@for(contact of c.contacts;track contact.id){<div class="list-row"><div><strong>{{contact.name}}</strong><small>{{contact.job_title||''}} · {{contact.email||contact.phone||''}}</small></div>@if(contact.is_primary){<span class="badge">Primary</span>}</div>}@empty{<p>No contacts yet.</p>}
        <form class="inline-form" [formGroup]="contactForm" (ngSubmit)="addContact()"><input formControlName="name" placeholder="Contact name"><input formControlName="email" placeholder="Email"><input formControlName="phone" placeholder="Phone"><button>Add contact</button></form>
      </div>
      <div class="card"><h3>Notes</h3>@for(note of notes();track note.id){<div class="timeline-item"><strong>{{note.user||'User'}}</strong><small>{{note.created_at}}</small><p>{{note.body}}</p></div>}@empty{<p>No notes yet.</p>}<form class="inline-form" [formGroup]="noteForm" (ngSubmit)="addNote()"><textarea formControlName="body" rows="3" placeholder="Write a note"></textarea><button>Add note</button></form></div>
      <div class="card"><h3>Activities</h3>@for(activity of activities();track activity.id){<div class="timeline-item"><strong>{{activity.type}} · {{activity.subject}}</strong><small>{{activity.occurred_at}}</small><p>{{activity.description||''}}</p></div>}@empty{<p>No activities yet.</p>}<form class="inline-form" [formGroup]="activityForm" (ngSubmit)="addActivity()"><select formControlName="type"><option value="call">Call</option><option value="email">Email</option><option value="meeting">Meeting</option><option value="task">Task</option><option value="other">Other</option></select><input formControlName="subject" placeholder="Subject"><input formControlName="occurred_at" type="datetime-local"><textarea formControlName="description" placeholder="Description"></textarea><button>Add activity</button></form></div>
    </div>
  }`, changeDetection:ChangeDetectionStrategy.OnPush
})
export class CustomerDetailComponent implements OnInit{
  readonly customer=signal<Customer|null>(null);readonly notes=signal<Note[]>([]);readonly activities=signal<Activity[]>([]);readonly errorMessage=signal('');readonly successMessage=signal('');
  readonly contactForm=new FormGroup({name:new FormControl('',{nonNullable:true,validators:[Validators.required]}),email:new FormControl('',{nonNullable:true,validators:[Validators.email]}),phone:new FormControl('',{nonNullable:true})});
  readonly noteForm=new FormGroup({body:new FormControl('',{nonNullable:true,validators:[Validators.required]})});
  readonly activityForm=new FormGroup({type:new FormControl('call',{nonNullable:true}),subject:new FormControl('',{nonNullable:true,validators:[Validators.required]}),description:new FormControl('',{nonNullable:true}),occurred_at:new FormControl(new Date().toISOString().slice(0,16),{nonNullable:true,validators:[Validators.required]})});
  private id='';
  constructor(private route:ActivatedRoute,private api:ApiService){}
  ngOnInit(){this.id=this.route.snapshot.paramMap.get('id')||'';this.load();}
  load(){this.api.get<{data:Customer}>(`/crm/customers/${this.id}`).subscribe({next:r=>this.customer.set(r.data),error:e=>this.errorMessage.set(this.msg(e))});this.api.get<{data:Note[]}>(`/crm/customers/${this.id}/notes`).subscribe(r=>this.notes.set(r.data));this.api.get<{data:Activity[]}>(`/crm/customers/${this.id}/activities`).subscribe(r=>this.activities.set(r.data));}
  addContact(){if(this.contactForm.invalid){this.contactForm.markAllAsTouched();return;}this.api.post(`/crm/customers/${this.id}/contacts`,this.contactForm.getRawValue()).subscribe({next:()=>{this.successMessage.set('Contact added.');this.contactForm.reset({name:'',email:'',phone:''});this.load();},error:e=>this.errorMessage.set(this.msg(e))});}
  addNote(){if(this.noteForm.invalid)return;this.api.post(`/crm/customers/${this.id}/notes`,this.noteForm.getRawValue()).subscribe({next:()=>{this.successMessage.set('Note added.');this.noteForm.reset({body:''});this.load();},error:e=>this.errorMessage.set(this.msg(e))});}
  addActivity(){if(this.activityForm.invalid)return;this.api.post(`/crm/customers/${this.id}/activities`,this.activityForm.getRawValue()).subscribe({next:()=>{this.successMessage.set('Activity added.');this.load();},error:e=>this.errorMessage.set(this.msg(e))});}
  private msg(e:HttpErrorResponse){return e.error?.message||'Request failed.';}
}
