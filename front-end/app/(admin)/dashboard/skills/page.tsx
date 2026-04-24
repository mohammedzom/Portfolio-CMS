"use client";

import { AxiosError } from "axios";
import { motion } from "framer-motion";
import { Loader2, Pencil, Plus, Save, Sparkles, Trash2 } from "lucide-react";
import * as LucideIcons from "lucide-react";
import { type ChangeEvent, type ComponentType, type FormEvent, useEffect, useState } from "react";

import { axiosClient, type ApiResponse } from "@/lib/axios";
import { ConfirmDialog } from "@/src/components/admin/ConfirmDialog";
import { SlideOverModal } from "@/src/components/admin/SlideOverModal";

interface Skill {
  id: number;
  name: string;
  proficiency: number;
  icon: string | null;
  type: string | null;
}

interface SkillsCollection {
  data?: Skill[];
  skills?: Skill[];
}

interface SkillFormValues {
  name: string;
  proficiency: number;
  icon: string;
  type: string;
}

type ModalMode = "create" | "edit";

const initialFormValues: SkillFormValues = {
  name: "",
  proficiency: 50,
  icon: "Sparkles",
  type: "technical",
};

function extractSkills(payload: unknown): Skill[] {
  if (Array.isArray(payload)) {
    return payload as Skill[];
  }

  if (payload && typeof payload === "object") {
    const normalized = payload as SkillsCollection;

    if (Array.isArray(normalized.data)) {
      return normalized.data;
    }

    if (Array.isArray(normalized.skills)) {
      return normalized.skills;
    }
  }

  return [];
}

function resolveIcon(iconName: string | null | undefined) {
  if (!iconName || iconName.startsWith("http")) {
    return Sparkles;
  }

  const icon = (LucideIcons as Record<string, unknown>)[iconName];

  if (typeof icon === "function") {
    return icon as ComponentType<{ className?: string }>;
  }

  return Sparkles;
}

