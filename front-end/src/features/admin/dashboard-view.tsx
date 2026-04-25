"use client";

import { Loader2, Mail, Sparkles, FolderKanban } from "lucide-react";
import { useEffect, useState } from "react";

import {
  messageService,
  projectService,
  skillService,
} from "@/services/admin-services";
import type { Message, Project, Skill } from "@/types/api";

type DashboardViewData = {
  projects: Project[];
  skills: Skill[];
  messages: Message[];
  projectsCount: number;
  skillsCount: number;
  unreadMessagesCount: number;
};

export function DashboardView() {
  const [data, setData] = useState<DashboardViewData | null>(null);
  const [error, setError] = useState("");

  useEffect(() => {
    Promise.all([
      projectService.list(),
      skillService.list(),
      messageService.list(),
    ])
      .then(([projects, skills, messagePage]) => {
        setData({
          projects: projects.slice(0, 5),
          skills: skills.slice(0, 6),
          messages: messagePage.messages.slice(0, 3),
          projectsCount: projects.length,
          skillsCount: skills.length,
          unreadMessagesCount: messagePage.messages.filter((message) => !message.is_read).length,
        });
      })
      .catch((caughtError: unknown) => {
        setError(
          caughtError instanceof Error
            ? caughtError.message
            : "Unable to load dashboard.",
        );
      });
  }, []);

  if (error) {
    return (
      <div className="rounded-lg border border-danger/30 bg-danger/10 p-4 text-sm text-danger">
        {error}
      </div>
    );
  }

  if (!data) {
    return (
      <div className="flex items-center gap-2 text-sm text-muted-foreground">
        <Loader2 className="animate-spin" size={16} />
        Loading dashboard
      </div>
    );
  }

  return (
    <section className="grid gap-6">
      <div>
        <p className="text-sm font-medium uppercase tracking-wide text-primary">
          Admin
        </p>
        <h1 className="mt-2 text-3xl font-semibold">Dashboard</h1>
      </div>

      <div className="grid gap-4 md:grid-cols-3">
        <StatCard icon={<FolderKanban size={20} />} label="Projects" value={data.projectsCount} />
        <StatCard icon={<Sparkles size={20} />} label="Skills" value={data.skillsCount} />
        <StatCard icon={<Mail size={20} />} label="Unread messages" value={data.unreadMessagesCount} />
      </div>

      <div className="grid gap-4 lg:grid-cols-2">
        <Panel title="Latest projects">
          {data.projects.map((project) => (
            <Row key={project.id} title={project.title} detail={project.category} />
          ))}
        </Panel>
        <Panel title="Recent messages">
          {data.messages.map((message) => (
            <Row key={message.id} title={message.subject ?? "No subject"} detail={message.email} />
          ))}
        </Panel>
        <Panel title="Top skills">
          {data.skills.map((skill) => (
            <Row key={skill.id} title={skill.name} detail={`${skill.category} - ${skill.proficiency}%`} />
          ))}
        </Panel>
      </div>
    </section>
  );
}

function StatCard({
  icon,
  label,
  value,
}: {
  icon: React.ReactNode;
  label: string;
  value: number;
}) {
  return (
    <div className="rounded-lg border border-border bg-surface p-5">
      <div className="flex items-center justify-between gap-4">
        <div className="text-sm text-muted-foreground">{label}</div>
        <div className="text-primary">{icon}</div>
      </div>
      <div className="mt-3 text-3xl font-semibold">{value}</div>
    </div>
  );
}

function Panel({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <div className="rounded-lg border border-border bg-surface p-5">
      <h2 className="text-lg font-semibold">{title}</h2>
      <div className="mt-4 grid gap-3">{children}</div>
    </div>
  );
}

function Row({ title, detail }: { title: string; detail: string }) {
  return (
    <div className="rounded-md border border-border bg-background px-3 py-2">
      <div className="text-sm font-medium">{title}</div>
      <div className="mt-1 text-xs text-muted-foreground">{detail}</div>
    </div>
  );
}
