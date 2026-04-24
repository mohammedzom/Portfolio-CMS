"use client";

import { motion } from "framer-motion";
import { AxiosError } from "axios";
import { Eye, EyeOff, Loader2, Lock, Mail, Sparkles } from "lucide-react";
import { useRouter } from "next/navigation";
import { FormEvent, useState } from "react";

import { apiClient, type ApiResponse } from "@/lib/axios";

type LoginPayload = {
  token?: string;
  access_token?: string;
};

type LoginFormData = {
  email: string;
  password: string;
};

const initialFormState: LoginFormData = {
  email: "",
  password: "",
};

export default function LoginPage() {
  const router = useRouter();

  const [formData, setFormData] = useState<LoginFormData>(initialFormState);
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const [showPassword, setShowPassword] = useState<boolean>(false);
  const [errorMessage, setErrorMessage] = useState<string>("");

  const handleSubmit = async (event: FormEvent<HTMLFormElement>): Promise<void> => {
    event.preventDefault();

    setIsLoading(true);
    setErrorMessage("");

    try {
      const response = await apiClient.post<ApiResponse<LoginPayload>>("/login", formData);

      const token =
        response.data.data?.token ??
        response.data.data?.access_token ??
        (response.data as unknown as LoginPayload)?.token ??
        (response.data as unknown as LoginPayload)?.access_token;

      if (!token) {
        throw new Error("No token was returned by the authentication endpoint.");
      }

      window.localStorage.setItem("auth_token", token);
      router.replace("/dashboard");
    } catch (error) {
      const axiosError = error as AxiosError<ApiResponse<null>>;

      setErrorMessage(
        axiosError.response?.data?.message ??
          (error instanceof Error ? error.message : "Unable to login. Please try again."),
      );
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <main className="relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-950 px-4 py-12 text-slate-100">
      <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(99,102,241,0.25),transparent_45%),radial-gradient(circle_at_80%_10%,rgba(168,85,247,0.2),transparent_40%),radial-gradient(circle_at_50%_90%,rgba(16,185,129,0.18),transparent_35%)]" />

      <motion.section
        initial={{ opacity: 0, y: 20, scale: 0.97 }}
        animate={{ opacity: 1, y: 0, scale: 1 }}
        transition={{ duration: 0.45, ease: "easeOut" }}
        className="relative z-10 w-full max-w-md rounded-2xl border border-violet-400/20 bg-black/30 p-7 shadow-[0_0_40px_rgba(99,102,241,0.25)] backdrop-blur-md"
      >
        <div className="mb-6 flex items-center gap-2 text-violet-300">
          <Sparkles className="size-5" />
          <span className="text-sm font-medium tracking-wide">Cosmic Dark Admin</span>
        </div>

        <h1 className="text-2xl font-semibold text-white">Welcome back</h1>
        <p className="mt-1 text-sm text-slate-300">Sign in to manage your portfolio content.</p>

        <form className="mt-6 space-y-4" onSubmit={handleSubmit}>
          <label className="block">
            <span className="mb-2 block text-sm text-slate-300">Email</span>
            <div className="flex items-center gap-2 rounded-xl border border-slate-700/80 bg-slate-900/60 px-3 focus-within:border-violet-400">
              <Mail className="size-4 text-slate-400" />
              <input
                required
                type="email"
                autoComplete="email"
                value={formData.email}
                onChange={(event) => {
                  setFormData((previous) => ({ ...previous, email: event.target.value }));
                }}
                className="h-11 w-full bg-transparent text-sm text-white outline-none placeholder:text-slate-500"
                placeholder="you@example.com"
              />
            </div>
          </label>

          <label className="block">
            <span className="mb-2 block text-sm text-slate-300">Password</span>
            <div className="flex items-center gap-2 rounded-xl border border-slate-700/80 bg-slate-900/60 px-3 focus-within:border-violet-400">
              <Lock className="size-4 text-slate-400" />
              <input
                required
                minLength={6}
                type={showPassword ? "text" : "password"}
                autoComplete="current-password"
                value={formData.password}
                onChange={(event) => {
                  setFormData((previous) => ({ ...previous, password: event.target.value }));
                }}
                className="h-11 w-full bg-transparent text-sm text-white outline-none placeholder:text-slate-500"
                placeholder="••••••••"
              />
              <button
                type="button"
                onClick={() => {
                  setShowPassword((previous) => !previous);
                }}
                className="text-slate-400 transition hover:text-violet-300"
              >
                {showPassword ? <EyeOff className="size-4" /> : <Eye className="size-4" />}
              </button>
            </div>
          </label>

          {errorMessage ? (
            <div className="rounded-xl border border-rose-500/35 bg-rose-500/10 px-3 py-2 text-sm text-rose-200">
              {errorMessage}
            </div>
          ) : null}

          <button
            type="submit"
            disabled={isLoading}
            className="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 font-medium text-white transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-75"
          >
            {isLoading ? <Loader2 className="size-4 animate-spin" /> : null}
            {isLoading ? "Signing in..." : "Sign in"}
          </button>
        </form>
      </motion.section>
    </main>
  );
}
