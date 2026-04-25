"use client";

import { Code2, ExternalLink, Loader2, MapPin } from "lucide-react";
import { useEffect, useState } from "react";

import { ContactForm } from "@/features/portfolio/contact-form";
import { getPortfolio } from "@/services/portfolio-service";
import type { PortfolioData } from "@/types/api";

export function PortfolioPage({
  initialData = null,
  initialError = "",
}: {
  initialData?: PortfolioData | null;
  initialError?: string;
}) {
  const [data, setData] = useState<PortfolioData | null>(initialData);
  const [error, setError] = useState(initialError);
  const [isLoading, setIsLoading] = useState(!initialData && !initialError);

  useEffect(() => {
    if (initialData || initialError) {
      return;
    }

    let isActive = true;

    getPortfolio()
      .then((portfolio) => {
        if (isActive) {
          setData(portfolio);
        }
      })
      .catch((caughtError: unknown) => {
        console.error("Failed to fetch portfolio data:", caughtError);

        if (isActive) {
          setError(caughtError instanceof Error ? caughtError.message : "Unable to load portfolio.");
        }
      })
      .finally(() => {
        if (isActive) {
          setIsLoading(false);
        }
      });

    return () => {
      isActive = false;
    };
  }, [initialData, initialError]);

  if (error) {
    return (
      <main className="flex min-h-dvh items-center justify-center bg-zinc-950 px-6 text-red-200">
        <section className="max-w-xl rounded-lg border border-red-400/30 bg-red-500/10 p-6">
          <p className="font-mono text-xs uppercase tracking-[0.28em] text-red-300">
            Portfolio sync failed
          </p>
          <h1 className="mt-3 text-2xl font-semibold text-white">
            API connection needs attention
          </h1>
          <p className="mt-4 text-sm leading-6 text-red-100">{error}</p>
          <p className="mt-4 text-sm leading-6 text-zinc-300">
            Confirm `NEXT_PUBLIC_API_BASE_URL` points to the Laravel `/api/v1`
            endpoint and `NEXT_PUBLIC_API_KEY` matches the backend `API_KEY`.
          </p>
        </section>
      </main>
    );
  }

  if (isLoading) {
    return (
      <main className="flex min-h-dvh items-center justify-center bg-zinc-950 text-cyan-200">
        <div className="flex items-center gap-3 text-sm uppercase tracking-[0.25em]">
          <Loader2 className="animate-spin" size={18} />
          Syncing portfolio
        </div>
      </main>
    );
  }

  if (!data) {
    return (
      <main className="flex min-h-dvh items-center justify-center bg-zinc-950 px-6 text-zinc-200">
        <section className="max-w-xl rounded-lg border border-cyan-400/30 bg-cyan-500/10 p-6">
          <p className="font-mono text-xs uppercase tracking-[0.28em] text-cyan-300">
            Portfolio unavailable
          </p>
          <h1 className="mt-3 text-2xl font-semibold text-white">
            No portfolio data returned
          </h1>
          <p className="mt-4 text-sm leading-6 text-zinc-300">
            The request finished, but the API response did not include portfolio
            data. Check the browser console for the exact fetch error.
          </p>
        </section>
      </main>
    );
  }

  const info = data.information;

  return (
    <main className="min-h-dvh overflow-hidden bg-zinc-950 text-zinc-100">
      <div className="pointer-events-none fixed inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(34,211,238,0.18),transparent_28%),radial-gradient(circle_at_80%_10%,rgba(244,63,94,0.14),transparent_24%),linear-gradient(180deg,rgba(8,47,73,0.35),transparent_42%)]" />
      <div className="relative mx-auto max-w-7xl px-5 py-8 sm:px-8">
        <header className="flex items-center justify-between border-b border-cyan-400/20 pb-5">
          <div className="font-mono text-sm uppercase tracking-[0.28em] text-cyan-300">
            {info.url_prefix ?? "portfolio"}::{info.url_suffix ?? "cms"}
          </div>
          <a href="#contact" className="text-sm text-fuchsia-300 hover:text-fuchsia-200">
            Contact
          </a>
        </header>

        <section className="grid min-h-[78dvh] items-center gap-10 py-16 lg:grid-cols-[1.1fr_0.9fr]">
          <div>
            <p className="font-mono text-sm uppercase tracking-[0.32em] text-cyan-300">
              {info.tagline ?? "Full-stack engineer"}
            </p>
            <h1 className="mt-5 text-5xl font-semibold leading-tight text-white sm:text-7xl">
              {info.full_name}
            </h1>
            <p className="mt-6 max-w-2xl text-lg leading-8 text-zinc-300">
              {info.bio ?? info.about_me}
            </p>
            <div className="mt-8 flex flex-wrap gap-3 text-sm text-zinc-300">
              {info.location ? (
                <span className="inline-flex items-center gap-2 rounded-md border border-cyan-400/30 bg-cyan-400/10 px-3 py-2">
                  <MapPin size={16} />
                  {info.location}
                </span>
              ) : null}
              <span className="rounded-md border border-fuchsia-400/30 bg-fuchsia-400/10 px-3 py-2">
                {info.available_for_freelance ? "Available for freelance" : "Focused on selected work"}
              </span>
            </div>
          </div>

          <div className="rounded-lg border border-cyan-400/25 bg-zinc-900/70 p-5 shadow-[0_0_60px_rgba(34,211,238,0.12)] backdrop-blur">
            <div className="grid grid-cols-3 gap-3">
              <CyberStat label="Years" value={info.years_experience} />
              <CyberStat label="Projects" value={info.projects_count || data.projects.length} />
              <CyberStat label="Clients" value={info.clients_count} />
            </div>
            <div className="mt-5 rounded-md border border-cyan-400/20 bg-black/30 p-4 font-mono text-sm leading-7 text-cyan-100">
              {info.about_me ?? "Building precise digital systems with resilient APIs and polished interfaces."}
            </div>
          </div>
        </section>

        <Section title="Featured Projects" eyebrow="deployments">
          <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            {data.projects.map((project) => (
              <article key={project.id} className="rounded-lg border border-cyan-400/20 bg-zinc-900/70 p-5">
                <div className="text-xs uppercase tracking-[0.24em] text-fuchsia-300">{project.category}</div>
                <h3 className="mt-3 text-xl font-semibold text-white">{project.title}</h3>
                <p className="mt-3 line-clamp-4 text-sm leading-6 text-zinc-400">{project.description}</p>
                <div className="mt-4 flex flex-wrap gap-2">
                  {project.tech_stack?.slice(0, 5).map((tech) => (
                    <span key={tech} className="rounded border border-cyan-400/20 px-2 py-1 text-xs text-cyan-200">
                      {tech}
                    </span>
                  ))}
                </div>
                <div className="mt-5 flex gap-3">
                  {project.live_url ? <CyberLink href={project.live_url} icon={<ExternalLink size={15} />} label="Live" /> : null}
                  {project.repo_url ? <CyberLink href={project.repo_url} icon={<Code2 size={15} />} label="Code" /> : null}
                </div>
              </article>
            ))}
          </div>
        </Section>

        <Section title="Capabilities" eyebrow="services">
          <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            {data.services.map((service) => (
              <article key={service.id} className="rounded-lg border border-fuchsia-400/20 bg-zinc-900/70 p-5">
                <Code2 className="text-fuchsia-300" size={24} />
                <h3 className="mt-4 text-xl font-semibold">{service.title}</h3>
                <p className="mt-3 text-sm leading-6 text-zinc-400">{service.description}</p>
              </article>
            ))}
          </div>
        </Section>

        <Section title="Skill Matrix" eyebrow="systems">
          <div className="grid gap-5 md:grid-cols-2">
            {Object.entries(data.skills).map(([category, skills]) => (
              <div key={category} className="rounded-lg border border-cyan-400/20 bg-zinc-900/70 p-5">
                <h3 className="text-lg font-semibold text-cyan-200">{category}</h3>
                <div className="mt-4 grid gap-3">
                  {skills.map((skill) => (
                    <div key={skill.id}>
                      <div className="flex justify-between text-sm">
                        <span>{skill.name}</span>
                        <span className="text-cyan-300">{skill.proficiency}%</span>
                      </div>
                      <div className="mt-2 h-2 rounded bg-zinc-800">
                        <div className="h-2 rounded bg-cyan-300" style={{ width: `${skill.proficiency}%` }} />
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            ))}
          </div>
        </Section>

        <Section title="Experience" eyebrow="timeline">
          <div className="grid gap-4">
            {[...data.experiences, ...data.educations].map((item) => (
              <article key={`${"job_title" in item ? "x" : "e"}-${item.id}`} className="rounded-lg border border-cyan-400/20 bg-zinc-900/70 p-5">
                <h3 className="text-lg font-semibold">
                  {"job_title" in item ? item.job_title : item.degree}
                </h3>
                <p className="mt-1 text-sm text-fuchsia-300">
                  {"job_title" in item ? item.company : item.institution}
                </p>
                <p className="mt-3 text-sm leading-6 text-zinc-400">
                  {"period" in item ? item.period : `${item.start_year} - ${item.end_year ?? "Present"}`}
                </p>
              </article>
            ))}
          </div>
        </Section>

        <Section title="Contact" eyebrow="transmission">
          <div id="contact" className="rounded-lg border border-cyan-400/20 bg-zinc-900/70 p-5">
            <ContactForm />
          </div>
        </Section>
      </div>
    </main>
  );
}

function CyberStat({ label, value }: { label: string; value: number }) {
  return (
    <div className="rounded-md border border-cyan-400/20 bg-cyan-400/10 p-4">
      <div className="text-3xl font-semibold text-cyan-200">{value}</div>
      <div className="mt-1 text-xs uppercase tracking-[0.2em] text-zinc-400">{label}</div>
    </div>
  );
}

function Section({
  eyebrow,
  title,
  children,
}: {
  eyebrow: string;
  title: string;
  children: React.ReactNode;
}) {
  return (
    <section className="py-12">
      <p className="font-mono text-xs uppercase tracking-[0.3em] text-fuchsia-300">{eyebrow}</p>
      <h2 className="mt-3 text-3xl font-semibold text-white sm:text-4xl">{title}</h2>
      <div className="mt-6">{children}</div>
    </section>
  );
}

function CyberLink({
  href,
  icon,
  label,
}: {
  href: string;
  icon: React.ReactNode;
  label: string;
}) {
  return (
    <a
      href={href}
      target="_blank"
      rel="noreferrer"
      className="inline-flex items-center gap-2 text-sm text-cyan-200 hover:text-cyan-100"
    >
      {icon}
      {label}
    </a>
  );
}
