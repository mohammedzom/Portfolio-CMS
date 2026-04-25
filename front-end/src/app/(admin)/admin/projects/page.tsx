"use client";

import { ResourceManager } from "@/features/admin/resource-manager";
import { projectConfig } from "@/features/admin/resource-configs";

export default function ProjectsPage() {
  return <ResourceManager config={projectConfig} />;
}
