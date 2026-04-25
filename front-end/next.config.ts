import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  allowedDevOrigins: ["127.0.0.1", "localhost", "10.0.0.45"],
  turbopack: {
    root: __dirname,
  },
};

export default nextConfig;
