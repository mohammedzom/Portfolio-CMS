import { api } from "@/lib/api/client";
import { apiEndpoints } from "@/lib/api/endpoints";
import type { PortfolioData } from "@/types/api";

export function getPortfolio(): Promise<PortfolioData> {
  return api.get<PortfolioData>(apiEndpoints.portfolio, { auth: false });
}

export function sendMessage(payload: {
  name: string;
  email: string;
  subject: string;
  body: string;
}): Promise<[]> {
  return api.post<[]>(apiEndpoints.message, payload, { auth: false });
}
