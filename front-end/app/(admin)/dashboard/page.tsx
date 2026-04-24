"use client";

import { AxiosError } from "axios";
import { Bell, FolderKanban, Loader2, Sparkles, UsersRound } from "lucide-react";
import { type ComponentType, useEffect, useMemo, useState } from "react";

import { apiClient, type ApiResponse } from "@/lib/axios";

type DashboardMessagesCount = {
  total?: number;
  unread?: number;
};

type DashboardResponseData = {
  projects_count?: number;
  skills_count?: number;
  services_count?: number;
  messages_count?: number | DashboardMessagesCount;
  projects?: unknown[];
  skills?: {
    technical?: unknown[];
    tool?: unknown[];
  };
};

type StatCard = {
  title: string;
  value: number;
  icon: ComponentType<{ className?: string }>;
  accentClassName: string;
};

export default function DashboardOverviewPage() {
  const [dashboardData, setDashboardData] = useState<DashboardResponseData | null>(null);
  const [isLoading, setIsLoading] = useState<boolean>(true);
  const [errorMessage, setErrorMessage] = useState<string>("");

  useEffect(() => {
    const fetchDashboard = async (): Promise<void> => {
      setIsLoading(true);
      setErrorMessage("");

      try {
        const response = await apiClient.get<ApiResponse<DashboardResponseData>>("/admin/dashboard");
        setDashboardData(response.data.data);
      } catch (error) {
        const axiosError = error as AxiosError<ApiResponse<null>>;
        setErrorMessage(
          axiosError.response?.data?.message ?? "Failed to load dashboard overview data.",
        );
      } finally {
        setIsLoading(false);
      }
    };

    void fetchDashboard();
  }, []);

  const stats = useMemo<StatCard[]>(() => {
    const projectCount = dashboardData?.projects_count ?? dashboardData?.projects?.length ?? 0;
    const skillsCount =
      dashboardData?.skills_count ??
      (dashboardData?.skills?.technical?.length ?? 0) + (dashboardData?.skills?.tool?.length ?? 0);

    const messageStats =
      typeof dashboardData?.messages_count === "number"
        ? { total: dashboardData.messages_count, unread: 0 }
        : dashboardData?.messages_count;

    return [
      {
        title: "Total Projects",
        value: projectCount,
        icon: FolderKanban,
        accentClassName: "from-violet-500/25 to-violet-500/5 border-violet-400/35",
      },
      {
        title: "Total Skills",
        value: skillsCount,
        icon: Sparkles,
        accentClassName: "from-indigo-500/25 to-indigo-500/5 border-indigo-400/35",
      },
      {
        title: "Total Messages",
        value: messageStats?.total ?? 0,
        icon: UsersRound,
        accentClassName: "from-cyan-500/25 to-cyan-500/5 border-cyan-400/35",
      },
      {
        title: "New Messages",
        value: messageStats?.unread ?? 0,
        icon: Bell,
        accentClassName: "from-emerald-500/25 to-emerald-500/5 border-emerald-400/35",
      },
    ];
  }, [dashboardData]);

  if (isLoading) {
    return (
      <section className="flex min-h-[50vh] items-center justify-center">
        <div className="inline-flex items-center gap-2 rounded-xl border border-violet-400/30 bg-slate-900/60 px-4 py-3 text-slate-200">
          <Loader2 className="size-4 animate-spin" />
          <span>Loading dashboard...</span>
        </div>
      </section>
    );
  }

  if (errorMessage) {
    return (
      <section className="rounded-2xl border border-rose-500/35 bg-rose-500/10 p-4 text-rose-200">
        {errorMessage}
      </section>
    );
  }

  return (
    <section className="space-y-6">
      <div>
        <h2 className="text-xl font-semibold text-white">Dashboard Overview</h2>
        <p className="mt-1 text-sm text-slate-300">Live metrics from your Laravel admin API.</p>
      </div>

      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        {stats.map((stat) => {
          const Icon = stat.icon;

          return (
            <article
              key={stat.title}
              className={`rounded-2xl border bg-gradient-to-br p-5 backdrop-blur-md ${stat.accentClassName} shadow-[0_0_24px_rgba(99,102,241,0.15)]`}
            >
              <div className="mb-4 inline-flex rounded-lg bg-black/30 p-2 text-slate-100">
                <Icon className="size-4" />
              </div>

              <p className="text-sm text-slate-300">{stat.title}</p>
              <p className="mt-1 text-3xl font-semibold text-white">{stat.value}</p>
            </article>
          );
        })}
      </div>
    </section>
  );
}
