import { api } from "@/lib/api/client";
import { apiEndpoints } from "@/lib/api/endpoints";
import { clearStoredAuthToken, setStoredAuthToken } from "@/lib/api/auth";

type LoginPayload = {
  email: string;
  password: string;
};

type LoginResponse = {
  token: string;
};

export async function login(payload: LoginPayload): Promise<LoginResponse> {
  const response = await api.post<LoginResponse>(
    apiEndpoints.admin.login,
    payload,
    { auth: false },
  );

  setStoredAuthToken(response.token);

  return response;
}

export async function logout(): Promise<void> {
  try {
    await api.post<[]>(apiEndpoints.admin.logout);
  } finally {
    clearStoredAuthToken();
  }
}
