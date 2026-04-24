"use client";

import { AxiosError } from "axios";
import { AnimatePresence, motion } from "framer-motion";
import {
  AlertTriangle,
  FileImage,
  Loader2,
  Pencil,
  Plus,
  Save,
  Trash2,
  X,
} from "lucide-react";
import { ChangeEvent, FormEvent, useEffect, useMemo, useState } from "react";

import { axiosClient, type ApiResponse } from "@/lib/axios";

interface Project {
  id: number;
  title: string;
  description: string;
  category: string | null;
  live_url: string | null;
  tech_stack: string[];
  images: string[];
  deleted_at: string | null;
}

interface ProjectsCollection {
  data?: Project[];
  projects?: Project[];
}

interface ProjectFormValues {
  title: string;
  description: string;
  category: string;
  link: string;
  imageFile: File | null;
}

type ModalMode = "create" | "edit";
type DeleteMode = "soft" | "force";

const initialFormValues: ProjectFormValues = {
  title: "",
  description: "",
  category: "",
  link: "",
  imageFile: null,
};

function extractProjects(payload: unknown): Project[] {
  if (Array.isArray(payload)) {
    return payload as Project[];
  }

  if (payload && typeof payload === "object") {
    const normalized = payload as ProjectsCollection;

    if (Array.isArray(normalized.data)) {
      return normalized.data;
    }

    if (Array.isArray(normalized.projects)) {
      return normalized.projects;
    }
  }

  return [];
}

function getThumbnail(project: Project): string | null {
  if (Array.isArray(project.images) && project.images.length > 0) {
    return project.images[0] ?? null;
  }

  return null;
}

