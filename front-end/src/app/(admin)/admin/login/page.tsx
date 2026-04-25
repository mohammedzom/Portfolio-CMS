export default function AdminLoginPage() {
  return (
    <div className="flex min-h-[calc(100dvh-3rem)] items-center justify-center">
      <section className="w-full max-w-md rounded-lg border border-border bg-surface p-6">
        <p className="text-sm font-medium uppercase tracking-wide text-primary">
          Admin
        </p>
        <h1 className="mt-3 text-2xl font-semibold text-surface-foreground">
          Sign in
        </h1>
        <p className="mt-3 text-sm leading-6 text-muted-foreground">
          Login form wiring will be implemented in Step 4 against
          `POST /api/v1/admin/login`.
        </p>
      </section>
    </div>
  );
}
