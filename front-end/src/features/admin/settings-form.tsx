"use client";

import { zodResolver } from "@hookform/resolvers/zod";
import { Loader2 } from "lucide-react";
import { useEffect, useState } from "react";
import { type Resolver, useForm } from "react-hook-form";
import type { z } from "zod";

import { Button } from "@/components/ui/button";
import { CheckboxField, TextAreaField, TextField } from "@/components/ui/field";
import { applyBackendValidationErrors } from "@/lib/forms/backend-errors";
import { siteSettingsService } from "@/services/admin-services";
import { toFormData } from "@/services/resource-service";
import type { SiteSettings } from "@/types/api";
import { siteSettingsSchema } from "./schemas";

type SettingsValues = z.infer<typeof siteSettingsSchema>;

export function SettingsForm() {
  const [settings, setSettings] = useState<SiteSettings | null>(null);
  const [message, setMessage] = useState("");
  const {
    register,
    handleSubmit,
    reset,
    setError,
    formState: { errors, isSubmitting },
  } = useForm<SettingsValues>({
    resolver: zodResolver(siteSettingsSchema) as Resolver<SettingsValues>,
  });

  useEffect(() => {
    siteSettingsService
      .get()
      .then((data) => {
        setSettings(data);
        reset({
          ...data,
          social_links: JSON.stringify(data.social_links ?? [], null, 2),
          languages: JSON.stringify(data.languages ?? [], null, 2),
        });
      })
      .catch((error: unknown) => {
        setMessage(error instanceof Error ? error.message : "Unable to load site settings.");
      });
  }, [reset]);

  const onSubmit = async (values: SettingsValues) => {
    setMessage("");
    try {
      const payload = toFormData(
        {
          ...values,
          social_links: parseJsonArray(values.social_links),
          languages: parseJsonArray(values.languages),
        },
        ["avatar", "cv_file"],
      );
      const updated = await siteSettingsService.update(payload);
      setSettings(updated);
      setMessage("Settings saved.");
    } catch (error) {
      setMessage(applyBackendValidationErrors(error, setError));
    }
  };

  if (!settings && !message) {
    return (
      <div className="flex items-center gap-2 text-sm text-muted-foreground">
        <Loader2 className="animate-spin" size={16} />
        Loading settings
      </div>
    );
  }

  return (
    <section className="grid gap-6">
      <div>
        <p className="text-sm font-medium uppercase tracking-wide text-primary">Admin</p>
        <h1 className="mt-2 text-3xl font-semibold">Site Settings</h1>
        <p className="mt-2 max-w-2xl text-sm leading-6 text-muted-foreground">
          Manage personal information, contact details, social links, stats, files,
          and public profile metadata.
        </p>
      </div>

      {message ? (
        <div className="rounded-md border border-border bg-surface px-4 py-3 text-sm">
          {message}
        </div>
      ) : null}

      <form onSubmit={handleSubmit(onSubmit)} className="rounded-lg border border-border bg-surface p-5">
        <div className="grid gap-4 md:grid-cols-2">
          <TextField label="First name" error={errors.first_name?.message} {...register("first_name")} />
          <TextField label="Last name" error={errors.last_name?.message} {...register("last_name")} />
          <TextField label="Tagline" error={errors.tagline?.message} {...register("tagline")} />
          <TextField label="Email" type="email" error={errors.email?.message} {...register("email")} />
          <TextField label="Phone" error={errors.phone?.message} {...register("phone")} />
          <TextField label="Location" error={errors.location?.message} {...register("location")} />
          <TextField label="URL prefix" error={errors.url_prefix?.message} {...register("url_prefix")} />
          <TextField label="URL suffix" error={errors.url_suffix?.message} {...register("url_suffix")} />
          <TextField label="Years experience" type="number" error={errors.years_experience?.message} {...register("years_experience")} />
          <TextField label="Projects count" type="number" error={errors.projects_count?.message} {...register("projects_count")} />
          <TextField label="Clients count" type="number" error={errors.clients_count?.message} {...register("clients_count")} />
          <TextField label="Avatar" type="file" error={errors.avatar?.message as string} {...register("avatar")} />
          <TextField label="CV file" type="file" error={errors.cv_file?.message as string} {...register("cv_file")} />
          <CheckboxField label="Available for freelance" error={errors.available_for_freelance?.message} {...register("available_for_freelance")} />
          <TextAreaField label="Bio" error={errors.bio?.message} {...register("bio")} />
          <TextAreaField label="About me" error={errors.about_me?.message} {...register("about_me")} />
          <TextAreaField label="Social links JSON" error={errors.social_links?.message} {...register("social_links")} />
          <TextAreaField label="Languages JSON" error={errors.languages?.message} {...register("languages")} />
        </div>

        <div className="mt-6 flex justify-end">
          <Button type="submit" disabled={isSubmitting}>
            {isSubmitting ? <Loader2 className="animate-spin" size={16} /> : null}
            Save settings
          </Button>
        </div>
      </form>
    </section>
  );
}

function parseJsonArray(value: unknown): unknown[] {
  if (!value) {
    return [];
  }

  try {
    const parsed = JSON.parse(String(value));
    return Array.isArray(parsed) ? parsed : [];
  } catch {
    return [];
  }
}
