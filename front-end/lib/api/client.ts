import axios, { type AxiosError, type AxiosResponse } from "axios";

export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
}

const apiBaseURL =
  process.env.NEXT_PUBLIC_API_BASE_URL ?? "http://localhost:8000/api/v1";

export const apiClient = axios.create({
  baseURL: apiBaseURL,
  withCredentials: true,
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
  },
});

apiClient.interceptors.request.use((config) => {
  if (typeof window === "undefined") {
    return config;
  }

  const token = window.localStorage.getItem("auth_token");

  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
});

apiClient.interceptors.response.use(
  (response: AxiosResponse) => response,
  (error: AxiosError) => Promise.reject(error),
);
