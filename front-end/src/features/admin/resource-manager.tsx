"use client";

import { zodResolver } from "@hookform/resolvers/zod";
import { Edit, Loader2, Plus, RotateCcw, Search, Trash2, X } from "lucide-react";
import { useCallback, useEffect, useState } from "react";
import { type Resolver, useForm } from "react-hook-form";

import { applyBackendValidationErrors } from "@/lib/forms/backend-errors";
import { Button } from "@/components/ui/button";
import {
  CheckboxField,
  SelectField,
  TextAreaField,
  TextField,
} from "@/components/ui/field";
import { EmptyState } from "@/components/ui/empty-state";
import type { AdminResourceConfig, FieldConfig } from "./resource-configs";

type ResourceBase = {
  id: number;
  deleted_at?: string | null;
};

export function ResourceManager<TResource extends ResourceBase>({
  config,
}: {
  config: AdminResourceConfig<TResource>;
}) {
  const [items, setItems] = useState<TResource[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [isSaving, setIsSaving] = useState(false);
  const [editingItem, setEditingItem] = useState<TResource | null>(null);
  const [isFormOpen, setIsFormOpen] = useState(false);
  const [archived, setArchived] = useState(false);
  const [search, setSearch] = useState("");
  const [notice, setNotice] = useState(config.notice ?? "");
  const [formMessage, setFormMessage] = useState("");

  const {
    register,
    handleSubmit,
    reset,
    setError,
    formState: { errors },
  } = useForm<Record<string, unknown>>({
    resolver: zodResolver(config.schema as never) as Resolver<Record<string, unknown>>,
    defaultValues: config.defaults,
  });

  const loadItems = useCallback(async () => {
    setIsLoading(true);
    try {
      const result = await config.service.list({
        ...(archived ? { archived: true } : {}),
        ...(search ? { search } : {}),
      });
      setItems(result as TResource[]);
    } catch (error) {
      setNotice(error instanceof Error ? error.message : "Unable to load records.");
    } finally {
      setIsLoading(false);
    }
  }, [archived, config.service, search]);

  useEffect(() => {
    queueMicrotask(() => void loadItems());
  }, [loadItems]);

  const openCreate = () => {
    setEditingItem(null);
    reset(config.defaults);
    setFormMessage("");
    setIsFormOpen(true);
  };

  const openEdit = (item: TResource) => {
    setEditingItem(item);
    reset(toFormValues(config.defaults, item));
    setFormMessage("");
    setIsFormOpen(true);
  };

  const closeForm = () => {
    setIsFormOpen(false);
    setEditingItem(null);
    setFormMessage("");
  };

  const onSubmit = async (values: Record<string, unknown>) => {
    setIsSaving(true);
    setFormMessage("");

    try {
      const payload = config.toPayload
        ? config.toPayload(cleanValues(values))
        : cleanValues(values);

      if (editingItem) {
        await config.service.update(editingItem.id, payload);
      } else {
        await config.service.create(payload);
      }

      closeForm();
      await loadItems();
    } catch (error) {
      setFormMessage(applyBackendValidationErrors(error, setError));
    } finally {
      setIsSaving(false);
    }
  };

  const handleArchive = async (item: TResource) => {
    await config.service.archive(item.id);
    await loadItems();
  };

  const handleRestore = async (item: TResource) => {
    await config.service.restore(item.id);
    await loadItems();
  };

  const handleForceDelete = async (item: TResource) => {
    await config.service.forceDelete(item.id);
    await loadItems();
  };

  const renderedRows = items.map((item) => (
        <tr key={item.id} className="border-t border-border">
          {config.columns.map((column) => (
            <td key={String(column.key)} className="px-4 py-3 align-top text-sm">
              {column.render
                ? column.render(item)
                : formatCellValue((item as Record<string, unknown>)[String(column.key)])}
            </td>
          ))}
          <td className="px-4 py-3">
            <div className="flex justify-end gap-2">
              <Button
                type="button"
                variant="secondary"
                className="size-9 p-0"
                onClick={() => openEdit(item)}
                aria-label="Edit"
              >
                <Edit size={16} />
              </Button>
              {archived ? (
                <>
                  <Button
                    type="button"
                    variant="secondary"
                    className="size-9 p-0"
                    onClick={() => handleRestore(item)}
                    aria-label="Restore"
                  >
                    <RotateCcw size={16} />
                  </Button>
                  <Button
                    type="button"
                    variant="danger"
                    className="size-9 p-0"
                    onClick={() => handleForceDelete(item)}
                    aria-label="Force delete"
                  >
                    <Trash2 size={16} />
                  </Button>
                </>
              ) : (
                <Button
                  type="button"
                  variant="danger"
                  className="size-9 p-0"
                  onClick={() => handleArchive(item)}
                  aria-label="Archive"
                >
                  <Trash2 size={16} />
                </Button>
              )}
            </div>
          </td>
        </tr>
      ));

  return (
    <section className="grid gap-6">
      <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
          <p className="text-sm font-medium uppercase tracking-wide text-primary">
            Admin
          </p>
          <h1 className="mt-2 text-3xl font-semibold">{config.title}</h1>
          <p className="mt-2 max-w-2xl text-sm leading-6 text-muted-foreground">
            {config.description}
          </p>
        </div>
        <Button type="button" onClick={openCreate}>
          <Plus size={18} />
          New
        </Button>
      </div>

      {notice ? (
        <div className="rounded-md border border-primary/30 bg-primary/10 px-4 py-3 text-sm text-foreground">
          {notice}
        </div>
      ) : null}

      <div className="rounded-lg border border-border bg-surface">
        <div className="flex flex-col gap-3 border-b border-border p-4 md:flex-row md:items-center md:justify-between">
          <label className="relative block w-full md:max-w-sm">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground" size={16} />
            <input
              value={search}
              onChange={(event) => setSearch(event.target.value)}
              placeholder="Search"
              className="min-h-10 w-full rounded-md border border-border bg-background pl-9 pr-3 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
            />
          </label>
          <Button
            type="button"
            variant={archived ? "primary" : "secondary"}
            onClick={() => setArchived((value) => !value)}
          >
            {archived ? "Viewing archived" : "View archived"}
          </Button>
        </div>

        {isLoading ? (
          <div className="flex items-center justify-center gap-2 px-4 py-12 text-sm text-muted-foreground">
            <Loader2 className="animate-spin" size={16} />
            Loading
          </div>
        ) : items.length === 0 ? (
          <div className="p-4">
            <EmptyState message={config.emptyMessage} />
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full min-w-[760px] text-left">
              <thead>
                <tr className="text-xs uppercase tracking-wide text-muted-foreground">
                  {config.columns.map((column) => (
                    <th key={String(column.key)} className="px-4 py-3 font-semibold">
                      {column.label}
                    </th>
                  ))}
                  <th className="px-4 py-3 text-right font-semibold">Actions</th>
                </tr>
              </thead>
              <tbody>{renderedRows}</tbody>
            </table>
          </div>
        )}
      </div>

      {isFormOpen ? (
        <div className="fixed inset-0 z-50 flex items-end bg-black/40 p-4 sm:items-center sm:justify-center">
          <form
            onSubmit={handleSubmit(onSubmit)}
            className="max-h-[90dvh] w-full max-w-3xl overflow-y-auto rounded-lg border border-border bg-surface p-5 shadow-2xl"
          >
            <div className="flex items-start justify-between gap-4">
              <div>
                <h2 className="text-xl font-semibold">
                  {editingItem ? `Edit ${config.title}` : `New ${config.title}`}
                </h2>
                {formMessage ? (
                  <p className="mt-2 text-sm text-danger">{formMessage}</p>
                ) : null}
              </div>
              <Button type="button" variant="ghost" className="size-9 p-0" onClick={closeForm}>
                <X size={18} />
              </Button>
            </div>

            <div className="mt-6 grid gap-4 sm:grid-cols-2">
              {config.fields.map((field) => (
                <FieldRenderer
                  key={field.name}
                  field={field}
                  register={register}
                  error={String(errors[field.name]?.message ?? "")}
                />
              ))}
            </div>

            <div className="mt-6 flex justify-end gap-3">
              <Button type="button" variant="secondary" onClick={closeForm}>
                Cancel
              </Button>
              <Button type="submit" disabled={isSaving}>
                {isSaving ? <Loader2 className="animate-spin" size={16} /> : null}
                Save
              </Button>
            </div>
          </form>
        </div>
      ) : null}
    </section>
  );
}

function FieldRenderer({
  field,
  register,
  error,
}: {
  field: FieldConfig;
  register: ReturnType<typeof useForm<Record<string, unknown>>>["register"];
  error: string;
}) {
  if (field.type === "textarea") {
    return <TextAreaField label={field.label} error={error} {...register(field.name)} />;
  }

  if (field.type === "select") {
    return (
      <SelectField label={field.label} error={error} {...register(field.name)}>
        {field.options?.map((option) => (
          <option key={option.value} value={option.value}>
            {option.label}
          </option>
        ))}
      </SelectField>
    );
  }

  if (field.type === "checkbox") {
    return <CheckboxField label={field.label} error={error} {...register(field.name)} />;
  }

  return (
    <TextField
      label={field.label}
      error={error}
      type={field.type ?? "text"}
      multiple={field.type === "file"}
      {...register(field.name)}
    />
  );
}

function cleanValues(values: Record<string, unknown>): Record<string, unknown> {
  return Object.fromEntries(
    Object.entries(values).filter(([, value]) => value !== "" && value !== undefined),
  );
}

function toFormValues(
  defaults: Record<string, unknown>,
  item: Record<string, unknown>,
): Record<string, unknown> {
  return Object.fromEntries(
    Object.entries(defaults).map(([key, defaultValue]) => {
      const value = item[key];

      if (Array.isArray(value)) {
        return [key, value.join(", ")];
      }

      if (typeof value === "string" && /^\d{4}-\d{2}-\d{2}/.test(value)) {
        return [key, value.slice(0, 10)];
      }

      return [key, value ?? defaultValue];
    }),
  );
}

function formatCellValue(value: unknown): React.ReactNode {
  if (Array.isArray(value)) {
    return value.join(", ");
  }

  if (typeof value === "boolean") {
    return value ? "Yes" : "No";
  }

  if (value === null || value === undefined || value === "") {
    return <span className="text-muted-foreground">-</span>;
  }

  return String(value);
}
