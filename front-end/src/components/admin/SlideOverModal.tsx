"use client";

import { AnimatePresence, motion } from "framer-motion";
import { X } from "lucide-react";
import { type ReactNode } from "react";

interface SlideOverModalProps {
  isOpen: boolean;
  title: string;
  children: ReactNode;
  onClose: () => void;
  isBusy?: boolean;
}

export function SlideOverModal({
  isOpen,
  title,
  children,
  onClose,
  isBusy = false,
}: SlideOverModalProps) {
  const handleClose = (): void => {
    if (isBusy) {
      return;
    }

    onClose();
  };

  return (
    <AnimatePresence>
      {isOpen ? (
        <>
          <motion.button
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            type="button"
            aria-label="Close modal"
            onClick={handleClose}
            className="fixed inset-0 z-40 bg-black/70"
          />

          <motion.aside
            initial={{ x: 380, opacity: 0 }}
            animate={{ x: 0, opacity: 1 }}
            exit={{ x: 380, opacity: 0 }}
            transition={{ duration: 0.25, ease: "easeOut" }}
            className="fixed inset-y-0 right-0 z-50 w-full max-w-xl border-l border-violet-400/20 bg-slate-950/95 p-6 shadow-2xl backdrop-blur-md"
          >
            <div className="mb-5 flex items-center justify-between">
              <h3 className="text-lg font-semibold text-white">{title}</h3>
              <button
                type="button"
                onClick={handleClose}
                className="rounded-lg border border-slate-700 bg-slate-900/70 p-2 text-slate-300 transition hover:text-white"
              >
                <X className="size-4" />
              </button>
            </div>

            {children}
          </motion.aside>
        </>
      ) : null}
    </AnimatePresence>
  );
}