export default function SkillsManagementPage() {
  const [skills, setSkills] = useState<Skill[]>([]);
  const [isLoading, setIsLoading] = useState<boolean>(true);
  const [errorMessage, setErrorMessage] = useState<string>("");

  const [isModalOpen, setIsModalOpen] = useState<boolean>(false);
  const [modalMode, setModalMode] = useState<ModalMode>("create");
  const [selectedSkill, setSelectedSkill] = useState<Skill | null>(null);
  const [formValues, setFormValues] = useState<SkillFormValues>(initialFormValues);
  const [isSubmitting, setIsSubmitting] = useState<boolean>(false);

  const [skillToDelete, setSkillToDelete] = useState<Skill | null>(null);
  const [isDeleting, setIsDeleting] = useState<boolean>(false);

  const fetchSkills = async (): Promise<void> => {
    setIsLoading(true);
    setErrorMessage("");

    try {
      const response = await axiosClient.get<ApiResponse<unknown>>("/admin/skills");
      setSkills(extractSkills(response.data.data));
    } catch (error) {
      const axiosError = error as AxiosError<ApiResponse<null>>;
      setErrorMessage(axiosError.response?.data?.message ?? "Failed to load skills.");
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    void fetchSkills();
  }, []);

  const openCreateModal = (): void => {
    setModalMode("create");
    setSelectedSkill(null);
    setFormValues(initialFormValues);
    setIsModalOpen(true);
  };

  const openEditModal = (skill: Skill): void => {
    setModalMode("edit");
    setSelectedSkill(skill);
    setFormValues({
      name: skill.name,
      proficiency: skill.proficiency,
      icon: skill.icon ?? "Sparkles",
      type: skill.type ?? "technical",
    });
    setIsModalOpen(true);
  };

  const handleInputChange = (
    event: ChangeEvent<HTMLInputElement | HTMLSelectElement>,
  ): void => {
    const { name, value } = event.target;

    setFormValues((previous) => ({
      ...previous,
      [name]: name === "proficiency" ? Number(value) : value,
    }));
  };

  const closeModal = (): void => {
    if (isSubmitting) {
      return;
    }

    setIsModalOpen(false);
  };

  const handleSubmit = async (event: FormEvent<HTMLFormElement>): Promise<void> => {
    event.preventDefault();

    setIsSubmitting(true);
    setErrorMessage("");

    try {
      const payload = {
        name: formValues.name,
        proficiency: formValues.proficiency,
        icon: formValues.icon,
        type: formValues.type,
      };

      if (modalMode === "create") {
        await axiosClient.post("/admin/skills", payload);
      } else if (selectedSkill) {
        await axiosClient.patch(`/admin/skills/${selectedSkill.id}`, payload);
      }

      setIsModalOpen(false);
      await fetchSkills();
    } catch (error) {
      const axiosError = error as AxiosError<ApiResponse<null>>;
      setErrorMessage(axiosError.response?.data?.message ?? "Failed to save skill.");
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleDelete = async (): Promise<void> => {
    if (!skillToDelete) {
      return;
    }

    setIsDeleting(true);

    try {
      await axiosClient.delete(`/admin/skills/${skillToDelete.id}`);
      setSkillToDelete(null);
      await fetchSkills();
    } catch (error) {
      const axiosError = error as AxiosError<ApiResponse<null>>;
      setErrorMessage(axiosError.response?.data?.message ?? "Failed to delete skill.");
    } finally {
      setIsDeleting(false);
    }
  };

  return (
    <section className="space-y-6">
      <div className="flex flex-wrap items-center justify-between gap-3">
        <div>
          <h2 className="text-xl font-semibold text-white">Skills Management</h2>
          <p className="mt-1 text-sm text-slate-300">Manage skill cards, levels, and icon style.</p>
        </div>

        <button
          type="button"
          onClick={openCreateModal}
          className="inline-flex items-center gap-2 rounded-xl bg-violet-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-violet-400"
        >
          <Plus className="size-4" />
          Add Skill
        </button>
      </div>

      {errorMessage ? (
        <div className="rounded-xl border border-rose-500/35 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
          {errorMessage}
        </div>
      ) : null}

      {isLoading ? (
        <div className="flex min-h-[35vh] items-center justify-center">
          <div className="inline-flex items-center gap-2 rounded-lg border border-violet-400/25 bg-black/20 px-3 py-2 text-slate-200">
            <Loader2 className="size-4 animate-spin text-violet-300" />
            <span>Loading skills...</span>
          </div>
        </div>
      ) : (
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
          {skills.map((skill) => {
            const Icon = resolveIcon(skill.icon);

            return (
              <motion.article
                key={skill.id}
                whileHover={{ scale: 1.02 }}
                transition={{ duration: 0.2 }}
                className="rounded-2xl border border-slate-700/70 bg-slate-900/50 p-4 backdrop-blur-md hover:border-violet-400/40 hover:shadow-[0_0_25px_rgba(139,92,246,0.2)]"
              >
                <div className="mb-4 flex items-start justify-between gap-3">
                  <div className="inline-flex rounded-lg border border-violet-400/30 bg-violet-500/10 p-2 text-violet-300">
                    <Icon className="size-4" />
                  </div>
                  <div className="flex gap-1.5">
                    <button
                      type="button"
                      onClick={() => {
                        openEditModal(skill);
                      }}
                      className="rounded-lg border border-indigo-400/35 bg-indigo-500/10 p-2 text-indigo-200 transition hover:bg-indigo-500/20"
                    >
                      <Pencil className="size-3.5" />
                    </button>
                    <button
                      type="button"
                      onClick={() => {
                        setSkillToDelete(skill);
                      }}
                      className="rounded-lg border border-rose-500/35 bg-rose-500/10 p-2 text-rose-200 transition hover:bg-rose-500/20"
                    >
                      <Trash2 className="size-3.5" />
                    </button>
                  </div>
                </div>

                <p className="text-base font-semibold text-white">{skill.name}</p>
                <p className="mt-1 text-xs text-slate-400">{skill.type ?? "technical"}</p>

                <div className="mt-4 space-y-2">
                  <div className="flex items-center justify-between text-xs text-slate-300">
                    <span>Proficiency</span>
                    <span className="font-medium text-emerald-300">{skill.proficiency}%</span>
                  </div>
                  <div className="h-2 rounded-full bg-slate-800">
                    <div
                      className="h-2 rounded-full bg-gradient-to-r from-emerald-500 to-emerald-400"
                      style={{ width: `${Math.max(0, Math.min(skill.proficiency, 100))}%` }}
                    />
                  </div>
                </div>
              </motion.article>
            );
          })}
        </div>
      )}

      <SlideOverModal
        isOpen={isModalOpen}
        title={modalMode === "create" ? "Add New Skill" : "Edit Skill"}
        onClose={closeModal}
        isBusy={isSubmitting}
      >
        <form className="space-y-4" onSubmit={handleSubmit}>
          <label className="block space-y-1.5">
            <span className="text-sm text-slate-300">Skill Name</span>
            <input
              required
              name="name"
              value={formValues.name}
              onChange={handleInputChange}
              className="h-11 w-full rounded-xl border border-slate-700 bg-slate-900/70 px-3 text-sm text-white outline-none transition focus:border-violet-400"
            />
          </label>

          <label className="block space-y-1.5">
            <span className="text-sm text-slate-300">Proficiency ({formValues.proficiency}%)</span>
            <input
              name="proficiency"
              type="range"
              min={0}
              max={100}
              value={formValues.proficiency}
              onChange={handleInputChange}
              className="w-full accent-emerald-500"
            />
          </label>

          <label className="block space-y-1.5">
            <span className="text-sm text-slate-300">Icon (Lucide name)</span>
            <input
              name="icon"
              value={formValues.icon}
              onChange={handleInputChange}
              placeholder="Sparkles"
              className="h-11 w-full rounded-xl border border-slate-700 bg-slate-900/70 px-3 text-sm text-white outline-none transition focus:border-violet-400"
            />
          </label>

          <label className="block space-y-1.5">
            <span className="text-sm text-slate-300">Type</span>
            <select
              name="type"
              value={formValues.type}
              onChange={handleInputChange}
              className="h-11 w-full rounded-xl border border-slate-700 bg-slate-900/70 px-3 text-sm text-white outline-none transition focus:border-violet-400"
            >
              <option value="technical">Technical</option>
              <option value="tool">Tool</option>
              <option value="soft">Soft</option>
            </select>
          </label>

          <button
            type="submit"
            disabled={isSubmitting}
            className="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-violet-500 font-medium text-white transition hover:bg-violet-400 disabled:cursor-not-allowed disabled:opacity-70"
          >
            {isSubmitting ? <Loader2 className="size-4 animate-spin" /> : <Save className="size-4" />}
            {isSubmitting ? "Saving..." : "Save Skill"}
          </button>
        </form>
      </SlideOverModal>

      <ConfirmDialog
        isOpen={Boolean(skillToDelete)}
        title="Delete skill?"
        description={
          skillToDelete
            ? `This will remove "${skillToDelete.name}" from your skills list.`
            : "This will remove this skill from your skills list."
        }
        confirmLabel="Delete"
        onClose={() => {
          if (!isDeleting) {
            setSkillToDelete(null);
          }
        }}
        onConfirm={handleDelete}
        isLoading={isDeleting}
      />
    </section>
  );
}
