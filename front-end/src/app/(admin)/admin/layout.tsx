export default function AdminLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <div className="min-h-dvh bg-muted text-foreground">
      <div className="mx-auto flex min-h-dvh w-full max-w-7xl">
        <aside className="hidden w-64 shrink-0 border-r border-border bg-surface px-5 py-6 lg:block">
          <div className="text-sm font-semibold uppercase tracking-wide text-primary">
            Portfolio CMS
          </div>
          <nav className="mt-8 grid gap-2 text-sm text-muted-foreground">
            <span>Dashboard</span>
            <span>Projects</span>
            <span>Services</span>
            <span>Skills</span>
            <span>Messages</span>
            <span>Settings</span>
          </nav>
        </aside>
        <main className="min-w-0 flex-1 px-5 py-6 sm:px-8">{children}</main>
      </div>
    </div>
  );
}
