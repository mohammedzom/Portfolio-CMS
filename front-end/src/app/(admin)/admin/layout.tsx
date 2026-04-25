import { AdminShell } from "@/components/admin/admin-shell";
import { AuthGuard } from "@/components/admin/auth-guard";
import { AuthProvider } from "@/context/auth-context";

export default function AdminLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <AuthProvider>
      <AuthGuard>
        <AdminShell>{children}</AdminShell>
      </AuthGuard>
    </AuthProvider>
  );
}
