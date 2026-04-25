"use client";

import { ResourceManager } from "@/features/admin/resource-manager";
import { achievementConfig } from "@/features/admin/resource-configs";

export default function AchievementsPage() {
  return <ResourceManager config={achievementConfig} />;
}
