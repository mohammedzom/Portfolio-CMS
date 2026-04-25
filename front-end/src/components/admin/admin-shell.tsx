"use client";

import {
  Award,
  BriefcaseBusiness,
  FolderKanban,
  GraduationCap,
  LayoutDashboard,
  LogOut,
  Mail,
  Menu,
  Settings,
  Sparkles,
  Wrench,
  X,
} from "lucide-react";
import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { useState } from "react";

import { useAuth } from "@/context/auth-context";
import { logout } from "@/services/auth-service";

const navItems = [
  { href: "/admin", label: "Dashboard", icon: LayoutDashboard },
  { href: "/admin/projects", label: "Projects", icon: FolderKanban },
  { href: "/admin/services", label: "Services", icon: Wrench },
  { href: "/admin/skills", label: "Skills", icon: Sparkles },
  { href: "/admin/experiences", label: "Experiences", icon: BriefcaseBusiness },
  { href: "/admin/education", label: "Education", icon: GraduationCap },
  { href: "/admin/achievements", label: "Achievements", icon: Award },
  { href: "/admin/messages", label: "Messages", icon: Mail },
  { href: "/admin/settings", label: "Settings", icon: Settings },
];

export function AdminShell({ children }: { children: React.ReactNode }) {
  const pathname = usePathname();
  const router = useRouter();
  const { clearToken } = useAuth();
  const [isOpen, setIsOpen] = useState(false);

  if (pathname === "/admin/login") {
    return <>{children}</>;
  }

  const handleLogout = async () => {
    await logout();
    clearToken();
    router.replace("/admin/login");
  };

  return (
    <div className="min-h-dvh bg-muted text-foreground">
      <div className="border-b border-border bg-surface px-4 py-3 lg:hidden">
        <div className="flex items-center justify-between gap-4">
          <Link href="/admin" className="font-semibold text-primary">
            Portfolio CMS
          </Link>
          <button
            type="button"
            onClick={() => setIsOpen((value) => !value)}
            className="inline-flex size-10 items-center justify-center rounded-md border border-border text-foreground"
            aria-label="Toggle navigation"
          >
            {isOpen ? <X size={18} /> : <Menu size={18} />}
          </button>
        </div>
      </div>

      <div className="mx-auto grid min-h-dvh w-full max-w-[1600px] lg:grid-cols-[280px_1fr]">
        <aside
          className={`border-r border-border bg-surface px-4 py-5 lg:block ${
            isOpen ? "block" : "hidden"
          }`}
        >
          <Link href="/admin" className="block px-3">
            <span className="text-xs font-semibold uppercase tracking-[0.2em] text-primary">
              Portfolio CMS
            </span>
            <span className="mt-2 block text-xl font-semibold">Admin Console</span>
          </Link>
          <nav className="mt-8 grid gap-1">
            {navItems.map((item) => {
              const Icon = item.icon;
              const isActive =
                pathname === item.href ||
                (item.href !== "/admin" && pathname.startsWith(item.href));

              return (
                <Link
                  key={item.href}
                  href={item.href}
                  onClick={() => setIsOpen(false)}
                  className={`flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium transition ${
                    isActive
                      ? "bg-primary text-primary-foreground"
                      : "text-muted-foreground hover:bg-muted hover:text-foreground"
                  }`}
                >
                  <Icon size={18} />
                  {item.label}
                </Link>
              );
            })}
          </nav>
          <button
            type="button"
            onClick={handleLogout}
            className="mt-8 flex w-full items-center gap-3 rounded-md border border-border px-3 py-2.5 text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground"
          >
            <LogOut size={18} />
            Logout
          </button>
        </aside>

        <main className="min-w-0 px-4 py-6 sm:px-6 lg:px-8">{children}</main>
      </div>
    </div>
  );
}
