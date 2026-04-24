"use client";

import { AxiosError } from "axios";
import { AnimatePresence, motion } from "framer-motion";
import {
  ArrowUp,
  Briefcase,
  ChevronDown,
  Code,
  ExternalLink,
  GitBranch,
  Layers,
  Loader2,
  Mail,
  MapPin,
  Send,
  Sparkles,
  Star,
  User,
} from "lucide-react";
import Link from "next/link";
import { type ReactNode, useEffect, useMemo, useState } from "react";

import { axiosClient, type ApiResponse } from "@/lib/api/client";

type SocialLink = {
  name: string;
  url: string;
  icon?: string;
};

type PortfolioProject = {
  id: number;
  title: string;
  description: string;
  category: string | null;
  tech_stack: string[] | null;
  images: string[];
  live_url: string | null;
  repo_url: string | null;
};

type PortfolioSkill = {
  id: number;
  name: string;
  proficiency: number;
  icon?: string | null;
  type?: string | null;
};

type PortfolioService = {
  id: number;
  title?: string;
  name?: string;
  description: string;
  icon?: string | null;
};

type PortfolioSettings = {
  first_name?: string;
  last_name?: string;
  full_name?: string;
  tagline?: string;
  bio?: string;
  about_me?: string;
  avatar?: string;
  email?: string;
  location?: string;
  projects_count?: number;
  years_experience?: number;
  clients_count?: number;
  social_links?: SocialLink[];
  site_title?: string;
  hero_description?: string;
  contact_email?: string;
};

type PortfolioData = {
  projects: PortfolioProject[];
  skills: PortfolioSkill[];
  services: PortfolioService[];
  experiences: unknown[];
  settings: PortfolioSettings;
};

type PortfolioApiData = {
  projects?: PortfolioProject[];
  skills?: PortfolioSkill[] | { technical?: PortfolioSkill[]; tool?: PortfolioSkill[]; tools?: PortfolioSkill[] };
  services?: PortfolioService[];
  experiences?: unknown[];
  settings?: PortfolioSettings;
  information?: PortfolioSettings;
};

const PORTFOLIO_ENDPOINT = "http://127.0.0.1:8000/api/v1/portfolio";
const FALLBACK_PORTFOLIO_ENDPOINT = "/portfolio";
const PROJECTS_PER_PAGE = 6;
const INITIAL_SKILLS_LIMIT = 9;

type ContactFormValues = {
  name: string;
  email: string;
  subject: string;
  body: string;
};

const navItems = [
  { href: "#about", label: "About" },
  { href: "#skills", label: "Skills" },
  { href: "#services", label: "Services" },
  { href: "#projects", label: "Projects" },
  { href: "#contact", label: "Contact" },
];

const initialContactForm: ContactFormValues = {
  name: "",
  email: "",
  subject: "",
  body: "",
};

function normalizePortfolio(payload: unknown): PortfolioData {
  const fallback: PortfolioData = {
    projects: [],
    skills: [],
    services: [],
    experiences: [],
    settings: {},
  };

  if (!payload || typeof payload !== "object") {
    return fallback;
  }

  const raw = payload as PortfolioApiData;
  const mergedSkills = Array.isArray(raw.skills)
    ? raw.skills
    : [...(raw.skills?.technical ?? []), ...(raw.skills?.tool ?? []), ...(raw.skills?.tools ?? [])];

  return {
    projects: Array.isArray(raw.projects) ? raw.projects : [],
    skills: mergedSkills,
    services: Array.isArray(raw.services) ? raw.services : [],
    experiences: Array.isArray(raw.experiences) ? raw.experiences : [],
    settings: raw.settings ?? raw.information ?? {},
  };
}