export default function ProjectsManagementPage() {
  const [projects, setProjects] = useState<Project[]>([]);
  const [isLoading, setIsLoading] = useState<boolean>(true);
  const [errorMessage, setErrorMessage] = useState<string>("");

  const [isModalOpen, setIsModalOpen] = useState<boolean>(false);
  const [modalMode, setModalMode] = useState<ModalMode>("create");
  const [selectedProject, setSelectedProject] = useState<Project | null>(null);
  const [formValues, setFormValues] = useState<ProjectFormValues>(initialFormValues);
  const [isSubmitting, setIsSubmitting] = useState<boolean>(false);

  const [projectToDelete, setProjectToDelete] = useState<Project | null>(null);
  const [deleteMode, setDeleteMode] = useState<DeleteMode>("soft");
  const [isDeleting, setIsDeleting] = useState<boolean>(false);

  const pageTitle = useMemo(() => {
    return modalMode === "create" ? "Add New Project" : "Edit Project";
  }, [modalMode]);

  const fetchProjects = async (): Promise<void> => {
    setIsLoading(true);
    setErrorMessage("");

    try {
      const response = await axiosClient.get<ApiResponse<unknown>>("/admin/projects");
      const nextProjects = extractProjects(response.data.data);
      setProjects(nextProjects);
    } catch (error) {
      const axiosError = error as AxiosError<ApiResponse<null>>;
      setErrorMessage(axiosError.response?.data?.message ?? "Failed to load projects.");
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    void fetchProjects();
  }, []);

  const openCreateModal = (): void => {
    setModalMode("create");
    setSelectedProject(null);
    setFormValues(initialFormValues);
    setIsModalOpen(true);
  };

  const openEditModal = (project: Project): void => {
    setModalMode("edit");
    setSelectedProject(project);
    setFormValues({
      title: project.title ?? "",
      description: project.description ?? "",
      category: project.category ?? "",
      link: project.live_url ?? "",
      imageFile: null,
    });
    setIsModalOpen(true);
  };

  const handleInputChange = (
    event: ChangeEvent<HTMLInputElement | HTMLTextAreaElement>,
  ): void => {
    const { name, value } = event.target;
    setFormValues((previous) => ({ ...previous, [name]: value }));
  };

  const handleImageChange = (event: ChangeEvent<HTMLInputElement>): void => {
    const file = event.target.files?.[0] ?? null;
    setFormValues((previous) => ({ ...previous, imageFile: file }));
  };

  const closeProjectModal = (): void => {
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
      const formData = new FormData();
      formData.append("title", formValues.title);
      formData.append("description", formValues.description);
      formData.append("category", formValues.category);
      formData.append("live_url", formValues.link);

      if (formValues.imageFile) {
        formData.append("image", formValues.imageFile);
      }

      if (modalMode === "create") {
        await axiosClient.post("/admin/projects", formData, {
          headers: { "Content-Type": "multipart/form-data" },
        });
      } else if (selectedProject) {
        await axiosClient.patch(`/admin/projects/${selectedProject.id}`, formData, {
          headers: { "Content-Type": "multipart/form-data" },
        });
      }

      setIsModalOpen(false);
      await fetchProjects();
    } catch (error) {
      const axiosError = error as AxiosError<ApiResponse<null>>;
      setErrorMessage(axiosError.response?.data?.message ?? "Failed to save project.");
    } finally {
      setIsSubmitting(false);
    }
  };

  const requestDelete = (project: Project, mode: DeleteMode): void => {
    setDeleteMode(mode);
    setProjectToDelete(project);
  };

  const closeDeleteDialog = (): void => {
    if (isDeleting) {
      return;
    }

    setProjectToDelete(null);
  };

  const handleDeleteConfirm = async (): Promise<void> => {
    if (!projectToDelete) {
      return;
    }

    setIsDeleting(true);
    setErrorMessage("");

    try {
      if (deleteMode === "soft") {
        await axiosClient.delete(`/admin/projects/${projectToDelete.id}`);
      } else {
        try {
          await axiosClient.delete(`/admin/projects/${projectToDelete.id}/force-delete`);
        } catch {
          await axiosClient.delete(`/admin/projects/${projectToDelete.id}`, {
            params: { force: 1 },
          });
        }
      }

      setProjectToDelete(null);
      await fetchProjects();
    } catch (error) {
      const axiosError = error as AxiosError<ApiResponse<null>>;
      setErrorMessage(axiosError.response?.data?.message ?? "Failed to delete project.");
    } finally {
      setIsDeleting(false);
    }
  };

  return (
    <section className="space-y-6">
      <div className="flex flex-wrap items-center justify-between gap-3">
        <div>
          <h2 className="text-xl font-semibold text-white">Projects Management</h2>
          <p className="mt-1 text-sm text-slate-300">Create, edit, and organize portfolio projects.</p>
        </div>

        <button
          type="button"
          onClick={openCreateModal}
          className="inline-flex items-center gap-2 rounded-xl bg-violet-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-violet-400"
        >
          <Plus className="size-4" />
          Add Project
        </button>
      </div>

      {errorMessage ? (
        <div className="rounded-xl border border-rose-500/35 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
          {errorMessage}
        </div>
      ) : null}

      <div className="overflow-x-auto rounded-2xl border border-slate-700/70 bg-slate-900/50 backdrop-blur">
        <table className="min-w-full divide-y divide-slate-700/60">
          <thead className="bg-slate-900/70">
            <tr className="text-left text-xs uppercase tracking-[0.12em] text-slate-300">
              <th className="px-4 py-3">Image</th>
              <th className="px-4 py-3">Title</th>
              <th className="px-4 py-3">Tech Stack</th>
              <th className="px-4 py-3">Category</th>
              <th className="px-4 py-3 text-right">Actions</th>
            </tr>
          </thead>

          <tbody className="divide-y divide-slate-800/70 text-sm">
            {isLoading ? (
              <tr>
                <td colSpan={5} className="px-4 py-12 text-center text-slate-300">
                  <div className="inline-flex items-center gap-2 rounded-lg border border-violet-400/25 bg-black/20 px-3 py-2">
                    <Loader2 className="size-4 animate-spin text-violet-300" />
                    <span>Loading projects...</span>
                  </div>
                </td>
              </tr>
            ) : null}

            {!isLoading && projects.length === 0 ? (
              <tr>
                <td colSpan={5} className="px-4 py-12 text-center text-slate-300">
                  No projects found. Add your first one.
                </td>
              </tr>
            ) : null}

            {!isLoading
              ? projects.map((project) => {
                  const thumbnail = getThumbnail(project);

                  return (
                    <tr key={project.id} className="text-slate-200">
                      <td className="px-4 py-3">
                        <div className="relative h-12 w-16 overflow-hidden rounded-lg border border-slate-700/70 bg-slate-800/80">
                          {thumbnail ? (
                            <img
                              src={thumbnail}
                              alt={project.title}
                              className="h-full w-full object-cover"
                            />
                          ) : (
                            <div className="flex h-full items-center justify-center text-slate-400">
                              <FileImage className="size-4" />
                            </div>
                          )}
                        </div>
                      </td>

                      <td className="px-4 py-3">
                        <p className="font-medium text-white">{project.title}</p>
                        <p className="line-clamp-1 text-xs text-slate-400">{project.description}</p>
                      </td>

                      <td className="px-4 py-3">
                        <div className="flex max-w-[260px] flex-wrap gap-1.5">
                          {project.tech_stack?.length ? (
                            project.tech_stack.map((tech) => (
                              <span
                                key={`${project.id}-${tech}`}
                                className="rounded-full border border-emerald-500/40 bg-emerald-500/10 px-2 py-1 text-xs text-emerald-200"
                              >
                                {tech}
                              </span>
                            ))
                          ) : (
                            <span className="text-xs text-slate-500">No stack</span>
                          )}
                        </div>
                      </td>

                      <td className="px-4 py-3 text-slate-300">{project.category ?? "—"}</td>

                      <td className="px-4 py-3">
                        <div className="flex justify-end gap-2">
                          <button
                            type="button"
                            onClick={() => {
                              openEditModal(project);
                            }}
                            className="inline-flex items-center gap-1 rounded-lg border border-indigo-400/35 bg-indigo-500/10 px-3 py-1.5 text-xs text-indigo-200 transition hover:bg-indigo-500/20"
                          >
                            <Pencil className="size-3.5" />
                            Edit
                          </button>

                          <button
                            type="button"
                            onClick={() => {
                              requestDelete(project, "soft");
                            }}
                            className="inline-flex items-center gap-1 rounded-lg border border-rose-500/35 bg-rose-500/10 px-3 py-1.5 text-xs text-rose-200 transition hover:bg-rose-500/20"
                          >
                            <Trash2 className="size-3.5" />
                            Delete
                          </button>

                          <button
                            type="button"
                            onClick={() => {
                              requestDelete(project, "force");
                            }}
                            className="inline-flex items-center gap-1 rounded-lg border border-orange-500/40 bg-orange-500/10 px-3 py-1.5 text-xs text-orange-200 transition hover:bg-orange-500/20"
                          >
                            <AlertTriangle className="size-3.5" />
                            Force Delete
                          </button>
                        </div>
                      </td>
                    </tr>
                  );
                })
              : null}
          </tbody>
        </table>
      </div>

      <AnimatePresence>
        {isModalOpen ? (
          <>
            <motion.button
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              type="button"
              aria-label="Close project modal"
              onClick={closeProjectModal}
              className="fixed inset-0 z-40 bg-black/70"
            />

            <motion.section
              initial={{ x: 420, opacity: 0 }}
              animate={{ x: 0, opacity: 1 }}
              exit={{ x: 420, opacity: 0 }}
              transition={{ duration: 0.25, ease: "easeOut" }}
              className="fixed inset-y-0 right-0 z-50 w-full max-w-xl border-l border-violet-400/20 bg-slate-950/95 p-6 shadow-2xl backdrop-blur-md"
            >
              <div className="mb-5 flex items-center justify-between">
                <h3 className="text-lg font-semibold text-white">{pageTitle}</h3>
                <button
                  type="button"
                  onClick={closeProjectModal}
                  className="rounded-lg border border-slate-700 bg-slate-900/70 p-2 text-slate-300 transition hover:text-white"
                >
                  <X className="size-4" />
                </button>
              </div>

              <form className="space-y-4" onSubmit={handleSubmit}>
                <label className="block space-y-1.5">
                  <span className="text-sm text-slate-300">Title</span>
                  <input
                    required
                    name="title"
                    value={formValues.title}
                    onChange={handleInputChange}
                    className="h-11 w-full rounded-xl border border-slate-700 bg-slate-900/70 px-3 text-sm text-white outline-none transition focus:border-violet-400"
                  />
                </label>

                <label className="block space-y-1.5">
                  <span className="text-sm text-slate-300">Description</span>
                  <textarea
                    required
                    name="description"
                    rows={4}
                    value={formValues.description}
                    onChange={handleInputChange}
                    className="w-full rounded-xl border border-slate-700 bg-slate-900/70 px-3 py-2 text-sm text-white outline-none transition focus:border-violet-400"
                  />
                </label>

                <label className="block space-y-1.5">
                  <span className="text-sm text-slate-300">Link</span>
                  <input
                    name="link"
                    type="url"
                    value={formValues.link}
                    onChange={handleInputChange}
                    placeholder="https://example.com"
                    className="h-11 w-full rounded-xl border border-slate-700 bg-slate-900/70 px-3 text-sm text-white outline-none transition focus:border-violet-400"
                  />
                </label>

                <label className="block space-y-1.5">
                  <span className="text-sm text-slate-300">Category</span>
                  <input
                    name="category"
                    value={formValues.category}
                    onChange={handleInputChange}
                    placeholder="Web, App, UI/UX"
                    className="h-11 w-full rounded-xl border border-slate-700 bg-slate-900/70 px-3 text-sm text-white outline-none transition focus:border-violet-400"
                  />
                </label>

                <label className="block space-y-1.5">
                  <span className="text-sm text-slate-300">Image</span>
                  <input
                    name="image"
                    type="file"
                    accept="image/*"
                    onChange={handleImageChange}
                    className="block w-full cursor-pointer rounded-xl border border-slate-700 bg-slate-900/70 px-3 py-2 text-sm text-slate-300 file:mr-3 file:rounded-lg file:border-0 file:bg-violet-500 file:px-3 file:py-1.5 file:text-white"
                  />
                </label>

                <button
                  type="submit"
                  disabled={isSubmitting}
                  className="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-violet-500 font-medium text-white transition hover:bg-violet-400 disabled:cursor-not-allowed disabled:opacity-70"
                >
                  {isSubmitting ? <Loader2 className="size-4 animate-spin" /> : <Save className="size-4" />}
                  {isSubmitting ? "Saving..." : "Save Project"}
                </button>
              </form>
            </motion.section>
          </>
        ) : null}
      </AnimatePresence>

      <AnimatePresence>
        {projectToDelete ? (
          <>
            <motion.button
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              type="button"
              aria-label="Close delete dialog"
              onClick={closeDeleteDialog}
              className="fixed inset-0 z-50 bg-black/70"
            />

            <motion.div
              initial={{ opacity: 0, y: 12 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: 12 }}
              className="fixed inset-0 z-[60] flex items-center justify-center p-4"
            >
              <div className="w-full max-w-md rounded-2xl border border-slate-700 bg-slate-900/90 p-5 shadow-[0_0_35px_rgba(139,92,246,0.2)] backdrop-blur-md">
                <h4 className="text-base font-semibold text-white">
                  {deleteMode === "soft" ? "Delete project?" : "Force delete project?"}
                </h4>
                <p className="mt-2 text-sm text-slate-300">
                  {deleteMode === "soft"
                    ? "This project will be archived and can be restored later."
                    : "This action is permanent and cannot be undone."}
                </p>
                <p className="mt-1 text-sm text-violet-200">{projectToDelete.title}</p>

                <div className="mt-5 flex justify-end gap-2">
                  <button
                    type="button"
                    onClick={closeDeleteDialog}
                    disabled={isDeleting}
                    className="rounded-lg border border-slate-700 bg-slate-800/70 px-3 py-2 text-sm text-slate-200"
                  >
                    Cancel
                  </button>
                  <button
                    type="button"
                    onClick={handleDeleteConfirm}
                    disabled={isDeleting}
                    className="inline-flex items-center gap-2 rounded-lg bg-rose-500 px-3 py-2 text-sm font-medium text-white disabled:opacity-70"
                  >
                    {isDeleting ? <Loader2 className="size-4 animate-spin" /> : <Trash2 className="size-4" />}
                    {deleteMode === "soft" ? "Delete" : "Force Delete"}
                  </button>
                </div>
              </div>
            </motion.div>
          </>
        ) : null}
      </AnimatePresence>
    </section>
  );
}
