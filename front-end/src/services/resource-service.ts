import { api, type ApiRequestConfig } from "@/lib/api/client";

export type ListQuery = {
  archived?: boolean;
  search?: string;
  page?: number;
  category?: string;
};

export type ResourceService<TResource, TPayload extends Record<string, unknown> = Record<string, unknown>> = {
  list: (query?: ListQuery) => Promise<TResource[]>;
  get: (id: number | string) => Promise<TResource>;
  create: (payload: TPayload | FormData) => Promise<TResource>;
  update: (id: number | string, payload: Partial<TPayload> | FormData) => Promise<TResource>;
  archive: (id: number | string) => Promise<unknown>;
  restore: (id: number | string) => Promise<TResource>;
  forceDelete: (id: number | string) => Promise<unknown>;
};

export function createResourceService<
  TResource,
  TPayload extends Record<string, unknown> = Record<string, unknown>,
>(
  basePath: string,
): ResourceService<TResource, TPayload> {
  return {
    list: (query) => api.get<TResource[]>(basePath, { query }),
    get: (id) => api.get<TResource>(`${basePath}/${id}`),
    create: (payload) => api.post<TResource>(basePath, payload),
    update: (id, payload) => api.patch<TResource>(`${basePath}/${id}`, payload),
    archive: (id) => api.delete<unknown>(`${basePath}/${id}`),
    restore: (id) => api.patch<TResource>(`${basePath}/${id}/restore`),
    forceDelete: (id) => api.delete<unknown>(`${basePath}/${id}/force-delete`),
  };
}

export function toFormData(
  values: Record<string, unknown>,
  fileFields: string[] = [],
): FormData {
  const formData = new FormData();

  Object.entries(values).forEach(([key, value]) => {
    if (value === undefined || value === null || value === "") {
      return;
    }

    if (fileFields.includes(key)) {
      appendFileValue(formData, key, value);
      return;
    }

    if (Array.isArray(value)) {
      value.forEach((item, index) => {
        if (typeof item === "object" && !(item instanceof File)) {
          Object.entries(item as Record<string, unknown>).forEach(([childKey, childValue]) => {
            if (childValue !== undefined && childValue !== null && childValue !== "") {
              formData.append(`${key}[${index}][${childKey}]`, String(childValue));
            }
          });
          return;
        }

        formData.append(`${key}[]`, String(item));
      });
      return;
    }

    formData.append(key, String(value));
  });

  return formData;
}

function appendFileValue(formData: FormData, key: string, value: unknown): void {
  if (value instanceof File) {
    formData.append(key, value);
    return;
  }

  if (value instanceof FileList) {
    Array.from(value).forEach((file) => formData.append(`${key}[]`, file));
    return;
  }

  if (Array.isArray(value)) {
    value.forEach((file) => {
      if (file instanceof File) {
        formData.append(`${key}[]`, file);
      }
    });
  }
}

export function noBodyConfig(): ApiRequestConfig {
  return {};
}
