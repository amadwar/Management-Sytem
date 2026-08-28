import { ChangeDetectionStrategy, Component, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { PlatformAuthService } from '../../core/auth/platform-auth.service';

@Component({
  selector: 'app-platform-login',
  imports: [ReactiveFormsModule],
  template: `
    <main class="login-page">
      <form class="card login-card" [formGroup]="form" (ngSubmit)="submit()">
        <h1>Platform Administration</h1>
        <p>Super Admin access.</p>
        <label>Email<input formControlName="email" type="email"></label>
        <label>Password<input formControlName="password" type="password"></label>
        @if (error()) { <div class="error">{{ error() }}</div> }
        <button class="primary" [disabled]="form.invalid || loading()">
          {{ loading() ? 'Signing in...' : 'Sign in' }}
        </button>
      </form>
    </main>
  `,
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class PlatformLoginComponent {
  readonly loading = signal(false);
  readonly error = signal('');

  readonly form = new FormGroup({
    email: new FormControl('', {
      nonNullable: true,
      validators: [Validators.required, Validators.email],
    }),
    password: new FormControl('', {
      nonNullable: true,
      validators: [Validators.required],
    }),
  });

  constructor(
    private readonly auth: PlatformAuthService,
    private readonly router: Router,
  ) {}

  submit(): void {
    if (this.form.invalid || this.loading()) return;

    this.loading.set(true);
    this.error.set('');

    this.auth.login(this.form.getRawValue()).subscribe({
      next: () => void this.router.navigateByUrl('/platform/tenants'),
      error: (error: HttpErrorResponse) => {
        this.loading.set(false);
        this.error.set(
          error.status === 0
            ? 'Cannot reach the API. Make sure Laravel is running on 127.0.0.1:8000.'
            : 'Invalid credentials.',
        );
      },
    });
  }
}
