"use client";

import {
  Briefcase,
  FolderKanban,
  LayoutDashboard,
  LogOut,
  Menu,
  MessageSquare,
  Settings,
  Sparkles,
  Wrench,
} from "lucide-react";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { type ComponentType, useState } from "react";

import { AuthGuard } from "@/src/components/AuthGuard";
import { useAuth } from "@/src/context/AuthContext";

type NavigationItem = {
  href: string;
  label: string;
  icon: ComponentType<{ className?: string }>;
};

const navigationItems: NavigationItem[] = [
  { href: "/dashboard", label: "Dashboard", icon: LayoutDashboard },
  { href: "/dashboard/projects", label: "Projects", icon: FolderKanban },
  { href: "/dashboard/skills", label: "Skills", icon: Sparkles },
  { href: "/dashboard/services", label: "Services", icon: Wrench },
  { href: "/dashboard/messages", label: "Messages", icon: MessageSquare },
  { href: "/dashboard/settings", label: "Settings", icon: Settings },
];

export default function AdminLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  const pathname = usePathname();
  const { logout } = useAuth();

  const [isSidebarOpen, setIsSidebarOpen] = useState<boolean>(false);
  const [isLoggingOut, setIsLoggingOut] = useState<boolean>(false);

  const handleLogout = async (): Promise<void> => {
    setIsLoggingOut(true);

    try {
      await logout({ redirectTo: "/login" });
    } finally {
      setIsLoggingOut(false);
    }
  };

  return (
    <AuthGuard protectedPrefixes={["/dashboard"]}>
      <div className="min-h-screen bg-slate-950 text-slate-100">
        <div className="pointer-events-none fixed inset-0 bg-[radial-gradient(circle_at_15%_20%,rgba(99,102,241,0.18),transparent_40%),radial-gradient(circle_at_80%_0%,rgba(168,85,247,0.17),transparent_35%)]" />

        {isSidebarOpen ? (
          <button
            type="button"
            aria-label="Close sidebar"
            onClick={() => {
              setIsSidebarOpen(false);
            }}
            className="fixed inset-0 z-20 bg-black/50 lg:hidden"
          />
        ) : null}

        <aside
          className={`fixed inset-y-0 left-0 z-30 w-72 border-r border-violet-400/15 bg-black/30 p-5 backdrop-blur-md transition-transform duration-300 ${
            isSidebarOpen ? "translate-x-0" : "-translate-x-full lg:translate-x-0"
          }`}
        >
          <div className="mb-8 flex items-center gap-3">
            <div className="rounded-lg border border-violet-400/40 bg-violet-500/10 p-2 text-violet-300">
              <Briefcase className="size-5" />
            </div>
            <div>
              <p className="font-semibold text-white">Portfolio CMS</p>
              <p className="text-xs text-slate-400">Cosmic Dark Admin</p>
            </div>
          </div>

          <nav className="space-y-1">
            {navigationItems.map((item) => {
              const isActive = pathname === item.href;
              const Icon = item.icon;

              return (
                <Link
                  key={item.href}
                  href={item.href}
                  onClick={() => {
                    setIsSidebarOpen(false);
                  }}
                  className={`flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition ${
                    isActive
                      ? "border border-violet-400/35 bg-violet-500/15 text-white shadow-[0_0_20px_rgba(139,92,246,0.25)]"
                      : "text-slate-300 hover:bg-slate-800/70 hover:text-white"
                  }`}
                >
                  <Icon className="size-4" />
                  <span>{item.label}</span>
                </Link>
              );
            })}
          </nav>
        </aside>

        <div className="relative z-10 lg:pl-72">
          <header className="sticky top-0 z-10 border-b border-violet-400/15 bg-slate-950/80 px-4 py-3 backdrop-blur-md lg:px-8">
            <div className="flex items-center justify-between gap-3">
              <div className="flex items-center gap-3">
                <button
                  type="button"
                  onClick={() => {
                    setIsSidebarOpen(true);
                  }}
                  className="rounded-lg border border-slate-700 bg-slate-900/70 p-2 text-slate-200 lg:hidden"
                >
                  <Menu className="size-4" />
                </button>
                <div>
                  <p className="text-xs uppercase tracking-[0.14em] text-violet-300">Control Center</p>
                  <h1 className="text-lg font-semibold text-white">Admin</h1>
                </div>
              </div>

              <button
                type="button"
                onClick={handleLogout}
                disabled={isLoggingOut}
                className="inline-flex items-center gap-2 rounded-lg border border-slate-700 bg-slate-900/70 px-3 py-2 text-sm text-slate-200 transition hover:border-violet-400/40 hover:text-white disabled:cursor-not-allowed disabled:opacity-60"
              >
                <LogOut className="size-4" />
                {isLoggingOut ? "Logging out..." : "Logout"}
              </button>
            </div>
          </header>

          <main className="h-[calc(100vh-73px)] overflow-y-auto px-4 py-6 lg:px-8">{children}</main>
        </div>
      </div>
    </AuthGuard>
  );
}