function SectionHeader({ label, title, description }: { label: string; title: string; description: string }) {
  return (
    <div className="mb-8">
      <p className="mb-2 text-xs uppercase tracking-widest text-violet-300/90">{label}</p>
      <h2 className="text-3xl font-semibold text-white sm:text-4xl">{title}</h2>
      <p className="mt-3 max-w-2xl text-sm text-slate-300 sm:text-base">{description}</p>
    </div>
  );
}

function Reveal({ children, className = "" }: { children: ReactNode; className?: string }) {
  return (
    <motion.div
      initial={{ opacity: 0, y: 24 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true, margin: "-100px" }}
      transition={{ duration: 0.45, ease: "easeOut" }}
      className={className}
    >
      {children}
    </motion.div>
  );
}

export default function PublicPortfolioPage() {
  const [portfolio, setPortfolio] = useState<PortfolioData | null>(null);
  const [isLoading, setIsLoading] = useState<boolean>(true);
  const [errorMessage, setErrorMessage] = useState<string>("");

  const [activeProjectCategory, setActiveProjectCategory] = useState<string>("All");
  const [currentProjectPage, setCurrentProjectPage] = useState<number>(1);
  const [showAllSkills, setShowAllSkills] = useState<boolean>(false);
  const [isNavbarScrolled, setIsNavbarScrolled] = useState<boolean>(false);
  const [showScrollTop, setShowScrollTop] = useState<boolean>(false);

  const [contactForm, setContactForm] = useState<ContactFormValues>(initialContactForm);
  const [isSending, setIsSending] = useState<boolean>(false);
  const [contactSuccess, setContactSuccess] = useState<string>("");

  useEffect(() => {
    const fetchPortfolio = async (): Promise<void> => {
      setIsLoading(true);
      setErrorMessage("");

      const endpoints = [PORTFOLIO_ENDPOINT, FALLBACK_PORTFOLIO_ENDPOINT];
      let lastError: AxiosError<ApiResponse<null>> | null = null;

      try {
        for (const endpoint of endpoints) {
          try {
            const response = await axiosClient.get<ApiResponse<unknown>>(endpoint, {
              withCredentials: false,
            });

            setPortfolio(normalizePortfolio(response.data.data));
            lastError = null;
            break;
          } catch (error) {
            lastError = error as AxiosError<ApiResponse<null>>;
          }
        }

        if (lastError) {
          const status = lastError.response?.status;
          const backendMessage = lastError.response?.data?.message;

          setErrorMessage(
            backendMessage ??
              (status
                ? `Failed to load portfolio data (HTTP ${status}).`
                : "Failed to load portfolio data. Check API server and CORS settings."),
          );
        }
      } finally {
        setIsLoading(false);
      }
    };

    void fetchPortfolio();
  }, []);

  useEffect(() => {
    const handleScroll = (): void => {
      const top = window.scrollY;
      setIsNavbarScrolled(top > 20);
      setShowScrollTop(top > 500);
    };

    handleScroll();
    window.addEventListener("scroll", handleScroll, { passive: true });

    return () => {
      window.removeEventListener("scroll", handleScroll);
    };
  }, []);

  const fullName =
    portfolio?.settings.full_name ??
    [portfolio?.settings.first_name, portfolio?.settings.last_name].filter(Boolean).join(" ") ??
    "Portfolio Owner";

  const heroTitle = portfolio?.settings.site_title ?? fullName;
  const heroDescription =
    portfolio?.settings.hero_description ??
    portfolio?.settings.tagline ??
    portfolio?.settings.bio ??
    "Crafting premium digital experiences with precision and aesthetic clarity.";

  const categories = useMemo<string[]>(() => {
    const projectCategories = portfolio?.projects.map((project) => project.category ?? "Other") ?? [];
    return ["All", ...Array.from(new Set(projectCategories))];
  }, [portfolio?.projects]);

  const filteredProjects = useMemo(() => {
    if (!portfolio) {
      return [];
    }

    if (activeProjectCategory === "All") {
      return portfolio.projects;
    }

    return portfolio.projects.filter((project) => (project.category ?? "Other") === activeProjectCategory);
  }, [activeProjectCategory, portfolio]);

  const totalProjectPages = useMemo<number>(() => {
    return Math.max(1, Math.ceil(filteredProjects.length / PROJECTS_PER_PAGE));
  }, [filteredProjects.length]);

  const paginatedProjects = useMemo<PortfolioProject[]>(() => {
    const startIndex = (currentProjectPage - 1) * PROJECTS_PER_PAGE;
    return filteredProjects.slice(startIndex, startIndex + PROJECTS_PER_PAGE);
  }, [currentProjectPage, filteredProjects]);

  useEffect(() => {
    setCurrentProjectPage(1);
  }, [activeProjectCategory]);

  useEffect(() => {
    setCurrentProjectPage((previousPage) => {
      return Math.min(previousPage, totalProjectPages);
    });
  }, [totalProjectPages]);

  const allSkills = portfolio?.skills ?? [];
  const hasExtraSkills = allSkills.length > INITIAL_SKILLS_LIMIT;
  const primarySkills = allSkills.slice(0, INITIAL_SKILLS_LIMIT);
  const extraSkills = allSkills.slice(INITIAL_SKILLS_LIMIT);

  const statItems = [
    {
      label: "Projects",
      value: portfolio?.settings.projects_count ?? portfolio?.projects.length ?? 0,
      icon: Layers,
      glow: "shadow-[0_0_28px_rgba(99,102,241,0.28)]",
    },
    {
      label: "Clients",
      value: portfolio?.settings.clients_count ?? 0,
      icon: User,
      glow: "shadow-[0_0_28px_rgba(139,92,246,0.28)]",
    },
    {
      label: "Experience",
      value: `${portfolio?.settings.years_experience ?? 0}+`,
      icon: Star,
      glow: "shadow-[0_0_28px_rgba(129,140,248,0.28)]",
    },
  ];

  const submitContact = async (): Promise<void> => {
    setIsSending(true);
    setErrorMessage("");
    setContactSuccess("");

    try {
      await axiosClient.post("/messages", contactForm);
      setContactSuccess("Message sent successfully.");
      setContactForm(initialContactForm);
    } catch (error) {
      const axiosError = error as AxiosError<ApiResponse<null>>;
      setErrorMessage(axiosError.response?.data?.message ?? "Unable to send your message right now.");
    } finally {
      setIsSending(false);
    }
  };

  const renderSkillCard = (skill: PortfolioSkill, index: number): ReactNode => {
    const SkillIcon = [Sparkles, Code, Layers][index % 3];

    return (
      <motion.article
        key={skill.id}
        whileHover={{ scale: 1.02 }}
        transition={{ duration: 0.2 }}
        className="rounded-2xl border border-white/10 bg-slate-900/40 p-4 backdrop-blur-md hover:shadow-[0_0_24px_rgba(99,102,241,0.2)]"
      >
        <div className="mb-4 flex items-center justify-between">
          <div className="inline-flex rounded-xl border border-white/10 bg-slate-800/60 p-2">
            <SkillIcon className="size-4 text-violet-200" />
          </div>
          <span className="text-xs text-emerald-300">{skill.proficiency}%</span>
        </div>
        <p className="text-sm font-medium text-white">{skill.name}</p>
        <div className="mt-3 h-2 rounded-full bg-slate-800">
          <div
            className="h-2 rounded-full bg-gradient-to-r from-violet-500 to-indigo-400 shadow-[0_0_14px_rgba(99,102,241,0.55)]"
            style={{ width: `${Math.min(Math.max(skill.proficiency, 0), 100)}%` }}
          />
        </div>
      </motion.article>
    );
  };

  if (isLoading) {
    return (
      <main className="relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-950 text-slate-100">
        <div className="absolute inset-0 bg-[radial-gradient(circle_at_10%_20%,rgba(99,102,241,0.2),transparent_35%),radial-gradient(circle_at_85%_10%,rgba(139,92,246,0.2),transparent_35%)]" />
        <div className="relative z-10 inline-flex items-center gap-2 rounded-xl border border-white/10 bg-slate-900/40 px-4 py-3 backdrop-blur-md">
          <Loader2 className="size-4 animate-spin text-violet-300" />
          <span>Loading portfolio...</span>
        </div>
      </main>
    );
  }

  return (
    <main className="relative min-h-screen overflow-x-clip bg-slate-950 text-slate-100">
      <div className="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div className="absolute -left-24 top-8 h-72 w-72 rounded-full bg-violet-500/25 blur-[120px]" />
        <div className="absolute right-[-60px] top-20 h-80 w-80 rounded-full bg-indigo-500/20 blur-[130px]" />
        <div className="absolute bottom-10 left-1/3 h-72 w-72 rounded-full bg-fuchsia-500/10 blur-[120px]" />
      </div>

      <header
        className={`fixed inset-x-0 top-0 z-50 transition-all duration-300 ${
          isNavbarScrolled
            ? "border-b border-white/10 bg-slate-900/40 backdrop-blur-md"
            : "bg-transparent"
        }`}
      >
        <div className="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
          <Link href="#" className="text-sm font-semibold tracking-wide text-white">
            {fullName}
          </Link>
          <nav className="hidden items-center gap-6 md:flex">
            {navItems.map((item) => (
              <a key={item.href} href={item.href} className="text-sm text-slate-300 transition hover:text-white">
                {item.label}
              </a>
            ))}
          </nav>
          <a
            href="#contact"
            className="rounded-full border border-white/10 bg-slate-900/40 px-4 py-2 text-xs text-white backdrop-blur-md transition hover:shadow-[0_0_24px_rgba(139,92,246,0.35)]"
          >
            Let’s Talk
          </a>
        </div>
      </header>

      <section className="relative isolate mx-auto flex min-h-screen w-full max-w-7xl items-center px-4 pb-20 pt-32 sm:px-6 lg:px-8">
        <div className="absolute inset-0 -z-10 bg-[linear-gradient(to_right,rgba(255,255,255,0.03)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.03)_1px,transparent_1px)] bg-[size:42px_42px] opacity-[0.03]" />

        <div className="grid w-full items-center gap-10 lg:grid-cols-12">
          <Reveal className="lg:col-span-7">
            <p className="mb-4 text-xs uppercase tracking-widest text-violet-300">Creative Developer Portfolio</p>
            <motion.h1
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.5 }}
              className="text-4xl font-semibold leading-tight text-white sm:text-5xl lg:text-6xl"
            >
              {heroTitle}
              <span className="block bg-gradient-to-r from-violet-300 via-indigo-300 to-violet-200 bg-clip-text text-transparent">
                {portfolio?.settings.tagline ?? "Front-end Developer & UI Designer"}
              </span>
            </motion.h1>
            <p className="mt-6 max-w-2xl text-base leading-7 text-slate-300">{heroDescription}</p>

            <div className="mt-8 flex flex-wrap items-center gap-3">
              <a
                href="#projects"
                className="rounded-xl bg-violet-500 px-5 py-3 text-sm font-medium text-white transition hover:scale-[1.02] hover:bg-violet-400 hover:shadow-[0_0_30px_rgba(139,92,246,0.35)]"
              >
                View Projects
              </a>
              <a
                href="#contact"
                className="rounded-xl border border-white/10 bg-slate-900/40 px-5 py-3 text-sm text-slate-100 backdrop-blur-md transition hover:border-indigo-300/40 hover:text-white"
              >
                Hire Me
              </a>
            </div>
          </Reveal>

          <Reveal className="grid gap-4 sm:grid-cols-3 lg:col-span-5 lg:grid-cols-1">
            {statItems.map((item, index) => {
              const Icon = item.icon;
              return (
                <motion.div
                  key={item.label}
                  initial={{ opacity: 0, y: 10 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: index * 0.12 + 0.2, duration: 0.35 }}
                  className={`rounded-2xl border border-white/10 bg-slate-900/40 p-4 backdrop-blur-md ${item.glow}`}
                >
                  <div className="mb-2 inline-flex rounded-lg bg-white/5 p-2">
                    <Icon className="size-4 text-violet-200" />
                  </div>
                  <p className="text-2xl font-semibold text-white">{item.value}</p>
                  <p className="text-xs uppercase tracking-wider text-slate-300">{item.label}</p>
                </motion.div>
              );
            })}
          </Reveal>
        </div>

        <a
          href="#about"
          className="absolute bottom-6 left-1/2 inline-flex -translate-x-1/2 flex-col items-center gap-1 text-xs uppercase tracking-widest text-slate-400"
        >
          Scroll
          <ChevronDown className="size-4 animate-bounce text-violet-300" />
        </a>
      </section>

      <section id="about" className="mx-auto w-full max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <Reveal>
          <SectionHeader
            label="About"
            title="A refined blend of code, design, and product thinking"
            description={
              portfolio?.settings.about_me ??
              portfolio?.settings.bio ??
              "I build premium interfaces that are both beautiful and performant, shaped by real product goals."
            }
          />
        </Reveal>

        <div className="grid gap-6 lg:grid-cols-2">
          <Reveal>
            <div className="relative h-[420px] overflow-hidden rounded-3xl border border-white/10 bg-slate-900/40 p-3 backdrop-blur-md">
              <div className="h-full rounded-2xl border border-violet-300/20 bg-slate-900/60 p-3">
                <div
                  className="h-full w-full rounded-xl bg-cover bg-center"
                  style={{
                    backgroundImage: portfolio?.settings.avatar
                      ? `url(${portfolio.settings.avatar})`
                      : "linear-gradient(135deg, rgba(99,102,241,0.35), rgba(139,92,246,0.2))",
                  }}
                />
              </div>
            </div>
          </Reveal>

          <Reveal className="grid gap-4 sm:grid-cols-2">
            <div className="rounded-2xl border border-white/10 bg-slate-900/40 p-4 backdrop-blur-md">
              <p className="text-xs uppercase tracking-wider text-slate-400">Location</p>
              <div className="mt-3 flex items-center gap-2">
                <MapPin className="size-4 text-indigo-300" />
                <p className="text-sm text-white">{portfolio?.settings.location ?? "Remote"}</p>
              </div>
            </div>
            <div className="rounded-2xl border border-white/10 bg-slate-900/40 p-4 backdrop-blur-md">
              <p className="text-xs uppercase tracking-wider text-slate-400">Contact</p>
              <div className="mt-3 flex items-center gap-2">
                <Mail className="size-4 text-violet-300" />
                <p className="text-sm text-white">
                  {portfolio?.settings.contact_email ?? portfolio?.settings.email ?? "hello@example.com"}
                </p>
              </div>
            </div>
            <div className="rounded-2xl border border-white/10 bg-slate-900/40 p-4 backdrop-blur-md">
              <p className="text-xs uppercase tracking-wider text-slate-400">Experience</p>
              <div className="mt-3 flex items-center gap-2">
                <Briefcase className="size-4 text-indigo-300" />
                <p className="text-sm text-white">{portfolio?.settings.years_experience ?? 0}+ Years</p>
              </div>
            </div>
            <div className="rounded-2xl border border-white/10 bg-slate-900/40 p-4 backdrop-blur-md">
              <p className="text-xs uppercase tracking-wider text-slate-400">Projects Delivered</p>
              <div className="mt-3 flex items-center gap-2">
                <Code className="size-4 text-violet-300" />
                <p className="text-sm text-white">
                  {portfolio?.settings.projects_count ?? portfolio?.projects.length ?? 0}
                </p>
              </div>
            </div>
          </Reveal>
        </div>
      </section>

      <section id="skills" className="mx-auto w-full max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <Reveal>
          <SectionHeader
            label="Skills"
            title="Core capabilities"
            description="From UI systems to robust frontend architecture, every skill is tuned for modern product delivery."
          />
        </Reveal>

        <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
          {primarySkills.map((skill, index) => renderSkillCard(skill, index))}
        </div>

        <AnimatePresence initial={false}>
          {showAllSkills && hasExtraSkills ? (
            <motion.div
              key="expanded-skills"
              initial={{ height: 0, opacity: 0 }}
              animate={{ height: "auto", opacity: 1 }}
              exit={{ height: 0, opacity: 0 }}
              transition={{ duration: 0.35, ease: "easeInOut" }}
              className="overflow-hidden"
            >
              <div className="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                {extraSkills.map((skill, index) => renderSkillCard(skill, index + INITIAL_SKILLS_LIMIT))}
              </div>
            </motion.div>
          ) : null}
        </AnimatePresence>

        {hasExtraSkills ? (
          <div className="mt-6 flex justify-center">
            <button
              type="button"
              onClick={() => {
                setShowAllSkills((previous) => !previous);
              }}
              className="rounded-xl border border-white/10 bg-slate-900/50 px-5 py-2.5 text-sm text-slate-100 backdrop-blur-md transition hover:border-violet-400/70 hover:text-white hover:shadow-[0_0_22px_rgba(139,92,246,0.28)]"
            >
              {showAllSkills ? "Show Less" : "Show All"}
            </button>
          </div>
        ) : null}
      </section>

      <section id="services" className="mx-auto w-full max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <Reveal>
          <SectionHeader
            label="Services"
            title="What I deliver"
            description="End-to-end support from concept to polished launch, with detail-first execution."
          />
        </Reveal>

        <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
          {(portfolio?.services ?? []).map((service, index) => {
            const ServiceIcon = [Briefcase, Sparkles, Code][index % 3];

            return (
              <motion.article
                key={service.id}
                whileHover={{ scale: 1.02 }}
                transition={{ duration: 0.22 }}
                className="group rounded-2xl border border-white/10 bg-slate-900/40 p-5 backdrop-blur-md"
              >
                <div className="mb-4 inline-flex rounded-xl border border-white/10 bg-slate-800/70 p-2 text-violet-200 transition group-hover:shadow-[0_0_18px_rgba(139,92,246,0.45)]">
                  <ServiceIcon className="size-5" />
                </div>
                <h3 className="text-lg font-medium text-white">{service.title ?? service.name ?? "Service"}</h3>
                <p className="mt-2 text-sm leading-6 text-slate-300">{service.description}</p>
              </motion.article>
            );
          })}
        </div>
      </section>

      <section id="projects" className="mx-auto w-full max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <Reveal>
          <SectionHeader
            label="Projects"
            title="Selected work"
            description="A filterable showcase of shipped products and crafted digital experiences."
          />
        </Reveal>

        <div className="mb-6 flex flex-wrap gap-2">
          {categories.map((category) => (
            <button
              key={category}
              type="button"
              onClick={() => {
                setActiveProjectCategory(category);
              }}
              className={`rounded-full px-4 py-2 text-xs uppercase tracking-wider transition ${
                activeProjectCategory === category
                  ? "bg-violet-500 text-white shadow-[0_0_20px_rgba(139,92,246,0.35)]"
                  : "border border-white/10 bg-slate-900/40 text-slate-300"
              }`}
            >
              {category}
            </button>
          ))}
        </div>

        <div className="columns-1 gap-4 md:columns-2 xl:columns-3">
          {paginatedProjects.map((project) => {
            const previewImage = project.images?.[0];
            return (
              <motion.article
                key={project.id}
                whileHover={{ scale: 1.01 }}
                className="group relative mb-4 break-inside-avoid overflow-hidden rounded-2xl border border-white/10 bg-slate-900/40 backdrop-blur-md"
              >
                <div
                  className="h-64 w-full bg-cover bg-center"
                  style={{
                    backgroundImage: previewImage
                      ? `url(${previewImage})`
                      : "linear-gradient(135deg, rgba(76,29,149,0.45), rgba(67,56,202,0.38))",
                  }}
                />
                <div className="p-4">
                  <p className="text-xs uppercase tracking-widest text-violet-300/90">{project.category ?? "Project"}</p>
                  <h3 className="mt-2 text-lg font-semibold text-white">{project.title}</h3>
                  <p className="mt-2 text-sm text-slate-300">{project.description}</p>
                </div>

                <div className="absolute inset-x-0 bottom-0 translate-y-full border-t border-white/10 bg-slate-950/85 p-4 backdrop-blur-md transition-transform duration-300 group-hover:translate-y-0">
                  <div className="mb-3 flex flex-wrap gap-1.5">
                    {(Array.isArray(project.tech_stack) ? project.tech_stack : []).map((tech) => (
                      <span
                        key={`${project.id}-${tech}`}
                        className="rounded-full border border-indigo-400/35 bg-indigo-500/10 px-2 py-1 text-[10px] text-indigo-200"
                      >
                        {tech}
                      </span>
                    ))}
                  </div>
                  <div className="flex gap-2">
                    <a
                      href={project.live_url ?? "#"}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="inline-flex items-center gap-1 rounded-lg bg-violet-500 px-3 py-2 text-xs text-white"
                    >
                      <ExternalLink className="size-3.5" />
                      Live Demo
                    </a>
                    <a
                      href={project.repo_url ?? "#"}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="inline-flex items-center gap-1 rounded-lg border border-white/10 bg-slate-900/50 px-3 py-2 text-xs text-slate-100"
                    >
                      <GitBranch className="size-3.5" />
                      Github
                    </a>
                  </div>
                </div>
              </motion.article>
            );
          })}
        </div>

        <div className="mt-8 flex items-center justify-center gap-4">
          <button
            type="button"
            disabled={currentProjectPage <= 1}
            onClick={() => {
              setCurrentProjectPage((previousPage) => Math.max(1, previousPage - 1));
            }}
            className="rounded-xl border border-white/10 bg-slate-900/50 px-5 py-2.5 text-sm text-slate-100 backdrop-blur-md transition hover:border-violet-400/80 hover:shadow-[0_0_20px_rgba(139,92,246,0.35)] disabled:cursor-not-allowed disabled:opacity-35"
          >
            Previous
          </button>

          <div className="rounded-xl border border-white/10 bg-slate-900/45 px-4 py-2 text-sm text-slate-200 backdrop-blur-md">
            Page {currentProjectPage} of {totalProjectPages}
          </div>

          <button
            type="button"
            disabled={currentProjectPage >= totalProjectPages}
            onClick={() => {
              setCurrentProjectPage((previousPage) => Math.min(totalProjectPages, previousPage + 1));
            }}
            className="rounded-xl border border-white/10 bg-slate-900/50 px-5 py-2.5 text-sm text-slate-100 backdrop-blur-md transition hover:border-violet-400/80 hover:shadow-[0_0_20px_rgba(139,92,246,0.35)] disabled:cursor-not-allowed disabled:opacity-35"
          >
            Next
          </button>
        </div>
      </section>

      <section id="contact" className="mx-auto w-full max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <Reveal>
          <SectionHeader
            label="Contact"
            title="Let’s build something memorable"
            description="Share your idea, timeline, and goals. I’ll reply with a focused plan and next steps."
          />
        </Reveal>

        <Reveal className="rounded-3xl border border-white/10 bg-slate-900/40 p-6 backdrop-blur-md sm:p-8">
          <form
            className="grid gap-4 sm:grid-cols-2"
            onSubmit={(event) => {
              event.preventDefault();
              void submitContact();
            }}
          >
            <label className="space-y-1.5">
              <span className="text-sm text-slate-300">Name</span>
              <input
                required
                value={contactForm.name}
                onChange={(event) => {
                  setContactForm((previous) => ({ ...previous, name: event.target.value }));
                }}
                className="h-11 w-full rounded-xl border border-white/10 bg-slate-900/60 px-3 text-sm text-white outline-none transition focus:border-violet-400 focus:shadow-[0_0_16px_rgba(139,92,246,0.3)]"
              />
            </label>
            <label className="space-y-1.5">
              <span className="text-sm text-slate-300">Email</span>
              <input
                required
                type="email"
                value={contactForm.email}
                onChange={(event) => {
                  setContactForm((previous) => ({ ...previous, email: event.target.value }));
                }}
                className="h-11 w-full rounded-xl border border-white/10 bg-slate-900/60 px-3 text-sm text-white outline-none transition focus:border-indigo-400 focus:shadow-[0_0_16px_rgba(99,102,241,0.3)]"
              />
            </label>
            <label className="space-y-1.5 sm:col-span-2">
              <span className="text-sm text-slate-300">Subject</span>
              <input
                required
                value={contactForm.subject}
                onChange={(event) => {
                  setContactForm((previous) => ({ ...previous, subject: event.target.value }));
                }}
                className="h-11 w-full rounded-xl border border-white/10 bg-slate-900/60 px-3 text-sm text-white outline-none transition focus:border-violet-400 focus:shadow-[0_0_16px_rgba(139,92,246,0.3)]"
              />
            </label>
            <label className="space-y-1.5 sm:col-span-2">
              <span className="text-sm text-slate-300">Message</span>
              <textarea
                required
                rows={6}
                value={contactForm.body}
                onChange={(event) => {
                  setContactForm((previous) => ({ ...previous, body: event.target.value }));
                }}
                className="w-full rounded-xl border border-white/10 bg-slate-900/60 px-3 py-2 text-sm text-white outline-none transition focus:border-indigo-400 focus:shadow-[0_0_16px_rgba(99,102,241,0.3)]"
              />
            </label>

            <div className="sm:col-span-2">
              <button
                type="submit"
                disabled={isSending}
                className="inline-flex h-11 items-center gap-2 rounded-xl bg-violet-500 px-5 font-medium text-white transition hover:bg-violet-400 hover:shadow-[0_0_28px_rgba(139,92,246,0.35)] disabled:opacity-70"
              >
                {isSending ? <Loader2 className="size-4 animate-spin" /> : <Send className="size-4" />}
                {isSending ? "Sending..." : "Send Message"}
              </button>
            </div>
          </form>
        </Reveal>

        <AnimatePresence>
          {contactSuccess ? (
            <motion.div
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: 10 }}
              className="fixed bottom-6 left-1/2 z-50 -translate-x-1/2 rounded-xl border border-emerald-400/35 bg-emerald-500/15 px-4 py-3 text-sm text-emerald-100 backdrop-blur-md"
            >
              {contactSuccess}
            </motion.div>
          ) : null}
        </AnimatePresence>
      </section>

      {showScrollTop ? (
        <button
          type="button"
          onClick={() => {
            window.scrollTo({ top: 0, behavior: "smooth" });
          }}
          className="fixed bottom-5 right-5 z-50 inline-flex h-11 w-11 items-center justify-center rounded-full bg-violet-500 text-white shadow-[0_0_24px_rgba(139,92,246,0.45)] transition hover:scale-105"
        >
          <ArrowUp className="size-4" />
        </button>
      ) : null}

      {errorMessage ? (
        <div className="fixed bottom-6 right-6 z-50 max-w-sm rounded-xl border border-rose-500/35 bg-rose-500/10 px-4 py-3 text-sm text-rose-200 backdrop-blur-md">
          {errorMessage}
        </div>
      ) : null}
    </main>
  );
}
