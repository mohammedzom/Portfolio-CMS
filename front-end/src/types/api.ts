export type ApiEnvelope<T> = {
  success: boolean;
  message: string;
  data: T;
  error_code?: string | null;
};

export type ValidationErrors = Record<string, string[]>;

export type ApiErrorPayload = ApiEnvelope<unknown | ValidationErrors | null>;

export type ProjectCategory = "Web" | "App" | "Mobile" | "Script" | "Other";

export type TimestampFields = {
  created_at: string;
  updated_at: string;
  deleted_at?: string | null;
  deleted_at_human?: string | null;
};

export type Project = TimestampFields & {
  id: number;
  title: string;
  slug: string;
  description: string | null;
  category: ProjectCategory;
  tech_stack: string[] | null;
  images: string[];
  live_url: string | null;
  repo_url: string | null;
  is_featured: boolean;
  sort_order: number;
};

export type Service = TimestampFields & {
  id: number;
  title: string;
  description: string;
  icon: string | null;
  tags?: string[];
  sort_order: number;
};

export type Skill = TimestampFields & {
  id: number;
  name: string;
  proficiency: number;
  icon: string | null;
  category: string;
};

export type Experience = TimestampFields & {
  id: number;
  job_title: string;
  company: string;
  description: string;
  period: string;
};

export type Education = TimestampFields & {
  id: number;
  degree: string;
  institution: string;
  field_of_study: string;
  start_year: number;
  end_year: number | null;
  gpa: number | null;
  description: string | null;
};

export type Achievement = TimestampFields & {
  id: number;
  title: string;
  issuer: string;
  date: string;
  url: string | null;
  description: string | null;
  certificate_url: string | null;
};

export type Message = TimestampFields & {
  id: number;
  name: string;
  email: string;
  subject: string | null;
  body: string;
  is_read: boolean;
  read_at_human: string | null;
  read_at: string | null;
};

export type SiteSettings = {
  id: number;
  first_name: string;
  last_name: string;
  full_name: string;
  tagline: string | null;
  bio: string | null;
  about_me: string | null;
  avatar: string | null;
  cv_file: string | null;
  cv_file_name: string | null;
  url_prefix: string | null;
  url_suffix: string | null;
  languages: Array<{ name: string; level: string }> | null;
  email: string | null;
  phone: string | null;
  location: string | null;
  social_links: Array<{ name: string; url: string }> | null;
  years_experience: number;
  projects_count: number;
  clients_count: number;
  available_for_freelance: boolean;
  created_at: string;
  updated_at: string;
};

export type PortfolioData = {
  skills: Record<string, Skill[]>;
  projects: Project[];
  services: Service[];
  information: SiteSettings;
  experiences: Experience[];
  achievements: Achievement[];
  educations: Education[];
};

export type PaginatedMessages = {
  messages: Message[];
  meta: {
    current_page: number;
    last_page: number;
    total: number;
    per_page: number;
  };
  paginationLinks: {
    self: string | null;
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
  };
};
