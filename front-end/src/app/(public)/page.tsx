export default function PortfolioHomePage() {
  return (
    <div className="flex min-h-dvh items-center justify-center bg-background px-6 py-16">
      <section className="w-full max-w-5xl">
        <p className="text-sm font-medium uppercase tracking-wide text-primary">
          Public Portfolio
        </p>
        <h1 className="mt-4 max-w-3xl text-4xl font-semibold text-foreground sm:text-6xl">
          Portfolio CMS frontend scaffold
        </h1>
        <p className="mt-6 max-w-2xl text-base leading-7 text-muted-foreground sm:text-lg">
          This public route group is ready for the portfolio rebuild. Step 4 will
          connect it to the mapped portfolio, message, and media endpoints.
        </p>
      </section>
    </div>
  );
}
