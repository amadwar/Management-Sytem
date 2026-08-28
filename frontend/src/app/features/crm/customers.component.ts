import { ChangeDetectionStrategy, Component, OnInit, signal } from '@angular/core';
import { ReactiveFormsModule, FormControl, FormGroup, Validators } from '@angular/forms';
import { HttpErrorResponse } from '@angular/common/http';
import { RouterLink } from '@angular/router';
import { ApiService } from '../../core/api/api.service';

interface Customer {
  id: string;
  type: 'person' | 'company';
  status: 'active' | 'inactive' | 'prospect' | 'blocked';
  name: string;
  company_name?: string | null;
  email?: string | null;
  phone?: string | null;
  source?: string | null;
}

@Component({
  selector: 'app-crm-customers',
  imports: [ReactiveFormsModule, RouterLink],
  template: `
    <div class="page-head">
      <div>
        <h1>CRM · Customers</h1>
        <p>Manage people and company accounts inside this workspace.</p>
      </div>
    </div>

    @if (successMessage()) { <div class="success">{{ successMessage() }}</div> }
    @if (errorMessage()) { <div class="error">{{ errorMessage() }}</div> }

    <div class="grid crm-grid">
      <form class="card form-card" [formGroup]="form" (ngSubmit)="create()">
        <h3>Create customer</h3>

        <label>Type
          <select formControlName="type">
            <option value="company">Company</option>
            <option value="person">Person</option>
          </select>
        </label>

        <label>Name
          <input formControlName="name" (blur)="form.controls.name.markAsTouched()">
          @if (form.controls.name.touched && form.controls.name.hasError('required')) {
            <small class="field-error">Name is required.</small>
          }
        </label>

        <label>Company name
          <input formControlName="company_name">
        </label>

        <label>Email
          <input formControlName="email" type="email" (blur)="form.controls.email.markAsTouched()">
          @if (form.controls.email.touched && form.controls.email.hasError('email')) {
            <small class="field-error">Enter a valid email address.</small>
          }
        </label>

        <label>Phone
          <input formControlName="phone">
        </label>

        <label>Status
          <select formControlName="status">
            <option value="active">Active</option>
            <option value="prospect">Prospect</option>
            <option value="inactive">Inactive</option>
            <option value="blocked">Blocked</option>
          </select>
        </label>

        <label>Source
          <input formControlName="source" placeholder="Referral, website, campaign...">
        </label>

        <label>Description
          <textarea formControlName="description" rows="4"></textarea>
        </label>

        <button class="primary" type="submit" [disabled]="submitting()">
          {{ submitting() ? 'Creating...' : 'Create customer' }}
        </button>
      </form>

      <div>
        <div class="card crm-toolbar">
          <input [formControl]="search" placeholder="Search name, email or phone">
          <select [formControl]="statusFilter">
            <option value="">All statuses</option>
            <option value="active">Active</option>
            <option value="prospect">Prospect</option>
            <option value="inactive">Inactive</option>
            <option value="blocked">Blocked</option>
          </select>
          <button (click)="load()">Search</button>
        </div>

        <div class="card table-card">
          @if (loading()) {
            <p>Loading customers...</p>
          } @else {
            <table>
              <thead><tr><th>Name</th><th>Type</th><th>Email</th><th>Phone</th><th>Status</th></tr></thead>
              <tbody>
                @for (customer of customers(); track customer.id) {
                  <tr>
                    <td><a [routerLink]="['/crm/customers', customer.id]">{{ customer.name }}</a></td>
                    <td>{{ customer.type }}</td>
                    <td>{{ customer.email || '-' }}</td>
                    <td>{{ customer.phone || '-' }}</td>
                    <td><span class="badge">{{ customer.status }}</span></td>
                  </tr>
                } @empty {
                  <tr><td colspan="5">No customers found.</td></tr>
                }
              </tbody>
            </table>
          }
        </div>
      </div>
    </div>
  `,
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class CustomersComponent implements OnInit {
  readonly customers = signal<Customer[]>([]);
  readonly loading = signal(false);
  readonly submitting = signal(false);
  readonly errorMessage = signal('');
  readonly successMessage = signal('');
  readonly search = new FormControl('', { nonNullable: true });
  readonly statusFilter = new FormControl('', { nonNullable: true });

  readonly form = new FormGroup({
    type: new FormControl<'person' | 'company'>('company', { nonNullable: true, validators: [Validators.required] }),
    status: new FormControl<'active' | 'inactive' | 'prospect' | 'blocked'>('active', { nonNullable: true, validators: [Validators.required] }),
    name: new FormControl('', { nonNullable: true, validators: [Validators.required, Validators.maxLength(255)] }),
    company_name: new FormControl('', { nonNullable: true }),
    email: new FormControl('', { nonNullable: true, validators: [Validators.email] }),
    phone: new FormControl('', { nonNullable: true }),
    source: new FormControl('', { nonNullable: true }),
    description: new FormControl('', { nonNullable: true }),
  });

  constructor(private api: ApiService) {}

  ngOnInit(): void { this.load(); }

  load(): void {
    this.loading.set(true);
    this.errorMessage.set('');
    const params = new URLSearchParams();
    if (this.search.value.trim()) params.set('search', this.search.value.trim());
    if (this.statusFilter.value) params.set('status', this.statusFilter.value);
    const query = params.toString() ? `?${params}` : '';
    this.api.get<{ data: Customer[] }>(`/crm/customers${query}`).subscribe({
      next: (response) => { this.customers.set(response.data); this.loading.set(false); },
      error: (error: HttpErrorResponse) => { this.errorMessage.set(this.message(error)); this.loading.set(false); },
    });
  }

  create(): void {
    this.errorMessage.set('');
    this.successMessage.set('');
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      this.errorMessage.set('Please correct the highlighted fields.');
      return;
    }

    this.submitting.set(true);
    const raw = this.form.getRawValue();
    const payload = {
      ...raw,
      company_name: raw.company_name || null,
      email: raw.email || null,
      phone: raw.phone || null,
      source: raw.source || null,
      description: raw.description || null,
    };

    this.api.post('/crm/customers', payload).subscribe({
      next: () => {
        this.successMessage.set('Customer created successfully.');
        this.form.reset({ type: 'company', status: 'active', name: '', company_name: '', email: '', phone: '', source: '', description: '' });
        this.submitting.set(false);
        this.load();
      },
      error: (error: HttpErrorResponse) => { this.errorMessage.set(this.message(error)); this.submitting.set(false); },
    });
  }

  private message(error: HttpErrorResponse): string {
    if (error.status === 403 && error.error?.message?.includes('module')) return 'CRM is not enabled for this company. Enable it under Modules first.';
    const validation = error.error?.errors ? Object.values(error.error.errors).flat().join(' ') : '';
    return validation || error.error?.message || 'An unexpected error occurred.';
  }
}
