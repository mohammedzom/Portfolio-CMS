"use client";

import { Loader2, MailOpen, RotateCcw, Search, Trash2 } from "lucide-react";
import { useCallback, useEffect, useState } from "react";

import { Button } from "@/components/ui/button";
import { EmptyState } from "@/components/ui/empty-state";
import { messageService } from "@/services/admin-services";
import type { Message, PaginatedMessages } from "@/types/api";

export function MessagesView() {
  const [data, setData] = useState<PaginatedMessages | null>(null);
  const [selected, setSelected] = useState<Message | null>(null);
  const [archived, setArchived] = useState(false);
  const [search, setSearch] = useState("");
  const [error, setError] = useState("");

  const load = useCallback(async () => {
    try {
      setError("");
      setData(
        await messageService.list({
          ...(archived ? { archived: true } : {}),
          ...(search ? { search } : {}),
        }),
      );
    } catch (caughtError) {
      setError(caughtError instanceof Error ? caughtError.message : "Unable to load messages.");
    }
  }, [archived, search]);

  useEffect(() => {
    queueMicrotask(() => void load());
  }, [load]);

  const openMessage = async (message: Message) => {
    const fullMessage = await messageService.get(message.id);
    setSelected(fullMessage);
    await load();
  };

  return (
    <section className="grid gap-6">
      <div>
        <p className="text-sm font-medium uppercase tracking-wide text-primary">Admin</p>
        <h1 className="mt-2 text-3xl font-semibold">Messages</h1>
        <p className="mt-2 text-sm text-muted-foreground">
          Review contact submissions, mark read state, and archive messages.
        </p>
      </div>

      <div className="rounded-lg border border-border bg-surface">
        <div className="flex flex-col gap-3 border-b border-border p-4 md:flex-row md:items-center md:justify-between">
          <label className="relative block w-full md:max-w-sm">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground" size={16} />
            <input
              value={search}
              onChange={(event) => setSearch(event.target.value)}
              placeholder="Search messages"
              className="min-h-10 w-full rounded-md border border-border bg-background pl-9 pr-3 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
            />
          </label>
          <Button variant={archived ? "primary" : "secondary"} onClick={() => setArchived((value) => !value)}>
            {archived ? "Viewing archived" : "View archived"}
          </Button>
        </div>

        {error ? (
          <div className="p-4 text-sm text-danger">{error}</div>
        ) : !data ? (
          <div className="flex items-center justify-center gap-2 p-12 text-sm text-muted-foreground">
            <Loader2 className="animate-spin" size={16} />
            Loading
          </div>
        ) : data.messages.length === 0 ? (
          <div className="p-4">
            <EmptyState message="No messages found." />
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full min-w-[760px] text-left">
              <thead>
                <tr className="text-xs uppercase tracking-wide text-muted-foreground">
                  <th className="px-4 py-3">Sender</th>
                  <th className="px-4 py-3">Subject</th>
                  <th className="px-4 py-3">Status</th>
                  <th className="px-4 py-3 text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                {data.messages.map((message) => (
                  <tr key={message.id} className="border-t border-border">
                    <td className="px-4 py-3 text-sm">
                      <div className="font-medium">{message.name}</div>
                      <div className="text-muted-foreground">{message.email}</div>
                    </td>
                    <td className="px-4 py-3 text-sm">{message.subject}</td>
                    <td className="px-4 py-3 text-sm">{message.is_read ? "Read" : "Unread"}</td>
                    <td className="px-4 py-3">
                      <div className="flex justify-end gap-2">
                        <Button variant="secondary" className="size-9 p-0" onClick={() => openMessage(message)}>
                          <MailOpen size={16} />
                        </Button>
                        {archived ? (
                          <Button
                            variant="secondary"
                            className="size-9 p-0"
                            onClick={async () => {
                              await messageService.restore(message.id);
                              await load();
                            }}
                          >
                            <RotateCcw size={16} />
                          </Button>
                        ) : (
                          <Button
                            variant="danger"
                            className="size-9 p-0"
                            onClick={async () => {
                              await messageService.archive(message.id);
                              await load();
                            }}
                          >
                            <Trash2 size={16} />
                          </Button>
                        )}
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {selected ? (
        <div className="rounded-lg border border-border bg-surface p-5">
          <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
            <div>
              <h2 className="text-xl font-semibold">{selected.subject}</h2>
              <p className="mt-1 text-sm text-muted-foreground">
                {selected.name} - {selected.email}
              </p>
            </div>
            <div className="flex gap-2">
              <Button
                variant="secondary"
                onClick={async () => {
                  if (selected.is_read) {
                    await messageService.markUnread(selected.id);
                  } else {
                    await messageService.markRead(selected.id);
                  }
                  setSelected(await messageService.get(selected.id));
                  await load();
                }}
              >
                {selected.is_read ? "Mark unread" : "Mark read"}
              </Button>
              <Button variant="ghost" onClick={() => setSelected(null)}>
                Close
              </Button>
            </div>
          </div>
          <p className="mt-5 whitespace-pre-wrap text-sm leading-7">{selected.body}</p>
        </div>
      ) : null}
    </section>
  );
}
