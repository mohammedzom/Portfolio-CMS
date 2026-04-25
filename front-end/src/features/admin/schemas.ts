import { z } from "zod";

const optionalUrl = z.union([z.url(), z.literal("")]).optional().nullable();
const optionalString = z.string().optional().nullable();
const requiredString = (label: string, max = 255) =>
  z.string().min(1, `${label} is required.`).max(max, `${label} cannot exceed ${max} characters.`);

export const loginSchema = z.object({
  email: z.email("Email must be a valid email address."),
  password: z.string().min(1, "Password is required."),
});

export const projectSchema = z.object({
  title: requiredString("Title"),
  slug: z.string().max(255).optional().nullable(),
  description: z.string().min(1, "Description is required."),
  category: z.enum(["Web", "App", "Mobile", "Script", "Other"]),
  tech_stack: z.string().optional().nullable(),
  images: z.any().optional(),
  deleted_images: z.string().optional().nullable(),
  live_url: optionalUrl,
  repo_url: optionalUrl,
  is_featured: z.coerce.boolean().optional(),
  sort_order: z.coerce.number().int().optional().nullable(),
});

export const serviceSchema = z.object({
  title: requiredString("Title"),
  description: z.string().min(1, "Description is required."),
  icon: z.url("Icon must be a valid URL.").max(255),
  sort_order: z.coerce.number().int("Sort order must be an integer."),
  tags: z.string().optional().nullable(),
});

export const skillSchema = z.object({
  name: requiredString("Name"),
  icon: optionalUrl,
  proficiency: z.coerce.number().int().min(0).max(100),
  skill_category_id: z.coerce.number().int().min(1, "Skill category is required."),
});

export const skillCategorySchema = z.object({
  name: requiredString("Name"),
  slug: z.string().max(255).optional().nullable(),
});

export const experienceSchema = z
  .object({
    job_title: requiredString("Job title"),
    company: requiredString("Company"),
    description: requiredString("Description"),
    start_date: z.coerce
      .number()
      .int()
      .min(1000)
      .max(new Date().getFullYear(), "Start date cannot be in the future."),
    end_date: z.coerce.number().int().optional().nullable(),
    is_current: z.coerce.boolean(),
  })
  .superRefine((value, context) => {
    if (!value.is_current && !value.end_date) {
      context.addIssue({
        code: "custom",
        path: ["end_date"],
        message: "End date is required if this is not current.",
      });
    }

    if (value.end_date && value.end_date < value.start_date) {
      context.addIssue({
        code: "custom",
        path: ["end_date"],
        message: "End date must be after or equal to start date.",
      });
    }

    if (value.is_current && value.end_date) {
      context.addIssue({
        code: "custom",
        path: ["end_date"],
        message: "End date must be empty when this is current.",
      });
    }
  });

export const educationSchema = z.object({
  degree: requiredString("Degree"),
  institution: requiredString("Institution"),
  field_of_study: requiredString("Field of study"),
  start_year: z.coerce
    .number()
    .int()
    .min(1950)
    .max(new Date().getFullYear() + 1),
  end_year: z.coerce.number().int().optional().nullable(),
  gpa: z.coerce.number().min(0).max(100).optional().nullable(),
  description: optionalString,
});

export const achievementSchema = z.object({
  title: requiredString("Title"),
  issuer: requiredString("Issuer"),
  date: z.string().min(1, "Date is required."),
  url: optionalUrl,
  description: z.string().max(255).optional().nullable(),
  file: z.any().optional(),
});

export const siteSettingsSchema = z.object({
  first_name: z.string().max(255).optional().nullable(),
  last_name: z.string().max(255).optional().nullable(),
  tagline: z.string().max(255).optional().nullable(),
  bio: z.string().max(255).optional().nullable(),
  avatar: z.any().optional(),
  cv_file: z.any().optional(),
  email: z.union([z.email(), z.literal("")]).optional().nullable(),
  phone: z.string().optional().nullable(),
  location: z.string().optional().nullable(),
  social_links: z.string().optional().nullable(),
  languages: z.string().optional().nullable(),
  years_experience: z.coerce.number().int().optional().nullable(),
  projects_count: z.coerce.number().int().optional().nullable(),
  clients_count: z.coerce.number().int().optional().nullable(),
  available_for_freelance: z.coerce.boolean().optional(),
  about_me: z.string().optional().nullable(),
  url_prefix: z.string().max(255).optional().nullable(),
  url_suffix: z.string().max(255).optional().nullable(),
});

export const contactMessageSchema = z.object({
  name: requiredString("Name"),
  email: z.email("Email must be a valid email address.").max(255),
  subject: requiredString("Subject"),
  body: z.string().min(1, "Body is required."),
});
