"use client";

import { AxiosError } from "axios";
import { AnimatePresence, motion } from "framer-motion";
import { CheckCheck, Loader2, Mail, Trash2, X } from "lucide-react";
import { useEffect, useState } from "react";

import { axiosClient, type ApiResponse } from "@/lib/axios";
import { ConfirmDialog } from "@/src/components/admin/ConfirmDialog";

interface MessageItem {
  id: number;
  name: string;
  email: string;
  subject: string;
  body: string;
  is_read: boolean;
  created_at?: string;
}

interface MessagesCollection {
  data?: MessageItem[];
  messages?: MessageItem[];
}

function extractMessages(payload: unknown): MessageItem[] {
  if (Array.isArray(payload)) {
    return payload as MessageItem[];
  }

  if (payload && typeof payload === "object") {
    const normalized = payload as MessagesCollection;

    if (Array.isArray(normalized.data)) {
      return normalized.data;
    }

    if (Array.isArray(normalized.messages)) {
      return normalized.messages;
    }
  }

  return [];
}

export default function MessagesPage() {
  const [messages, setMessages] = useState<MessageItem[]>([]);
  const [isLoading, setIsLoading] = useState<boolean>(true);
  const [errorMessage, setErrorMessage] = useState<string>("");

  const [selectedMessage, setSelectedMessage] = useState<MessageItem | null>(null);
  const [messageToDelete, setMessageToDelete] = useState<MessageItem | null>(null);
  const [isDeleting, setIsDeleting] = useState<boolean>(false);
  const [isMarking, setIsMarking] = useState<boolean>(false);

  const fetchMessages = async (): Promise<void> => {
    setIsLoading(true);
    setErrorMessage("");

    try {
      const response = await axiosClient.get<ApiResponse<unknown>>("/admin/messages");
      setMessages(extractMessages(response.data.data));
    } catch (error) {
      const axiosError = error as AxiosError<ApiResponse<null>>;
      setErrorMessage(axiosError.response?.data?.message ?? "Failed to load messages.");
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    void fetchMessages();
  }, []);

  const openMessage = (message: MessageItem): void => {
    setSelectedMessage(message);
  };

  const closeMessageModal = (): void => {
    if (isMarking) {
      return;
    }

    setSelectedMessage(null);
  };

  const handleMarkAsRead = async (): Promise<void> => {
    if (!selectedMessage || selectedMessage.is_read) {
      return;
    }

    setIsMarking(true);

    try {
      await axiosClient.patch(`/admin/messages/${selectedMessage.id}`, { is_read: true });
      await fetchMessages();
      setSelectedMessage((previous) => (previous ? { ...previous, is_read: true } : previous));
    } catch (error) {
      const axiosError = error as AxiosError<ApiResponse<null>>;
      setErrorMessage(axiosError.response?.data?.message ?? "Failed to mark message as read.");
    } finally {
      setIsMarking(false);
    }
  };

  const handleDelete = async (): Promise<void> => {
    if (!messageToDelete) {
      return;
    }

    setIsDeleting(true);

    try {
      await axiosClient.delete(`/admin/messages/${messageToDelete.id}`);

      if (selectedMessage?.id === messageToDelete.id) {
        setSelectedMessage(null);
      }

      setMessageToDelete(null);
      await fetchMessages();
    } catch (error) {
      const axiosError = error as AxiosError<ApiResponse<null>>;
      setErrorMessage(axiosError.response?.data?.message ?? "Failed to delete message.");
    } finally {
      setIsDeleting(false);
    }
  };

  return (
    <section className="space-y-6">
      <div>
        <h2 className="text-xl font-semibold text-white">Messages Inbox</h2>
        <p className="mt-1 text-sm text-slate-300">Review and manage incoming contact messages.</p>
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
                <span>Loading messages...</span>
              </div>
            </li>
          ) : null}

          {!isLoading && messages.length === 0 ? (
            <li className="px-4 py-12 text-center text-slate-300">No messages available.</li>
          ) : null}

          {!isLoading
            ? messages.map((message) => (
                <li
                  key={message.id}
                  className="cursor-pointer px-4 py-4 transition hover:bg-slate-800/30"
                  onClick={() => {
                    openMessage(message);
                  }}
                >
                  <div className="flex items-start justify-between gap-3">
                    <div className="min-w-0">
                      <div className="mb-1 flex items-center gap-2">
                        {!message.is_read ? (
                          <span className="h-2.5 w-2.5 rounded-full bg-indigo-400 shadow-[0_0_12px_rgba(129,140,248,0.95)]" />
                        ) : null}
                        <p className="truncate text-sm font-medium text-white">{message.subject}</p>
                      </div>
                      <p className="text-xs text-slate-400">
                        {message.name} • {message.email}
                      </p>
                      <p className="mt-1 line-clamp-1 text-sm text-slate-300">{message.body}</p>
                    </div>

                    <button
                      type="button"
                      onClick={(event) => {
                        event.stopPropagation();
                        setMessageToDelete(message);
                      }}
                      className="inline-flex items-center gap-1 rounded-lg border border-rose-500/35 bg-rose-500/10 px-3 py-1.5 text-xs text-rose-200 transition hover:bg-rose-500/20"
                    >
                      <Trash2 className="size-3.5" />
                      Delete
                    </button>
                  </div>
                </li>
              ))
            : null}
        </ul>
      </div>

      <AnimatePresence>
        {selectedMessage ? (
          <>
            <motion.button
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              type="button"
              aria-label="Close message modal"
              onClick={closeMessageModal}
              className="fixed inset-0 z-40 bg-black/70"
            />

            <motion.div
              initial={{ opacity: 0, y: 18, scale: 0.98 }}
              animate={{ opacity: 1, y: 0, scale: 1 }}
              exit={{ opacity: 0, y: 18, scale: 0.98 }}
              className="fixed inset-0 z-50 flex items-center justify-center p-4"
            >
              <div className="w-full max-w-2xl rounded-2xl border border-violet-400/20 bg-slate-950/95 p-5 shadow-[0_0_45px_rgba(99,102,241,0.2)] backdrop-blur-md">
                <div className="mb-4 flex items-start justify-between gap-3">
                  <div>
                    <p className="text-base font-semibold text-white">{selectedMessage.subject}</p>
                    <p className="mt-1 text-sm text-slate-300">
                      {selectedMessage.name} • {selectedMessage.email}
                    </p>
                  </div>

                  <button
                    type="button"
                    onClick={closeMessageModal}
                    className="rounded-lg border border-slate-700 bg-slate-900/70 p-2 text-slate-300 transition hover:text-white"
                  >
                    <X className="size-4" />
                  </button>
                </div>

                <div className="rounded-xl border border-slate-800 bg-slate-900/60 p-4 text-sm leading-6 text-slate-200">
                  {selectedMessage.body}
                </div>

                <div className="mt-5 flex flex-wrap justify-end gap-2">
                  <button
                    type="button"
                    onClick={() => {
                      setMessageToDelete(selectedMessage);
                    }}
                    className="inline-flex items-center gap-2 rounded-lg border border-rose-500/35 bg-rose-500/10 px-3 py-2 text-sm text-rose-200 transition hover:bg-rose-500/20"
                  >
                    <Trash2 className="size-4" />
                    Delete
                  </button>

                  <button
                    type="button"
                    onClick={handleMarkAsRead}
                    disabled={isMarking || selectedMessage.is_read}
                    className="inline-flex items-center gap-2 rounded-lg bg-indigo-500 px-3 py-2 text-sm font-medium text-white transition hover:bg-indigo-400 disabled:cursor-not-allowed disabled:opacity-60"
                  >
                    {isMarking ? <Loader2 className="size-4 animate-spin" /> : <CheckCheck className="size-4" />}
                    {selectedMessage.is_read ? "Already Read" : "Mark as Read"}
                  </button>
                </div>
              </div>
            </motion.div>
          </>
        ) : null}
      </AnimatePresence>

      <ConfirmDialog
        isOpen={Boolean(messageToDelete)}
        title="Delete message?"
        description={
          messageToDelete
            ? `This will permanently remove the message from ${messageToDelete.name}.`
            : "This will permanently remove this message."
        }
        confirmLabel="Delete"
        onClose={() => {
          if (!isDeleting) {
            setMessageToDelete(null);
          }
        }}
        onConfirm={handleDelete}
        isLoading={isDeleting}
      />
    </section>
  );
}
