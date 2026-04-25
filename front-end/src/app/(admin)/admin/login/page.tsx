import { LoginForm } from "@/features/admin/login-form";

export default function AdminLoginPage() {
  return (
    <div className="flex min-h-dvh items-center justify-center bg-[radial-gradient(circle_at_top,#164e63_0,#09090b_38%,#020617_100%)] px-4 py-12">
      <LoginForm />
    </div>
  );
}
