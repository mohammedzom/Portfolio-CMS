"use client";

import { AxiosError } from "axios";
import { useRouter } from "next/navigation";
import {
  createContext,
  type ReactNode,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
} from "react";

import { apiClient, type ApiResponse, setUnauthorizedHandler } from "@/lib/axios";

export interface User {
  id?: number;
  name?: string;
  email: string;
}

interface LoginCredentials {
  email: string;
  password: string;
}

interface LoginResponseData {
  token?: string;
  access_token?: string;
  user?: User;
}

interface LogoutOptions {
  callApi?: boolean;
  redirectTo?: string;
}

interface LoginOptions {
  redirectTo?: string;
}

interface AuthContextValue {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  login: (credentials: LoginCredentials, options?: LoginOptions) => Promise<void>;
  logout: (options?: LogoutOptions) => Promise<void>;
}

const AuthContext = createContext<AuthContextValue | undefined>(undefined);

const USER_STORAGE_KEY = "auth_user";
const TOKEN_STORAGE_KEY = "auth_token";

export function AuthProvider({ children }: { children: ReactNode }) {
  const router = useRouter();

  const [user, setUser] = useState<User | null>(null);
  const [token, setToken] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState<boolean>(true);

  const clearAuthState = useCallback((): void => {
    window.localStorage.removeItem(TOKEN_STORAGE_KEY);
    window.localStorage.removeItem(USER_STORAGE_KEY);
    setToken(null);
    setUser(null);
  }, []);

  const logout = useCallback(
    async (options?: LogoutOptions): Promise<void> => {
      const shouldCallApi = options?.callApi ?? true;
      const redirectTo = options?.redirectTo ?? "/login";

      try {
        if (shouldCallApi) {
          await apiClient.post("/logout");
        }
      } catch (error) {
        const axiosError = error as AxiosError;
        if (axiosError.response?.status !== 401) {
          console.error(axiosError);
        }
      } finally {
        clearAuthState();
        router.replace(redirectTo);
      }
    },
    [clearAuthState, router],
  );

  const login = useCallback(
    async (credentials: LoginCredentials, options?: LoginOptions): Promise<void> => {
      const redirectTo = options?.redirectTo ?? "/dashboard";
      const response = await apiClient.post<ApiResponse<LoginResponseData>>("/login", credentials);

      const tokenValue =
        response.data.data?.token ??
        response.data.data?.access_token ??
        (response.data as unknown as LoginResponseData)?.token ??
        (response.data as unknown as LoginResponseData)?.access_token;

      if (!tokenValue) {
        throw new Error("No token was returned by the authentication endpoint.");
      }

      const userData = response.data.data?.user ?? { email: credentials.email };

      window.localStorage.setItem(TOKEN_STORAGE_KEY, tokenValue);
      window.localStorage.setItem(USER_STORAGE_KEY, JSON.stringify(userData));

      setToken(tokenValue);
      setUser(userData);

      router.replace(redirectTo);
    },
    [router],
  );

  useEffect(() => {
    const storedToken = window.localStorage.getItem(TOKEN_STORAGE_KEY);
    const storedUser = window.localStorage.getItem(USER_STORAGE_KEY);

    if (storedToken) {
      setToken(storedToken);
    }

    if (storedUser) {
      try {
        const parsedUser = JSON.parse(storedUser) as User;
        setUser(parsedUser);
      } catch {
        window.localStorage.removeItem(USER_STORAGE_KEY);
      }
    }

    setIsLoading(false);
  }, []);

  useEffect(() => {
    setUnauthorizedHandler(() => logout({ callApi: false, redirectTo: "/login" }));

    return () => {
      setUnauthorizedHandler(null);
    };
  }, [logout]);

  const value = useMemo<AuthContextValue>(
    () => ({
      user,
      token,
      isAuthenticated: Boolean(token),
      isLoading,
      login,
      logout,
    }),
    [isLoading, login, logout, token, user],
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth(): AuthContextValue {
  const context = useContext(AuthContext);

  if (!context) {
    throw new Error("useAuth must be used within an AuthProvider.");
  }

  return context;
}
