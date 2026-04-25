import { clearStoredAuthToken, getStoredAuthToken } from "@/lib/api/auth";
import { ApiError } from "@/lib/api/errors";
import type { ApiEnvelope, ApiErrorPayload } from "@/types/api";

type PreparedApiRequestConfig = Omit<ApiRequestConfig, "body"> & RequestInit;
type RequestInterceptor = (
  config: PreparedApiRequestConfig,
) => PreparedApiRequestConfig;
type ResponseInterceptor = (response: Response) => void | Promise<void>;

export type ApiRequestConfig = Omit<RequestInit, "body"> & {
  body?: BodyInit | Record<string, unknown> | unknown[] | null;
  auth?: boolean;
  query?: Record<string, string | number | boolean | null | undefined>;
};

const requestInterceptors: RequestInterceptor[] = [];
const responseInterceptors: ResponseInterceptor[] = [];

const apiBaseUrl =
  process.env.NEXT_PUBLIC_API_BASE_URL?.replace(/\/$/, "") ?? "/api/v1";

const apiKey = process.env.NEXT_PUBLIC_API_KEY;

requestInterceptors.push((config) => {
  const headers = new Headers(config.headers);

  headers.set("Accept", "application/json");

  if (apiKey) {
    headers.set("x-api-key", apiKey);
  }

  if (config.auth !== false) {
    const token = getStoredAuthToken();

    if (token) {
      headers.set("Authorization", `Bearer ${token}`);
    }
  }

  return {
    ...config,
    headers,
  };
});

responseInterceptors.push((response) => {
  if (response.status !== 401 || typeof window === "undefined") {
    return;
  }

  clearStoredAuthToken();

  if (!window.location.pathname.startsWith("/admin/login")) {
    window.location.assign("/admin/login");
  }
});

export function addRequestInterceptor(interceptor: RequestInterceptor): void {
  requestInterceptors.push(interceptor);
}

export function addResponseInterceptor(interceptor: ResponseInterceptor): void {
  responseInterceptors.push(interceptor);
}

export async function apiRequest<T>(
  path: string,
  config: ApiRequestConfig = {},
): Promise<T> {
  const normalizedConfig = prepareRequestConfig(config);
  const interceptedConfig = requestInterceptors.reduce(
    (currentConfig, interceptor) => interceptor(currentConfig),
    normalizedConfig,
  );

  const response = await fetch(buildUrl(path, config.query), interceptedConfig);

  await Promise.all(
    responseInterceptors.map((interceptor) => interceptor(response)),
  );

  const payload = await parseJson<ApiEnvelope<T> | ApiErrorPayload>(response);

  if (!response.ok || !payload?.success) {
    throw new ApiError(response.status, payload as ApiErrorPayload | null);
  }

  return (payload as ApiEnvelope<T>).data;
}

export const api = {
  get: <T>(path: string, config?: ApiRequestConfig) =>
    apiRequest<T>(path, { ...config, method: "GET" }),
  post: <T>(
    path: string,
    body?: ApiRequestConfig["body"],
    config?: ApiRequestConfig,
  ) => apiRequest<T>(path, { ...config, method: "POST", body }),
  patch: <T>(
    path: string,
    body?: ApiRequestConfig["body"],
    config?: ApiRequestConfig,
  ) => apiRequest<T>(path, { ...config, method: "PATCH", body }),
  delete: <T>(path: string, config?: ApiRequestConfig) =>
    apiRequest<T>(path, { ...config, method: "DELETE" }),
};

function prepareRequestConfig(
  config: ApiRequestConfig,
): PreparedApiRequestConfig {
  const headers = new Headers(config.headers);
  const body = config.body;

  if (body === undefined || body === null || body instanceof FormData) {
    return {
      ...config,
      headers,
      body: body ?? undefined,
    };
  }

  headers.set("Content-Type", "application/json");

  return {
    ...config,
    headers,
    body: JSON.stringify(body),
  };
}

function buildUrl(
  path: string,
  query?: ApiRequestConfig["query"],
): string {
  const url = new URL(`${apiBaseUrl}${path}`, getUrlOrigin());

  Object.entries(query ?? {}).forEach(([key, value]) => {
    if (value !== null && value !== undefined) {
      url.searchParams.set(key, String(value));
    }
  });

  if (apiBaseUrl.startsWith("http")) {
    return url.toString();
  }

  return `${url.pathname}${url.search}`;
}

function getUrlOrigin(): string {
  if (typeof window !== "undefined") {
    return window.location.origin;
  }

  return process.env.NEXT_PUBLIC_APP_URL ?? "http://localhost:3000";
}

async function parseJson<T>(response: Response): Promise<T | null> {
  const text = await response.text();

  if (!text) {
    return null;
  }

  return JSON.parse(text) as T;
}
