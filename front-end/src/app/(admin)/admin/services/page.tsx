"use client";

import { ResourceManager } from "@/features/admin/resource-manager";
import { serviceConfig } from "@/features/admin/resource-configs";

export default function ServicesPage() {
  return <ResourceManager config={serviceConfig} />;
}
