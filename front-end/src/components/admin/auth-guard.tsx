"use client";

import { usePathname, useRouter } from "next/navigation";
import { useEffect } from "react";

import { useAuth } from "@/context/auth-context";

export function AuthGuard({ children }: { children: React.ReactNode }) {
  const pathname = usePathname();
  const router = useRouter();
  const { isAuthenticated, isReady } = useAuth();
  const isLoginRoute = pathname === "/admin/login";

  useEffect(() => {
    if (!isReady) {
      return;
    }

    if (!isAuthenticated && !isLoginRoute) {
      router.replace("/admin/login");
    }

    if (isAuthenticated && isLoginRoute) {
      router.replace("/admin");
    }
  }, [isAuthenticated, isLoginRoute, isReady, router]);

  if (isLoginRoute) {
    return children;
  }

  if (!isReady) {
    return (
      <div className="flex min-h-dvh items-center justify-center bg-muted text-sm text-muted-foreground">
        Loading session...
      </div>
    );
  }

  if (!isAuthenticated) {
    return null;
  }

  return children;
}
