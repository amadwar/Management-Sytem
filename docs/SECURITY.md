# Security Baseline

- Passwords use Laravel hashing.
- API authentication uses Sanctum personal access tokens.
- Tenant context is derived from authenticated identity.
- Authorization is enforced through permissions middleware and server queries.
- Mutating operations create audit entries.
- API throttling is enabled.
- Sensitive values stay in `.env` and must not be committed.
- Tenant identifiers are public UUIDs; internal numeric IDs are not exposed by API resources.
- Database IDs are not a security boundary; authorization and tenant isolation are.

Before production add:
- TLS termination
- secure cookies if switching to first-party SPA cookie auth
- secret manager
- managed backups
- WAF / edge rate limiting
- centralized logs/alerts
- dependency and container scanning
- 2FA implementation
