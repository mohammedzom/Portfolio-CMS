import { forwardRef, type InputHTMLAttributes, type SelectHTMLAttributes, type TextareaHTMLAttributes } from "react";

type BaseFieldProps = {
  label: string;
  error?: string;
};

export const TextField = forwardRef<
  HTMLInputElement,
  BaseFieldProps & InputHTMLAttributes<HTMLInputElement>
>(function TextField({ label, error, className = "", ...props }, ref) {
  return (
    <label className="grid gap-2 text-sm">
      <span className="font-medium text-foreground">{label}</span>
      <input
        ref={ref}
        className={`min-h-10 rounded-md border border-border bg-background px-3 py-2 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20 ${className}`}
        {...props}
      />
      {error ? <span className="text-xs text-danger">{error}</span> : null}
    </label>
  );
});

export const TextAreaField = forwardRef<
  HTMLTextAreaElement,
  BaseFieldProps & TextareaHTMLAttributes<HTMLTextAreaElement>
>(function TextAreaField({ label, error, className = "", ...props }, ref) {
  return (
    <label className="grid gap-2 text-sm">
      <span className="font-medium text-foreground">{label}</span>
      <textarea
        ref={ref}
        className={`min-h-28 rounded-md border border-border bg-background px-3 py-2 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20 ${className}`}
        {...props}
      />
      {error ? <span className="text-xs text-danger">{error}</span> : null}
    </label>
  );
});

export const SelectField = forwardRef<
  HTMLSelectElement,
  BaseFieldProps & SelectHTMLAttributes<HTMLSelectElement>
>(function SelectField({ label, error, className = "", children, ...props }, ref) {
  return (
    <label className="grid gap-2 text-sm">
      <span className="font-medium text-foreground">{label}</span>
      <select
        ref={ref}
        className={`min-h-10 rounded-md border border-border bg-background px-3 py-2 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20 ${className}`}
        {...props}
      >
        {children}
      </select>
      {error ? <span className="text-xs text-danger">{error}</span> : null}
    </label>
  );
});

export const CheckboxField = forwardRef<
  HTMLInputElement,
  BaseFieldProps & InputHTMLAttributes<HTMLInputElement>
>(function CheckboxField({ label, error, ...props }, ref) {
  return (
    <label className="flex items-center gap-3 rounded-md border border-border bg-background px-3 py-2 text-sm">
      <input ref={ref} type="checkbox" className="size-4 accent-primary" {...props} />
      <span className="font-medium text-foreground">{label}</span>
      {error ? <span className="ml-auto text-xs text-danger">{error}</span> : null}
    </label>
  );
});
