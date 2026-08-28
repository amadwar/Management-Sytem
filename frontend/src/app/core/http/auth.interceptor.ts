import { HttpInterceptorFn } from '@angular/common/http';

const TENANT_TOKEN_KEY = 'mdr_token';
const PLATFORM_TOKEN_KEY = 'mdr_platform_token';

export const authInterceptor: HttpInterceptorFn = (request, next) => {
  // Do not inject AuthService here: both auth services depend on HttpClient,
  // which would create a circular dependency while HttpClient builds its interceptor chain.
  const isPlatformRequest = request.url.includes('/platform/');
  const token = localStorage.getItem(
    isPlatformRequest ? PLATFORM_TOKEN_KEY : TENANT_TOKEN_KEY,
  );

  const headers: Record<string, string> = {
    'Accept-Language': document.documentElement.lang || 'en',
  };

  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  return next(request.clone({ setHeaders: headers }));
};
