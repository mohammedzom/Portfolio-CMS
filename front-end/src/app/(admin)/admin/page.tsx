export default function AdminDashboardPage() {
  return (
    <section className="rounded-lg border border-border bg-surface p-6">
      <p className="text-sm font-medium uppercase tracking-wide text-primary">
        Admin Dashboard
      </p>
      <h1 className="mt-3 text-3xl font-semibold text-surface-foreground">
        Dashboard scaffold
      </h1>
      <p className="mt-4 max-w-2xl text-sm leading-6 text-muted-foreground">
        The private route group is ready for authenticated dashboard screens.
        CRUD modules will be wired in Step 4 from the API map.
      </p>
    </section>
  );
}
