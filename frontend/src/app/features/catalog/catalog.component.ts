import {
  ChangeDetectionStrategy,
  Component,
  OnInit,
  signal,
} from '@angular/core';
import {
  FormControl,
  FormGroup,
  ReactiveFormsModule,
  Validators,
} from '@angular/forms';
import { HttpErrorResponse } from '@angular/common/http';
import { ApiService } from '../../core/api/api.service';

interface CatalogItem {
  id: string;
  type: 'product' | 'service';
  sku: string | null;
  name: string;
  description: string | null;
  price: string;
  currency_code: string;
  unit: string | null;
  status: 'active' | 'inactive';
  taxable: boolean;
  created_at: string;
  updated_at: string;
}

interface CatalogResponse {
  data: CatalogItem[];
  meta?: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

@Component({
  selector: 'app-catalog',
  imports: [
    ReactiveFormsModule,
  ],
  template: `
    <div class="page-head">
      <div>
        <h1>Products & Services</h1>
        <p>
          Manage the products and services available in this workspace.
        </p>
      </div>
    </div>

    @if (successMessage()) {
      <div class="success">
        {{ successMessage() }}
      </div>
    }

    @if (errorMessage()) {
      <div class="error">
        {{ errorMessage() }}
      </div>
    }

    <div class="grid crm-grid">

      <form
        class="card form-card"
        [formGroup]="form"
        (ngSubmit)="save()"
      >
        <h3>
          {{ editingId() ? 'Edit item' : 'Create item' }}
        </h3>

        <label>
          Type

          <select formControlName="type">
            <option value="product">
              Product
            </option>

            <option value="service">
              Service
            </option>
          </select>
        </label>

        <label>
          Name

          <input
            formControlName="name"
            (blur)="form.controls.name.markAsTouched()"
          >

          @if (
            form.controls.name.touched
            && form.controls.name.hasError('required')
          ) {
            <small class="field-error">
              Name is required.
            </small>
          }
        </label>

        <label>
          SKU

          <input
            formControlName="sku"
            placeholder="PROD-001"
          >
        </label>

        <label>
          Description

          <textarea
            formControlName="description"
            rows="4"
          ></textarea>
        </label>

        <label>
          Price

          <input
            type="number"
            min="0"
            step="0.01"
            formControlName="price"
          >
        </label>

        <label>
          Currency

          <select formControlName="currency_code">
            <option value="EUR">
              EUR
            </option>

            <option value="USD">
              USD
            </option>

            <option value="SYP">
              SYP
            </option>

            <option value="SAR">
              SAR
            </option>

            <option value="AED">
              AED
            </option>
          </select>
        </label>

        <label>
          Unit

          <input
            formControlName="unit"
            placeholder="piece, hour, kg..."
          >
        </label>

        <label>
          Status

          <select formControlName="status">
            <option value="active">
              Active
            </option>

            <option value="inactive">
              Inactive
            </option>
          </select>
        </label>

        <label>
          <input
            type="checkbox"
            formControlName="taxable"
          >

          Taxable
        </label>

        <div class="form-actions">
          <button
            class="primary"
            type="submit"
            [disabled]="submitting()"
          >
            @if (submitting()) {
              Saving...
            } @else if (editingId()) {
              Update item
            } @else {
              Create item
            }
          </button>

          @if (editingId()) {
            <button
              type="button"
              (click)="cancelEdit()"
            >
              Cancel
            </button>
          }
        </div>
      </form>

      <div>
        <div class="card crm-toolbar">

          <input
            [formControl]="search"
            placeholder="Search name or SKU"
          >

          <select [formControl]="typeFilter">
            <option value="">
              All types
            </option>

            <option value="product">
              Products
            </option>

            <option value="service">
              Services
            </option>
          </select>

          <select [formControl]="statusFilter">
            <option value="">
              All statuses
            </option>

            <option value="active">
              Active
            </option>

            <option value="inactive">
              Inactive
            </option>
          </select>

          <button (click)="load()">
            Search
          </button>
        </div>

        <div class="card table-card">

          @if (loading()) {

            <p>
              Loading catalog...
            </p>

          } @else {

            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Type</th>
                  <th>SKU</th>
                  <th>Price</th>
                  <th>Unit</th>
                  <th>Status</th>
                  <th>Tax</th>
                  <th>Actions</th>
                </tr>
              </thead>

              <tbody>

                @for (
                  item of items();
                  track item.id
                ) {

                  <tr>
                    <td>
                      <strong>
                        {{ item.name }}
                      </strong>
                    </td>

                    <td>
                      <span class="badge">
                        {{ item.type }}
                      </span>
                    </td>

                    <td>
                      {{ item.sku || '-' }}
                    </td>

                    <td>
                      {{ item.price }}
                      {{ item.currency_code }}
                    </td>

                    <td>
                      {{ item.unit || '-' }}
                    </td>

                    <td>
                      <span class="badge">
                        {{ item.status }}
                      </span>
                    </td>

                    <td>
                      {{ item.taxable ? 'Yes' : 'No' }}
                    </td>

                    <td>
                      <button
                        type="button"
                        (click)="edit(item)"
                      >
                        Edit
                      </button>

                      <button
                        type="button"
                        (click)="remove(item)"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>

                } @empty {

                  <tr>
                    <td colspan="8">
                      No products or services found.
                    </td>
                  </tr>

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
export class CatalogComponent implements OnInit {

  readonly items = signal<CatalogItem[]>([]);

  readonly loading = signal(false);

  readonly submitting = signal(false);

  readonly errorMessage = signal('');

  readonly successMessage = signal('');

  readonly editingId = signal<string | null>(null);

  readonly search = new FormControl(
    '',
    {
      nonNullable: true,
    },
  );

  readonly typeFilter = new FormControl(
    '',
    {
      nonNullable: true,
    },
  );

  readonly statusFilter = new FormControl(
    '',
    {
      nonNullable: true,
    },
  );

  readonly form = new FormGroup({
    type: new FormControl<'product' | 'service'>(
      'product',
      {
        nonNullable: true,
        validators: [
          Validators.required,
        ],
      },
    ),

    sku: new FormControl(
      '',
      {
        nonNullable: true,
        validators: [
          Validators.maxLength(100),
        ],
      },
    ),

    name: new FormControl(
      '',
      {
        nonNullable: true,
        validators: [
          Validators.required,
          Validators.maxLength(255),
        ],
      },
    ),

    description: new FormControl(
      '',
      {
        nonNullable: true,
      },
    ),

    price: new FormControl(
      0,
      {
        nonNullable: true,
        validators: [
          Validators.required,
          Validators.min(0),
        ],
      },
    ),

    currency_code: new FormControl(
      'EUR',
      {
        nonNullable: true,
        validators: [
          Validators.required,
        ],
      },
    ),

    unit: new FormControl(
      '',
      {
        nonNullable: true,
        validators: [
          Validators.maxLength(50),
        ],
      },
    ),

    status: new FormControl<'active' | 'inactive'>(
      'active',
      {
        nonNullable: true,
        validators: [
          Validators.required,
        ],
      },
    ),

    taxable: new FormControl(
      false,
      {
        nonNullable: true,
      },
    ),
  });

  constructor(
    private readonly api: ApiService,
  ) {}

  ngOnInit(): void {
    this.load();
  }

  load(): void {

    this.loading.set(true);
    this.errorMessage.set('');

    const params = new URLSearchParams();

    if (this.search.value.trim()) {
      params.set(
        'search',
        this.search.value.trim(),
      );
    }

    if (this.typeFilter.value) {
      params.set(
        'type',
        this.typeFilter.value,
      );
    }

    if (this.statusFilter.value) {
      params.set(
        'status',
        this.statusFilter.value,
      );
    }

    const query = params.toString()
      ? `?${params.toString()}`
      : '';

    this.api
      .get<CatalogResponse>(
        `/catalog-items${query}`,
      )
      .subscribe({
        next: (response) => {
          this.items.set(response.data);
          this.loading.set(false);
        },

        error: (
          error: HttpErrorResponse,
        ) => {
          this.errorMessage.set(
            this.message(error),
          );

          this.loading.set(false);
        },
      });
  }

  save(): void {

    this.errorMessage.set('');
    this.successMessage.set('');

    if (this.form.invalid) {
      this.form.markAllAsTouched();

      this.errorMessage.set(
        'Please correct the highlighted fields.',
      );

      return;
    }

    this.submitting.set(true);

    const raw = this.form.getRawValue();

    const payload = {
      type: raw.type,

      sku:
        raw.sku.trim()
          ? raw.sku.trim()
          : null,

      name: raw.name.trim(),

      description:
        raw.description.trim()
          ? raw.description.trim()
          : null,

      price: raw.price,

      currency_code:
        raw.currency_code,

      unit:
        raw.unit.trim()
          ? raw.unit.trim()
          : null,

      status: raw.status,

      taxable: raw.taxable,
    };

    const id = this.editingId();

    const request = id
      ? this.api.put(
          `/catalog-items/${id}`,
          payload,
        )
      : this.api.post(
          '/catalog-items',
          payload,
        );

    request.subscribe({
      next: () => {

        this.successMessage.set(
          id
            ? 'Item updated successfully.'
            : 'Item created successfully.',
        );

        this.submitting.set(false);

        this.resetForm();

        this.load();
      },

      error: (
        error: HttpErrorResponse,
      ) => {

        this.errorMessage.set(
          this.message(error),
        );

        this.submitting.set(false);
      },
    });
  }

  edit(item: CatalogItem): void {

    this.editingId.set(item.id);

    this.form.setValue({
      type: item.type,
      sku: item.sku ?? '',
      name: item.name,
      description:
        item.description ?? '',
      price: Number(item.price),
      currency_code:
        item.currency_code,
      unit: item.unit ?? '',
      status: item.status,
      taxable: item.taxable,
    });

    window.scrollTo({
      top: 0,
      behavior: 'smooth',
    });
  }

  cancelEdit(): void {
    this.resetForm();
  }

  remove(item: CatalogItem): void {

    const confirmed = window.confirm(
      `Delete "${item.name}"?`,
    );

    if (!confirmed) {
      return;
    }

    this.errorMessage.set('');
    this.successMessage.set('');

    this.api
      .delete(
        `/catalog-items/${item.id}`,
      )
      .subscribe({
        next: () => {

          this.successMessage.set(
            'Item deleted successfully.',
          );

          if (
            this.editingId()
            === item.id
          ) {
            this.resetForm();
          }

          this.load();
        },

        error: (
          error: HttpErrorResponse,
        ) => {
          this.errorMessage.set(
            this.message(error),
          );
        },
      });
  }

  private resetForm(): void {

    this.editingId.set(null);

    this.form.reset({
      type: 'product',
      sku: '',
      name: '',
      description: '',
      price: 0,
      currency_code: 'EUR',
      unit: '',
      status: 'active',
      taxable: false,
    });
  }

  private message(
    error: HttpErrorResponse,
  ): string {

    const validation =
      error.error?.errors
        ? Object
            .values(error.error.errors)
            .flat()
            .join(' ')
        : '';

    if (validation) {
      return validation;
    }

    if (error.status === 403) {
      return 'You do not have permission to perform this action.';
    }

    return (
      error.error?.message
      || 'An unexpected error occurred.'
    );
  }
}