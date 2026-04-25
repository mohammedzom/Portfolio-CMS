"use client";

import { ResourceManager } from "@/features/admin/resource-manager";
import { experienceConfig } from "@/features/admin/resource-configs";

export default function ExperiencesPage() {
  return <ResourceManager config={experienceConfig} />;
}
