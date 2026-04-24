"use client";

import { AxiosError } from "axios";
import { AnimatePresence, motion } from "framer-motion";
import { CheckCircle2, Loader2, Save } from "lucide-react";
import { ChangeEvent, FormEvent, useEffect, useMemo, useState } from "react";

import { axiosClient, type ApiResponse } from "@/lib/axios";

interface SocialLink {
  name: string;
  url: string;
  icon?: string;
}

interface SiteSettings {
  site_title?: string;
  hero_description?: string;
  contact_email?: string;
  social_links?: SocialLink[];
  title?: string;
  description?: string;
  email?: string;
}

interface SettingsFormValues {
  siteTitle: string;
  heroDescription: string;
  contactEmail: string;
  socialLinksText: string;
}

const initialFormValues: SettingsFormValues = {
  siteTitle: "",
  heroDescription: "",
  contactEmail: "",
  socialLinksText: "[]",
};

function normalizeSettings(payload: unknown): SiteSettings {
  if (payload && typeof payload === "object") {
    return payload as SiteSettings;
  }

  return {};
}

export default function SettingsPage() {
  const [formValues, setFormValues] = useState<SettingsFormValues>(initialFormValues);
  const [isLoading, setIsLoading] = useState<boolean>(true);
  const [isSubmitting, setIsSubmitting] = useState<boolean>(false);
  const [errorMessage, setErrorMessage] = useState<string>("");
  const [successMessage, setSuccessMessage] = useState<string>("");

  const parsedSocialLinksPreview = useMemo(() => {
    try {
      const parsed = JSON.parse(formValues.socialLinksText) as SocialLink[];
      return Array.isArray(parsed) ? parsed.length : 0;
    } catch {
      return 0;
    }
  }, [formValues.socialLinksText]);

  useEffect(() => {
    const fetchSettings = async (): Promise<void> => {
      setIsLoading(true);
      setErrorMessage("");

      try {
        const response = await axiosClient.get<ApiResponse<unknown>>("/admin/settings");
        const settings = normalizeSettings(response.data.data);

        const socialLinks = Array.isArray(settings.social_links) ? settings.social_links : [];

        setFormValues({
          siteTitle: settings.site_title ?? settings.title ?? "",
          heroDescription: settings.hero_description ?? settings.description ?? "",
          contactEmail: settings.contact_email ?? settings.email ?? "",
          socialLinksText: JSON.stringify(socialLinks, null, 2),
        });
      } catch (error) {
        const axiosError = error as AxiosError<ApiResponse<null>>;
        setErrorMessage(axiosError.response?.data?.message ?? "Failed to load site settings.");
      } finally {
        setIsLoading(false);
      }
    };

    void fetchSettings();
  }, []);

  useEffect(() => {
    if (!successMessage) {
      return;
    }

    const timeout = window.setTimeout(() => {
      setSuccessMessage("");
    }, 2800);

    return () => {
      window.clearTimeout(timeout);
    };
  }, [successMessage]);

  const handleInputChange = (
    event: ChangeEvent<HTMLInputElement | HTMLTextAreaElement>,
  ): void => {
    const { name, value } = event.target;
    setFormValues((previous) => ({ ...previous, [name]: value }));
  };

  const handleSubmit = async (event: FormEvent<HTMLFormElement>): Promise<void> => {
    event.preventDefault();

    setIsSubmitting(true);
    setErrorMessage("");
    setSuccessMessage("");

    try {
      let socialLinks: SocialLink[] = [];

      try {
        const parsed = JSON.parse(formValues.socialLinksText) as unknown;

        if (!Array.isArray(parsed)) {
          throw new Error("Social links must be a JSON array.");
        }

        socialLinks = parsed as SocialLink[];
      } catch {
        throw new Error("Invalid social links JSON format.");
      }

      const payload = {
        site_title: formValues.siteTitle,
        hero_description: formValues.heroDescription,
        contact_email: formValues.contactEmail,
        social_links: socialLinks,
      };

      await axiosClient.patch("/admin/settings", payload);
      setSuccessMessage("Settings updated successfully.");
    } catch (error) {
      const axiosError = error as AxiosError<ApiResponse<null>>;
      setErrorMessage(
        axiosError.response?.data?.message ??
          (error instanceof Error ? error.message : "Failed to update settings."),
      );
    } finally {
      setIsSubmitting(false);
    }
  };

  if (isLoading) {
    return (
      <section className="flex min-h-[40vh] items-center justify-center">
        <div className="inline-flex items-center gap-2 rounded-lg border border-violet-400/25 bg-black/20 px-3 py-2 text-slate-200">
          <Loader2 className="size-4 animate-spin text-violet-300" />
          <span>Loading settings...</span>
        </div>
      </section>
    );
  }

  return (
    <section className="space-y-6">
      <div>
        <h2 className="text-xl font-semibold text-white">Site Settings</h2>
        <p className="mt-1 text-sm text-slate-300">Update your portfolio hero and contact identity.</p>
      </div>

      {errorMessage ? (
        <div className="rounded-xl border border-rose-500/35 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
          {errorMessage}
        </div>
      ) : null}

      <div className="rounded-2xl border border-slate-700/70 bg-slate-900/50 p-5 backdrop-blur-md">
        <form className="space-y-4" onSubmit={handleSubmit}>
          <label className="block space-y-1.5">
            <span className="text-sm text-slate-300">Site Title</span>
            <input
              required
              name="siteTitle"
              value={formValues.siteTitle}
              onChange={handleInputChange}
              className="h-11 w-full rounded-xl border border-slate-700 bg-slate-900/70 px-3 text-sm text-white outline-none transition focus:border-violet-400"
            />
          </label>

          <label className="block space-y-1.5">
            <span className="text-sm text-slate-300">Hero Description</span>
            <textarea
              required
              name="heroDescription"
              rows={4}
              value={formValues.heroDescription}
              onChange={handleInputChange}
              className="w-full rounded-xl border border-slate-700 bg-slate-900/70 px-3 py-2 text-sm text-white outline-none transition focus:border-violet-400"
            />
          </label>

          <label className="block space-y-1.5">
            <span className="text-sm text-slate-300">Contact Email</span>
            <input
              required
              name="contactEmail"
              type="email"
              value={formValues.contactEmail}
              onChange={handleInputChange}
              className="h-11 w-full rounded-xl border border-slate-700 bg-slate-900/70 px-3 text-sm text-white outline-none transition focus:border-violet-400"
            />
          </label>

          <label className="block space-y-1.5">
            <div className="flex items-center justify-between gap-2">
              <span className="text-sm text-slate-300">Social Links (JSON Array)</span>
              <span className="text-xs text-emerald-300">{parsedSocialLinksPreview} links</span>
            </div>
            <textarea
              name="socialLinksText"
              rows={8}
              value={formValues.socialLinksText}
              onChange={handleInputChange}
              className="w-full rounded-xl border border-slate-700 bg-slate-900/70 px-3 py-2 text-sm text-white outline-none transition focus:border-violet-400"
            />
          </label>

          <button
            type="submit"
            disabled={isSubmitting}
            className="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-violet-500 font-medium text-white transition hover:bg-violet-400 disabled:cursor-not-allowed disabled:opacity-70"
          >
            {isSubmitting ? <Loader2 className="size-4 animate-spin" /> : <Save className="size-4" />}
            {isSubmitting ? "Saving..." : "Save Settings"}
          </button>
        </form>
      </div>

      <AnimatePresence>
        {successMessage ? (
          <motion.div
            initial={{ opacity: 0, y: 12 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: 12 }}
            className="fixed bottom-6 right-6 z-[70] inline-flex items-center gap-2 rounded-xl border border-emerald-500/40 bg-emerald-500/15 px-4 py-3 text-sm text-emerald-100 shadow-[0_0_25px_rgba(16,185,129,0.3)] backdrop-blur-md"
          >
            <CheckCircle2 className="size-4" />
            {successMessage}
          </motion.div>
        ) : null}
      </AnimatePresence>
    </section>
  );
}
