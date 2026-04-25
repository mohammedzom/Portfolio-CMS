"use client";

import { zodResolver } from "@hookform/resolvers/zod";
import { Send } from "lucide-react";
import { useState } from "react";
import { type Resolver, useForm } from "react-hook-form";
import type { z } from "zod";

import { Button } from "@/components/ui/button";
import { TextAreaField, TextField } from "@/components/ui/field";
import { applyBackendValidationErrors } from "@/lib/forms/backend-errors";
import { sendMessage } from "@/services/portfolio-service";
import { contactMessageSchema } from "@/features/admin/schemas";

type ContactValues = z.infer<typeof contactMessageSchema>;

export function ContactForm() {
  const [message, setMessage] = useState("");
  const {
    register,
    handleSubmit,
    reset,
    setError,
    formState: { errors, isSubmitting },
  } = useForm<ContactValues>({
    resolver: zodResolver(contactMessageSchema) as Resolver<ContactValues>,
    defaultValues: { name: "", email: "", subject: "", body: "" },
  });

  const onSubmit = async (values: ContactValues) => {
    setMessage("");
    try {
      await sendMessage(values);
      reset();
      setMessage("Message transmitted.");
    } catch (error) {
      setMessage(applyBackendValidationErrors(error, setError));
    }
  };

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="grid gap-4">
      {message ? <div className="text-sm text-cyan-200">{message}</div> : null}
      <div className="grid gap-4 md:grid-cols-2">
        <TextField label="Name" error={errors.name?.message} {...register("name")} />
        <TextField label="Email" type="email" error={errors.email?.message} {...register("email")} />
      </div>
      <TextField label="Subject" error={errors.subject?.message} {...register("subject")} />
      <TextAreaField label="Body" error={errors.body?.message} {...register("body")} />
      <Button type="submit" disabled={isSubmitting} className="justify-self-start">
        <Send size={16} />
        Send
      </Button>
    </form>
  );
}
