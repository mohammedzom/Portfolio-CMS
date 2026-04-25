import type { z } from "zod";

import {
  achievementService,
  educationService,
  experienceService,
  projectService,
  serviceService,
  skillCategoryService,
  skillService,
} from "@/services/admin-services";
import { toFormData } from "@/services/resource-service";
import type {
  Achievement,
  Education,
  Experience,
  Project,
  Service,
  Skill,
  SkillCategory,
} from "@/types/api";
import {
  achievementSchema,
  educationSchema,
  experienceSchema,
  projectSchema,
  serviceSchema,
  skillCategorySchema,
  skillSchema,
} from "./schemas";

export type FieldConfig = {
  name: string;
  label: string;
  type?: "text" | "textarea" | "number" | "select" | "checkbox" | "file" | "date";
  options?: Array<{ label: string; value: string | number }>;
};

type AdminResourceService<TResource extends { id: number }> = {
  list: (query?: { archived?: boolean; search?: string }) => Promise<TResource[]>;
  get: (id: number | string) => Promise<TResource>;
  create: (payload: Record<string, unknown> | FormData) => Promise<TResource>;
  update: (id: number | string, payload: Record<string, unknown> | FormData) => Promise<TResource>;
  archive: (id: number | string) => Promise<unknown>;
  restore: (id: number | string) => Promise<TResource>;
  forceDelete: (id: number | string) => Promise<unknown>;
};

export type AdminResourceConfig<TResource extends { id: number }> = {
  title: string;
  description: string;
  emptyMessage: string;
  columns: Array<{
    key: keyof TResource | string;
    label: string;
    render?: (resource: TResource) => React.ReactNode;
  }>;
  fields: FieldConfig[];
  schema: z.ZodType;
  service: AdminResourceService<TResource>;
  defaults: Record<string, unknown>;
  toPayload?: (values: Record<string, unknown>) => Record<string, unknown> | FormData;
  notice?: string;
};

const parseCsv = (value: unknown) =>
  String(value ?? "")
    .split(",")
    .map((item) => item.trim())
    .filter(Boolean);

export const projectConfig: AdminResourceConfig<Project> = {
  title: "Projects",
  description: "Manage portfolio projects, media, links, and display order.",
  emptyMessage: "No projects found.",
  service: projectService,
  schema: projectSchema,
  defaults: {
    title: "",
    slug: "",
    description: "",
    category: "Web",
    tech_stack: "",
    live_url: "",
    repo_url: "",
    is_featured: false,
    sort_order: "",
  },
  fields: [
    { name: "title", label: "Title" },
    { name: "slug", label: "Slug" },
    { name: "description", label: "Description", type: "textarea" },
    {
      name: "category",
      label: "Category",
      type: "select",
      options: ["Web", "App", "Mobile", "Script", "Other"].map((value) => ({
        label: value,
        value,
      })),
    },
    { name: "tech_stack", label: "Tech stack, comma separated" },
    { name: "images", label: "Images", type: "file" },
    { name: "deleted_images", label: "Deleted image URLs, comma separated" },
    { name: "live_url", label: "Live URL" },
    { name: "repo_url", label: "Repository URL" },
    { name: "is_featured", label: "Featured", type: "checkbox" },
    { name: "sort_order", label: "Sort order", type: "number" },
  ],
  columns: [
    { key: "title", label: "Title" },
    { key: "category", label: "Category" },
    { key: "is_featured", label: "Featured", render: (item) => (item.is_featured ? "Yes" : "No") },
    { key: "sort_order", label: "Order" },
  ],
  toPayload: (values) =>
    toFormData(
      {
        ...values,
        tech_stack: parseCsv(values.tech_stack),
        deleted_images: parseCsv(values.deleted_images),
      },
      ["images"],
    ),
};

export const serviceConfig: AdminResourceConfig<Service> = {
  title: "Services",
  description: "Manage offered services and their presentation tags.",
  emptyMessage: "No services found.",
  service: serviceService,
  schema: serviceSchema,
  defaults: { title: "", description: "", icon: "", sort_order: 0, tags: "" },
  fields: [
    { name: "title", label: "Title" },
    { name: "description", label: "Description", type: "textarea" },
    { name: "icon", label: "Icon URL" },
    { name: "sort_order", label: "Sort order", type: "number" },
    { name: "tags", label: "Tags, comma separated" },
  ],
  columns: [
    { key: "title", label: "Title" },
    { key: "icon", label: "Icon" },
    { key: "tags", label: "Tags", render: (item) => item.tags?.join(", ") ?? "" },
    { key: "sort_order", label: "Order" },
  ],
  toPayload: (values) => ({ ...values, tags: parseCsv(values.tags) }),
};

