"use client";

import { zodResolver } from "@hookform/resolvers/zod";
import { Loader2, LockKeyhole } from "lucide-react";
import { useRouter } from "next/navigation";
import { useState } from "react";
import { type Resolver, useForm } from "react-hook-form";
import type { z } from "zod";

import { Button } from "@/components/ui/button";
import { TextField } from "@/components/ui/field";
import { useAuth } from "@/context/auth-context";
import { applyBackendValidationErrors } from "@/lib/forms/backend-errors";
import { login } from "@/services/auth-service";
import { loginSchema } from "./schemas";

type LoginValues = z.infer<typeof loginSchema>;

export function LoginForm() {
  const router = useRouter();
  const { setToken } = useAuth();
  const [message, setMessage] = useState("");
  const {
    register,
    handleSubmit,
    setError,
    formState: { errors, isSubmitting },
  } = useForm<LoginValues>({
    resolver: zodResolver(loginSchema) as Resolver<LoginValues>,
    defaultValues: {
      email: "",
      password: "",
    },
  });

  const onSubmit = async (values: LoginValues) => {
    console.info("Submitting admin login request for:", values.email);
    setMessage("Signing in...");
    try {
      const response = await login(values);
      console.info("Admin login succeeded.");
      setToken(response.token);
      router.push("/admin");
      router.refresh();
    } catch (error) {
      console.error("Admin login failed:", error);
      setMessage(applyBackendValidationErrors(error, setError));
    }
  };

  return (
    <form
      method="post"
      noValidate
      onSubmit={handleSubmit(onSubmit)}
      className="w-full max-w-md rounded-lg border border-cyan-400/30 bg-zinc-950/90 p-6 shadow-[0_0_40px_rgba(34,211,238,0.16)]"
    >
      <div className="flex size-12 items-center justify-center rounded-md border border-cyan-400/40 bg-cyan-400/10 text-cyan-200">
        <LockKeyhole size={22} />
      </div>
      <p className="mt-6 text-sm font-medium uppercase tracking-[0.24em] text-cyan-300">
        Admin Access
      </p>
      <h1 className="mt-3 text-3xl font-semibold text-white">Sign in</h1>
      <p className="mt-3 text-sm leading-6 text-zinc-400">
        Use your Portfolio CMS account to manage content.
      </p>

      {message ? (
        <div className="mt-5 rounded-md border border-red-400/40 bg-red-500/10 px-3 py-2 text-sm text-red-200">
          {message}
        </div>
      ) : null}

      <div className="mt-6 grid gap-4">
        <TextField
          label="Email"
          type="email"
          autoComplete="email"
          error={errors.email?.message}
          {...register("email")}
        />
        <TextField
          label="Password"
          type="password"
          autoComplete="current-password"
          error={errors.password?.message}
          {...register("password")}
        />
      </div>

      <Button type="submit" className="mt-6 w-full" disabled={isSubmitting}>
        {isSubmitting ? <Loader2 className="animate-spin" size={16} /> : null}
        {isSubmitting ? "Signing in" : "Login"}
      </Button>
    </form>
  );
}
