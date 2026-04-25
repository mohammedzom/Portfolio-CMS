import type { ApiErrorPayload, ValidationErrors } from "@/types/api";

export class ApiError extends Error {
  readonly status: number;

  readonly code: string | null;

  readonly payload: ApiErrorPayload | null;

  readonly validationErrors: ValidationErrors | null;

  constructor(status: number, payload: ApiErrorPayload | null) {
    super(payload?.message ?? `Request failed with status ${status}`);
    this.name = "ApiError";
    this.status = status;
    this.code = payload?.error_code ?? null;
    this.payload = payload;
    this.validationErrors =
      payload?.error_code === "VALIDATION_ERROR" && isValidationErrors(payload.data)
        ? payload.data
        : null;
  }
}

function isValidationErrors(value: unknown): value is ValidationErrors {
  if (!value || typeof value !== "object" || Array.isArray(value)) {
    return false;
  }

  return Object.values(value).every(
    (messages) =>
      Array.isArray(messages) &&
      messages.every((message) => typeof message === "string"),
  );
}
