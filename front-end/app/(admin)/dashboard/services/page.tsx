"use client";

import { AxiosError } from "axios";
import { motion } from "framer-motion";
import { Loader2, Pencil, Plus, Save, Trash2, Wrench } from "lucide-react";
import * as LucideIcons from "lucide-react";
import { type ChangeEvent, type ComponentType, type FormEvent, useEffect, useState } from "react";

import { axiosClient, type ApiResponse } from "@/lib/axios";
import { ConfirmDialog } from "@/src/components/admin/ConfirmDialog";
import { SlideOverModal } from "@/src/components/admin/SlideOverModal";

interface Service {
  id: number;
  title: string;
  description: string;
  icon: string | null;
}

interface ServicesCollection {
  data?: Service[];
  services?: Service[];
}

interface ServiceFormValues {
  title: string;
  icon: string;
  description: string;
}

type ModalMode = "create" | "edit";

const initialFormValues: ServiceFormValues = {
  title: "",
  icon: "Wrench",
  description: "",
};

function extractServices(payload: unknown): Service[] {
  if (Array.isArray(payload)) {
    return payload as Service[];
  }

  if (payload && typeof payload === "object") {
    const normalized = payload as ServicesCollection;

    if (Array.isArray(normalized.data)) {
      return normalized.data;
    }

    if (Array.isArray(normalized.services)) {
      return normalized.services;
    }
  }

  return [];
}

function resolveIcon(iconName: string | null | undefined) {
  if (!iconName) {
    return Wrench;
  }

  const icon = (LucideIcons as Record<string, unknown>)[iconName];

  if (typeof icon === "function") {
    return icon as ComponentType<{ className?: string }>;
  }

  return Wrench;
}