export const skillConfig: AdminResourceConfig<Skill> = {
  title: "Skills",
  description: "Manage skills. Category IDs must match backend skill category records.",
  emptyMessage: "No skills found.",
  service: skillService,
  schema: skillSchema,
  defaults: { name: "", icon: "", proficiency: 80, skill_category_id: 1 },
  fields: [
    { name: "name", label: "Name" },
    { name: "icon", label: "Icon URL" },
    { name: "proficiency", label: "Proficiency", type: "number" },
    { name: "skill_category_id", label: "Skill category ID", type: "number" },
  ],
  columns: [
    { key: "name", label: "Name" },
    { key: "category", label: "Category" },
    { key: "proficiency", label: "Proficiency" },
  ],
};

export const skillCategoryConfig: AdminResourceConfig<SkillCategory> = {
  title: "Skill Categories",
  description: "Prepare category management UI for pending backend endpoints.",
  emptyMessage: "No skill categories found.",
  service: skillCategoryService as AdminResourceService<SkillCategory>,
  schema: skillCategorySchema,
  defaults: { name: "", slug: "" },
  notice:
    "Skill category endpoints are pending in Laravel. The list falls back to local mock categories when the API returns 404.",
  fields: [
    { name: "name", label: "Name" },
    { name: "slug", label: "Slug" },
  ],
  columns: [
    { key: "name", label: "Name" },
    { key: "slug", label: "Slug" },
  ],
};

export const experienceConfig: AdminResourceConfig<Experience> = {
  title: "Experiences",
  description: "Manage timeline entries and current roles.",
  emptyMessage: "No experiences found.",
  service: experienceService,
  schema: experienceSchema,
  defaults: {
    job_title: "",
    company: "",
    description: "",
    start_date: new Date().getFullYear(),
    end_date: "",
    is_current: false,
  },
  fields: [
    { name: "job_title", label: "Job title" },
    { name: "company", label: "Company" },
    { name: "description", label: "Description", type: "textarea" },
    { name: "start_date", label: "Start year", type: "number" },
    { name: "end_date", label: "End year", type: "number" },
    { name: "is_current", label: "Current role", type: "checkbox" },
  ],
  columns: [
    { key: "job_title", label: "Role" },
    { key: "company", label: "Company" },
    { key: "period", label: "Period" },
  ],
};

export const educationConfig: AdminResourceConfig<Education> = {
  title: "Education",
  description: "Manage academic background.",
  emptyMessage: "No education records found.",
  service: educationService,
  schema: educationSchema,
  defaults: {
    degree: "",
    institution: "",
    field_of_study: "",
    start_year: new Date().getFullYear(),
    end_year: "",
    gpa: "",
    description: "",
  },
  fields: [
    { name: "degree", label: "Degree" },
    { name: "institution", label: "Institution" },
    { name: "field_of_study", label: "Field of study" },
    { name: "start_year", label: "Start year", type: "number" },
    { name: "end_year", label: "End year", type: "number" },
    { name: "gpa", label: "GPA", type: "number" },
    { name: "description", label: "Description", type: "textarea" },
  ],
  columns: [
    { key: "degree", label: "Degree" },
    { key: "institution", label: "Institution" },
    { key: "field_of_study", label: "Field" },
    { key: "start_year", label: "Start" },
  ],
};

export const achievementConfig: AdminResourceConfig<Achievement> = {
  title: "Achievements",
  description: "Manage certificates, awards, and proof files.",
  emptyMessage: "No achievements found.",
  service: achievementService,
  schema: achievementSchema,
  defaults: { title: "", issuer: "", date: "", url: "", description: "" },
  fields: [
    { name: "title", label: "Title" },
    { name: "issuer", label: "Issuer" },
    { name: "date", label: "Date", type: "date" },
    { name: "url", label: "URL" },
    { name: "description", label: "Description", type: "textarea" },
    { name: "file", label: "Certificate file", type: "file" },
  ],
  columns: [
    { key: "title", label: "Title" },
    { key: "issuer", label: "Issuer" },
    { key: "date", label: "Date" },
    { key: "certificate_url", label: "File", render: (item) => (item.certificate_url ? "Uploaded" : "") },
  ],
  toPayload: (values) => toFormData(values, ["file"]),
};
