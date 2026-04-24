"use client";

import { AnimatePresence, motion } from "framer-motion";
import { Loader2, Trash2 } from "lucide-react";

interface ConfirmDialogProps {
  isOpen: boolean;
  title: string;
  description: string;
  confirmLabel: string;
  onClose: () => void;
  onConfirm: () => void;
  isLoading?: boolean;
}

export function ConfirmDialog({
  isOpen,
  title,
  description,
  confirmLabel,
  onClose,
  onConfirm,
  isLoading = false,
}: ConfirmDialogProps) {
  return (
    <AnimatePresence>
      {isOpen ? (
        <>
          <motion.button
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            type="button"
            aria-label="Close confirmation dialog"
            onClick={onClose}
            className="fixed inset-0 z-50 bg-black/70"
          />

          <motion.div
            initial={{ opacity: 0, y: 12 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: 12 }}
            className="fixed inset-0 z-[60] flex items-center justify-center p-4"
          >
            <div className="w-full max-w-md rounded-2xl border border-slate-700 bg-slate-900/90 p-5 shadow-[0_0_35px_rgba(139,92,246,0.2)] backdrop-blur-md">
              <h4 className="text-base font-semibold text-white">{title}</h4>
              <p className="mt-2 text-sm text-slate-300">{description}</p>

              <div className="mt-5 flex justify-end gap-2">
                <button
                  type="button"
                  onClick={onClose}
                  disabled={isLoading}
                  className="rounded-lg border border-slate-700 bg-slate-800/70 px-3 py-2 text-sm text-slate-200 disabled:opacity-70"
                >
                  Cancel
                </button>

                <button
                  type="button"
                  onClick={onConfirm}
                  disabled={isLoading}
                  className="inline-flex items-center gap-2 rounded-lg bg-rose-500 px-3 py-2 text-sm font-medium text-white disabled:opacity-70"
                >
                  {isLoading ? <Loader2 className="size-4 animate-spin" /> : <Trash2 className="size-4" />}
                  {confirmLabel}
                </button>
              </div>
            </div>
          </motion.div>
        </>
      ) : null}
    </AnimatePresence>
  );
}