export default function ServicesManagementPage() {
  const [services, setServices] = useState<Service[]>([]);
  const [isLoading, setIsLoading] = useState<boolean>(true);
  const [errorMessage, setErrorMessage] = useState<string>("");

  const [isModalOpen, setIsModalOpen] = useState<boolean>(false);
  const [modalMode, setModalMode] = useState<ModalMode>("create");
  const [selectedService, setSelectedService] = useState<Service | null>(null);
  const [formValues, setFormValues] = useState<ServiceFormValues>(initialFormValues);
  const [isSubmitting, setIsSubmitting] = useState<boolean>(false);

  const [serviceToDelete, setServiceToDelete] = useState<Service | null>(null);
  const [isDeleting, setIsDeleting] = useState<boolean>(false);

  const fetchServices = async (): Promise<void> => {
    setIsLoading(true);
    setErrorMessage("");

    try {
      const response = await axiosClient.get<ApiResponse<unknown>>("/admin/services");
      setServices(extractServices(response.data.data));
    } catch (error) {
      const axiosError = error as AxiosError<ApiResponse<null>>;
      setErrorMessage(axiosError.response?.data?.message ?? "Failed to load services.");
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    void fetchServices();
  }, []);

  const openCreateModal = (): void => {
    setModalMode("create");
    setSelectedService(null);
    setFormValues(initialFormValues);
    setIsModalOpen(true);
  };

  const openEditModal = (service: Service): void => {
    setModalMode("edit");
    setSelectedService(service);
    setFormValues({
      title: service.title,
      icon: service.icon ?? "Wrench",
      description: service.description,
    });
    setIsModalOpen(true);
  };

  const handleInputChange = (
    event: ChangeEvent<HTMLInputElement | HTMLTextAreaElement>,
  ): void => {
    const { name, value } = event.target;
    setFormValues((previous) => ({ ...previous, [name]: value }));
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
        title: formValues.title,
        icon: formValues.icon,
        description: formValues.description,
      };

      if (modalMode === "create") {
        await axiosClient.post("/admin/services", payload);
      } else if (selectedService) {
        await axiosClient.patch(`/admin/services/${selectedService.id}`, payload);
      }

      setIsModalOpen(false);
      await fetchServices();
    } catch (error) {
      const axiosError = error as AxiosError<ApiResponse<null>>;
      setErrorMessage(axiosError.response?.data?.message ?? "Failed to save service.");
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleDelete = async (): Promise<void> => {
    if (!serviceToDelete) {
      return;
    }

    setIsDeleting(true);

    try {
      await axiosClient.delete(`/admin/services/${serviceToDelete.id}`);
      setServiceToDelete(null);
      await fetchServices();
    } catch (error) {
      const axiosError = error as AxiosError<ApiResponse<null>>;
      setErrorMessage(axiosError.response?.data?.message ?? "Failed to delete service.");
    } finally {
      setIsDeleting(false);
    }
  };

  return (
    <section className="space-y-6">
      <div className="flex flex-wrap items-center justify-between gap-3">
        <div>
          <h2 className="text-xl font-semibold text-white">Services Management</h2>
          <p className="mt-1 text-sm text-slate-300">Maintain service offerings and descriptions.</p>
        </div>

        <button
          type="button"
          onClick={openCreateModal}
          className="inline-flex items-center gap-2 rounded-xl bg-violet-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-violet-400"
        >
          <Plus className="size-4" />
          Add Service
        </button>
      </div>

      {errorMessage ? (
        <div className="rounded-xl border border-rose-500/35 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
          {errorMessage}
        </div>
      ) : null}

      <div className="overflow-hidden rounded-2xl border border-slate-700/70 bg-slate-900/50 backdrop-blur-md">
        <ul className="divide-y divide-slate-800/70">
          {isLoading ? (
            <li className="flex min-h-[35vh] items-center justify-center text-slate-300">
              <div className="inline-flex items-center gap-2 rounded-lg border border-violet-400/25 bg-black/20 px-3 py-2">
                <Loader2 className="size-4 animate-spin text-violet-300" />
                <span>Loading services...</span>
              </div>
            </li>
          ) : null}

          {!isLoading && services.length === 0 ? (
            <li className="px-4 py-12 text-center text-slate-300">No services found. Add your first one.</li>
          ) : null}

          {!isLoading
            ? services.map((service) => {
                const Icon = resolveIcon(service.icon);

                return (
                  <motion.li
                    key={service.id}
                    whileHover={{ scale: 1.01 }}
                    transition={{ duration: 0.2 }}
                    className="flex flex-wrap items-start justify-between gap-4 px-4 py-4 hover:bg-slate-800/30"
                  >
                    <div className="flex min-w-0 gap-3">
                      <div className="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-violet-400/30 bg-violet-500/10 text-violet-300">
                        <Icon className="size-4" />
                      </div>

                      <div className="min-w-0">
                        <p className="font-medium text-white">{service.title}</p>
                        <p className="mt-0.5 text-xs text-emerald-300">{service.icon ?? "Wrench"}</p>
                        <p className="mt-1 text-sm text-slate-300">{service.description}</p>
                      </div>
                    </div>

                    <div className="flex gap-2">
                      <button
                        type="button"
                        onClick={() => {
                          openEditModal(service);
                        }}
                        className="inline-flex items-center gap-1 rounded-lg border border-indigo-400/35 bg-indigo-500/10 px-3 py-1.5 text-xs text-indigo-200 transition hover:bg-indigo-500/20"
                      >
                        <Pencil className="size-3.5" />
                        Edit
                      </button>

                      <button
                        type="button"
                        onClick={() => {
                          setServiceToDelete(service);
                        }}
                        className="inline-flex items-center gap-1 rounded-lg border border-rose-500/35 bg-rose-500/10 px-3 py-1.5 text-xs text-rose-200 transition hover:bg-rose-500/20"
                      >
                        <Trash2 className="size-3.5" />
                        Delete
                      </button>
                    </div>
                  </motion.li>
                );
              })
            : null}
        </ul>
      </div>

      <SlideOverModal
        isOpen={isModalOpen}
        title={modalMode === "create" ? "Add New Service" : "Edit Service"}
        onClose={closeModal}
        isBusy={isSubmitting}
      >
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
            <span className="text-sm text-slate-300">Icon Name (Lucide)</span>
            <input
              required
              name="icon"
              value={formValues.icon}
              onChange={handleInputChange}
              placeholder="Wrench"
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

          <button
            type="submit"
            disabled={isSubmitting}
            className="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-violet-500 font-medium text-white transition hover:bg-violet-400 disabled:cursor-not-allowed disabled:opacity-70"
          >
            {isSubmitting ? <Loader2 className="size-4 animate-spin" /> : <Save className="size-4" />}
            {isSubmitting ? "Saving..." : "Save Service"}
          </button>
        </form>
      </SlideOverModal>

      <ConfirmDialog
        isOpen={Boolean(serviceToDelete)}
        title="Delete service?"
        description={
          serviceToDelete
            ? `This will remove "${serviceToDelete.title}" from your service list.`
            : "This will remove this service from your service list."
        }
        confirmLabel="Delete"
        onClose={() => {
          if (!isDeleting) {
            setServiceToDelete(null);
          }
        }}
        onConfirm={handleDelete}
        isLoading={isDeleting}
      />
    </section>
  );
}
