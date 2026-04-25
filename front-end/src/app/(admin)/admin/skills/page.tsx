"use client";

import { ResourceManager } from "@/features/admin/resource-manager";
import { skillCategoryConfig, skillConfig } from "@/features/admin/resource-configs";

export default function SkillsPage() {
  return (
    <div className="grid gap-10">
      <ResourceManager config={skillConfig} />
      <ResourceManager config={skillCategoryConfig} />
    </div>
  );
}
