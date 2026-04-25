import { PortfolioPage } from "@/features/portfolio/portfolio-page";
import type { ApiEnvelope, PortfolioData } from "@/types/api";

export const dynamic = "force-dynamic";

export default async function PortfolioHomePage() {
  const { data, error } = await fetchPortfolio();

  return <PortfolioPage initialData={data} initialError={error} />;
}

async function fetchPortfolio(): Promise<{
  data: PortfolioData | null;
  error: string;
}> {
  const baseUrl =
    process.env.NEXT_PUBLIC_API_BASE_URL?.replace(/\/$/, "") ??
    "https://api.mohammedzomlot.online/api/v1";
  const apiKey = process.env.NEXT_PUBLIC_API_KEY;

  if (!apiKey) {
    return {
      data: null,
      error: "NEXT_PUBLIC_API_KEY is missing from the frontend environment.",
    };
  }

  try {
    const response = await fetch(`${baseUrl}/portfolio`, {
      headers: {
        Accept: "application/json",
        "x-api-key": apiKey,
      },
      cache: "no-store",
    });

    const payload = (await response.json()) as ApiEnvelope<PortfolioData>;

    if (!response.ok || !payload.success) {
      return {
        data: null,
        error: payload.message || `Portfolio request failed with ${response.status}.`,
      };
    }

    return {
      data: payload.data,
      error: "",
    };
  } catch (error) {
    console.error("Failed to server-fetch portfolio data:", error);

    return {
      data: null,
      error:
        error instanceof Error
          ? error.message
          : "Unable to load portfolio from the API.",
    };
  }
}
