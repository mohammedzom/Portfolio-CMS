import type { FieldValues, Path, UseFormSetError } from "react-hook-form";

import { ApiError } from "@/lib/api/errors";

export function applyBackendValidationErrors<T extends FieldValues>(
  error: unknown,
  setError: UseFormSetError<T>,
): string {
  if (!(error instanceof ApiError)) {
    return "Something went wrong. Please try again.";
  }

  if (!error.validationErrors) {
    return error.message;
  }

  Object.entries(error.validationErrors).forEach(([field, messages]) => {
    setError(field as Path<T>, {
      type: "server",
      message: messages[0] ?? "Invalid value.",
    });
  });

  return error.message;
}
