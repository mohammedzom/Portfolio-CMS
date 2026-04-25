"use client";

import { ResourceManager } from "@/features/admin/resource-manager";
import { educationConfig } from "@/features/admin/resource-configs";

export default function EducationPage() {
  return <ResourceManager config={educationConfig} />;
}
