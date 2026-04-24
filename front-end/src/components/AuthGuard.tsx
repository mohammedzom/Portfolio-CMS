"use client";

import { motion } from "framer-motion";
import { Loader2, Sparkles } from "lucide-react";
import { usePathname, useRouter } from "next/navigation";
import { type ReactNode, useEffect } from "react";

import { useAuth } from "@/src/context/AuthContext";

interface AuthGuardProps {
  children: ReactNode;
  protectedPrefixes: string[];
}

export function AuthGuard({ children, protectedPrefixes }: AuthGuardProps) {
  const pathname = usePathname();
  const router = useRouter();
  const { isAuthenticated, isLoading } = useAuth();

  const isProtectedRoute = protectedPrefixes.some((prefix) => pathname.startsWith(prefix));

  useEffect(() => {
    if (isLoading || !isProtectedRoute) {
      return;
    }

    if (!isAuthenticated) {
      router.replace("/login");
    }
  }, [isAuthenticated, isLoading, isProtectedRoute, router]);

  if (isLoading && isProtectedRoute) {
    return (
      <div className="relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-950 text-slate-100">
        <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(99,102,241,0.2),transparent_45%),radial-gradient(circle_at_80%_10%,rgba(168,85,247,0.16),transparent_40%)]" />

        <motion.div
          initial={{ opacity: 0, y: 8 }}
          animate={{ opacity: 1, y: 0 }}
          className="relative z-10 flex items-center gap-3 rounded-2xl border border-violet-400/25 bg-black/30 px-5 py-4 backdrop-blur-md"
        >
          <Sparkles className="size-4 text-violet-300" />
          <Loader2 className="size-4 animate-spin text-violet-200" />
          <span className="text-sm text-slate-200">Authenticating session...</span>
        </motion.div>
      </div>
    );
  }

  if (!isAuthenticated && isProtectedRoute) {
    return null;
  }

  return <>{children}</>;
}
