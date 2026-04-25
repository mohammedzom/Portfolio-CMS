import { api } from "@/lib/api/client";
import { apiEndpoints } from "@/lib/api/endpoints";
import type {
  Achievement,
  DashboardData,
  Education,
  Experience,
  Message,
  PaginatedMessages,
  Project,
  Service,
  SiteSettings,
  Skill,
  SkillCategory,
} from "@/types/api";
import { createResourceService, type ListQuery } from "@/services/resource-service";

export const projectService = createResourceService<Project>(apiEndpoints.admin.projects);
export const serviceService = createResourceService<Service>(apiEndpoints.admin.services);
export const skillService = createResourceService<Skill>(apiEndpoints.admin.skills);
export const experienceService = createResourceService<Experience>(apiEndpoints.admin.experiences);
export const educationService = createResourceService<Education>(apiEndpoints.admin.education);
export const achievementService = createResourceService<Achievement>(apiEndpoints.admin.achievements);

export const siteSettingsService = {
  get: () => api.get<SiteSettings>(apiEndpoints.admin.siteInfo),
  update: (payload: FormData | Record<string, unknown>) =>
    api.patch<SiteSettings>(apiEndpoints.admin.siteInfo, payload),
};

export const dashboardService = {
  get: () => api.get<DashboardData>(apiEndpoints.admin.dashboard),
};

export const messageService = {
  list: (query?: ListQuery) =>
    api.get<PaginatedMessages>(apiEndpoints.admin.messages, { query }),
  get: (id: number | string) => api.get<Message>(`${apiEndpoints.admin.messages}/${id}`),
  markRead: (id: number | string) =>
    api.patch<{ read_at: string }>(`${apiEndpoints.admin.messages}/${id}/read`),
  markUnread: (id: number | string) =>
    api.patch<[]>(`${apiEndpoints.admin.messages}/${id}/unread`),
  archive: (id: number | string) => api.delete<unknown>(`${apiEndpoints.admin.messages}/${id}`),
  restore: (id: number | string) =>
    api.patch<Message>(`${apiEndpoints.admin.messages}/${id}/restore`),
  forceDelete: (id: number | string) =>
    api.delete<unknown>(`${apiEndpoints.admin.messages}/${id}/force-delete`),
};

const fallbackSkillCategories: SkillCategory[] = [
  {
    id: 1,
    name: "Frontend",
    slug: "frontend",
    created_at: "",
    updated_at: "",
    deleted_at: null,
    deleted_at_human: null,
  },
  {
    id: 2,
    name: "Backend",
    slug: "backend",
    created_at: "",
    updated_at: "",
    deleted_at: null,
    deleted_at_human: null,
  },
  {
    id: 3,
    name: "Tools",
    slug: "tools",
    created_at: "",
    updated_at: "",
    deleted_at: null,
    deleted_at_human: null,
  },
];

export const skillCategoryService = {
  list: async (): Promise<SkillCategory[]> => {
    try {
      return await api.get<SkillCategory[]>(apiEndpoints.admin.skillCategories);
    } catch {
      return fallbackSkillCategories;
    }
  },
  get: (id: number | string) =>
    api.get<SkillCategory>(`${apiEndpoints.admin.skillCategories}/${id}`),
  create: (payload: Record<string, unknown>) =>
    api.post<SkillCategory>(apiEndpoints.admin.skillCategories, payload),
  update: (id: number | string, payload: Record<string, unknown>) =>
    api.patch<SkillCategory>(`${apiEndpoints.admin.skillCategories}/${id}`, payload),
  archive: (id: number | string) =>
    api.delete<unknown>(`${apiEndpoints.admin.skillCategories}/${id}`),
  restore: (id: number | string) =>
    api.patch<SkillCategory>(`${apiEndpoints.admin.skillCategories}/${id}/restore`),
  forceDelete: (id: number | string) =>
    api.delete<unknown>(`${apiEndpoints.admin.skillCategories}/${id}/force-delete`),
};
