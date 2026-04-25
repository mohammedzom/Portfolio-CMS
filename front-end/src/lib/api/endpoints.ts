export const apiEndpoints = {
  portfolio: "/portfolio",
  message: "/message",
  admin: {
    login: "/admin/login",
    logout: "/admin/logout",
    dashboard: "/admin/dashboard",
    projects: "/admin/projects",
    services: "/admin/services",
    skills: "/admin/skills",
    skillCategories: "/admin/skill-categories",
    experiences: "/admin/experiences",
    education: "/admin/education",
    achievements: "/admin/achievements",
    messages: "/admin/messages",
    siteInfo: "/admin/site-info",
  },
} as const;
